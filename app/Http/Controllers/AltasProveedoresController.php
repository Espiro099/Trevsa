<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Proveedor;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\AltasProveedoresExport;
use App\Http\Requests\StoreAltaProveedorRequest;
use App\Services\AltaProveedorService;

class AltasProveedoresController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Mostrar listado de prospectos de proveedores para altas
     */
    public function index(Request $request)
    {
        $query = Proveedor::with('altaProveedor');
        
        // Búsqueda por nombre
        if ($request->has('search') && $request->search) {
            $searchTerm = $request->search;
            $query->where('nombre_empresa', 'like', "%{$searchTerm}%");
        }
        
        // Obtener todos los proveedores (prospectos) con sus altas asociadas
        $prospectos = $query->orderBy('_id', 'desc')->paginate(10);
        
        // Obtener todos los prospectos para el select del modal (sin paginación)
        $allProspectos = Proveedor::orderBy('nombre_empresa', 'asc')->get(['_id', 'nombre_empresa']);
        
        // Obtener usuarios para evitar consultas N+1
        $userIds = $prospectos->pluck('created_by')->filter()->unique()->toArray();
        $users = \App\Models\User::whereIn('_id', $userIds)->get()->keyBy('_id');
        
        return view('altas_proveedores.index', compact('prospectos', 'allProspectos', 'users'));
    }

    /**
     * Mostrar detalles de un prospecto y su alta
     */
    public function show($id)
    {
        $prospecto = Proveedor::findOrFail($id);
        $alta = $prospecto->altaProveedor;
        
        // Obtener usuario asociado si existe
        $usuario = null;
        if ($alta && $alta->user_id) {
            $usuario = \App\Models\User::find($alta->user_id);
        }
        
        return view('altas_proveedores.show', compact('prospecto', 'alta', 'usuario'));
    }

    /**
     * Mostrar formulario para crear/editar alta desde un prospecto
     */
    public function create($prospectoId)
    {
        $prospecto = Proveedor::findOrFail($prospectoId);
        $alta = $prospecto->altaProveedor;
        
        return view('altas_proveedores.create', compact('prospecto', 'alta'));
    }

    /**
     * Editar alta de un prospecto
     */
    public function edit($prospectoId)
    {
        $prospecto = Proveedor::findOrFail($prospectoId);
        $alta = $prospecto->altaProveedor;
        
        if (!$alta) {
            return redirect()->route('altas_proveedores.create', $prospectoId)
                ->with('info', 'Primero debe crear el registro de alta para este prospecto.');
        }
        
        return view('altas_proveedores.edit', compact('prospecto', 'alta'));
    }

    /**
     * Guardar o actualizar alta de proveedor
     */
    public function store(StoreAltaProveedorRequest $request, AltaProveedorService $service, $prospectoId)
    {
        $prospecto = Proveedor::findOrFail($prospectoId);
        
        try {
            $resultado = $service->guardarAlta($prospecto, $request);

            return redirect()
                ->route('altas_proveedores.show', $prospectoId)
                ->with('success', $resultado['mensaje']);
        } catch (\Throwable $e) {
            Log::error('Error al guardar alta proveedor', [
                'prospecto_id' => $prospectoId,
                'exception' => $e->getMessage(),
            ]);

            report($e);

            return redirect()
                ->back()
                ->withInput()
                ->withErrors([
                    'error' => 'Hubo un error al guardar el registro. Por favor, intenta nuevamente. Error: ' . $e->getMessage(),
                ]);
        }
    }

    /**
     * Actualizar alta de proveedor
     */
    public function update(StoreAltaProveedorRequest $request, AltaProveedorService $service, $prospectoId)
    {
        return $this->store($request, $service, $prospectoId);
    }

    /**
     * Dar de alta a un proveedor (cambiar status a 'alta')
     * Crea automáticamente un usuario con rol 'transportista' si no existe
     */
    public function darAlta($prospectoId)
    {
        $prospecto = Proveedor::findOrFail($prospectoId);
        $alta = $prospecto->altaProveedor;
        
        if (!$alta) {
            return redirect()->route('altas_proveedores.show', $prospectoId)
                ->withErrors(['error' => 'Este prospecto no tiene un registro de alta. Debe completar los datos primero.']);
        }

        // Validar que todos los documentos requeridos estén presentes
        $documentosFaltantes = $alta->validarDocumentosRequeridos();
        
        if (!empty($documentosFaltantes)) {
            $mensaje = 'No se puede dar de alta al proveedor. Faltan los siguientes documentos requeridos: ' . implode(', ', $documentosFaltantes);
            
            return redirect()->route('altas_proveedores.show', $prospectoId)
                ->withErrors(['error' => $mensaje])
                ->with('documentos_faltantes', $documentosFaltantes);
        }
        
        // Si no tiene usuario asociado, crear uno automáticamente
        if (!$alta->user_id) {
            $user = $this->crearUsuarioTransportista($prospecto, $alta);
            $alta->user_id = $user->_id;
        }
        
        $alta->status = 'alta';
        $alta->save();
        
        // Actualizar estado del prospecto
        $prospecto->estado_prospecto = 'alta';
        $prospecto->save();
        
        $mensaje = 'Proveedor dado de alta correctamente.';
        
        // Si se creó un usuario, guardar credenciales en sesión para mostrarlas en un modal
        if (isset($user)) {
            $tempPassword = session('temp_password');
            
            // Guardar credenciales en sesión para mostrarlas en la vista
            session([
                'nuevas_credenciales' => [
                    'email' => $user->email,
                    'password' => $tempPassword,
                    'nombre_empresa' => $prospecto->nombre_empresa,
                ]
            ]);
            
            $mensaje .= ' Se ha creado un usuario automáticamente. Las credenciales se muestran a continuación.';
        }
        
        return redirect()->route('altas_proveedores.show', $prospectoId)
            ->with('success', $mensaje);
    }

    /**
     * Crear usuario automáticamente para transportista
     */
    private function crearUsuarioTransportista(Proveedor $prospecto, $alta)
    {
        // Intentar usar el email del proveedor si existe y es válido
        $email = null;
        if (!empty($prospecto->email) && filter_var($prospecto->email, FILTER_VALIDATE_EMAIL)) {
            // Verificar si el email ya está en uso
            if (!\App\Models\User::where('email', $prospecto->email)->exists()) {
                $email = $prospecto->email;
            }
        }
        
        // Si no hay email válido, generar uno automáticamente
        if (!$email) {
            $emailBase = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $prospecto->nombre_empresa));
            $email = $emailBase . '@trevsa.transportista';
            
            // Verificar si el email ya existe, si es así, agregar un número
            $counter = 1;
            while (\App\Models\User::where('email', $email)->exists()) {
                $email = $emailBase . $counter . '@trevsa.transportista';
                $counter++;
            }
        }
        
        // Generar contraseña temporal aleatoria (12 caracteres: letras mayúsculas, minúsculas y números)
        $tempPassword = \Illuminate\Support\Str::random(12);
        
        // Crear usuario
        $user = \App\Models\User::create([
            'name' => $prospecto->nombre_empresa,
            'email' => $email,
            'password' => \Illuminate\Support\Facades\Hash::make($tempPassword),
            'role' => 'transportista', // Rol legacy
            'roles' => ['transportista'], // Nuevo sistema de roles
        ]);
        
        // Guardar contraseña temporal en sesión para mostrarla al admin
        session(['temp_password' => $tempPassword]);
        
        return $user;
    }

    /**
     * Exportar todas las altas a Excel
     */
    public function exportAll()
    {
        $prospectos = Proveedor::with('altaProveedor')
            ->orderBy('created_at', 'desc')
            ->get();
        
        $filename = 'altas_proveedores_todos_' . date('Ymd_His') . '.xlsx';
        return Excel::download(new AltasProveedoresExport($prospectos), $filename);
    }

    /**
     * Exportar una alta específica a Excel
     */
    public function exportSpecific($prospectoId)
    {
        $prospecto = Proveedor::with('altaProveedor')->findOrFail($prospectoId);
        
        if (!$prospecto->altaProveedor) {
            return redirect()->route('altas_proveedores.index')
                ->withErrors(['error' => 'Este prospecto no tiene un registro de alta.']);
        }
        
        $filename = 'alta_proveedor_' . str_replace(' ', '_', $prospecto->nombre_empresa ?? 'prospecto') . '_' . date('Ymd_His') . '.xlsx';
        return Excel::download(new AltasProveedoresExport(collect([$prospecto])), $filename);
    }
}


