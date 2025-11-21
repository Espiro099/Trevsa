<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUnidadRequest;
use App\Http\Requests\UpdateUnidadRequest;
use App\Models\UnidadDisponible;
use App\Models\TransporteProveedor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UnidadesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        $query = UnidadDisponible::with('transporteProveedor.proveedor');
        
        // Row-Level Security: Si es transportista, solo ver sus propias unidades
        if ($user->hasRole('transportista')) {
            $query->where('user_id', $user->_id);
        }
        
        // Búsqueda por nombre de transportista
        if ($request->has('search') && $request->search) {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('nombre_transportista', 'like', "%{$searchTerm}%")
                  ->orWhereHas('transporteProveedor.proveedor', function($q2) use ($searchTerm) {
                      $q2->where('nombre_empresa', 'like', "%{$searchTerm}%");
                  });
            });
        }
        
        $unidades = $query->orderBy('created_at', 'desc')->paginate(10);
        return view('unidades.index', compact('unidades'));
    }

    public function create()
    {
        $user = Auth::user();
        
        // Si es transportista, solo puede ver su propio proveedor
        if ($user->hasRole('transportista')) {
            $proveedoresAlta = TransporteProveedor::with('proveedor')
                ->where('status', 'alta')
                ->where('user_id', $user->_id)
                ->orderBy('created_at', 'desc')
                ->get();
        } else {
            // Admin puede ver todos los proveedores con alta
            $proveedoresAlta = TransporteProveedor::with('proveedor')
                ->where('status', 'alta')
                ->orderBy('created_at', 'desc')
                ->get();
        }
        
        return view('unidades.create', compact('proveedoresAlta'));
    }

    public function store(StoreUnidadRequest $request)
    {
        $user = Auth::user();
        $data = $request->validated();

        // Row-Level Security: Si es transportista, validar que el transporte_proveedor_id pertenezca al usuario
        if ($user->hasRole('transportista')) {
            if (isset($data['transporte_proveedor_id'])) {
                $transporteProveedor = TransporteProveedor::find($data['transporte_proveedor_id']);
                if (!$transporteProveedor || $transporteProveedor->user_id !== $user->_id) {
                    return redirect()->route('unidades.create')
                        ->withErrors(['error' => 'No tienes permisos para registrar unidades con este proveedor.'])
                        ->withInput();
                }
            }
        }

        // Normalize date
        if (!empty($data['fecha_disponible'])) {
            $data['fecha_disponible'] = \Carbon\Carbon::parse($data['fecha_disponible']);
        }

        // Procesar unidades disponibles y cantidades
        if (isset($data['unidades_disponibles']) && is_array($data['unidades_disponibles'])) {
            $unidadesFiltradas = [];
            $cantidadesFiltradas = [];
            
            foreach ($data['unidades_disponibles'] as $index => $unidad) {
                if (!empty($unidad) && $unidad !== '') {
                    // Si es "Otros", verificar si hay texto personalizado
                    if ($unidad === 'Otros' && isset($data['unidad_otro_texto'][$index]) && !empty($data['unidad_otro_texto'][$index])) {
                        $unidad = trim($data['unidad_otro_texto'][$index]);
                    }
                    
                    // Solo agregar si no es "Otros" vacío
                    if (!empty($unidad) && $unidad !== 'Otros') {
                        $unidadesFiltradas[] = $unidad;
                        // Obtener la cantidad correspondiente
                        $cantidad = isset($data['cantidades_unidades'][$index]) 
                            ? (int) $data['cantidades_unidades'][$index] 
                            : 1;
                        $cantidadesFiltradas[$unidad] = $cantidad;
                    }
                }
            }
            
            $data['unidades_disponibles'] = $unidadesFiltradas;
            $data['cantidades_unidades'] = $cantidadesFiltradas;
        }

        // Obtener nombre del transportista desde la relación
        if (isset($data['transporte_proveedor_id'])) {
            $transporteProveedor = TransporteProveedor::with('proveedor')->find($data['transporte_proveedor_id']);
            if ($transporteProveedor && $transporteProveedor->proveedor) {
                $data['nombre_transportista'] = $transporteProveedor->proveedor->nombre_empresa;
            }
        }

        // Asignar user_id para Row-Level Security
        $data['user_id'] = $user->_id;
        $data['created_by'] = Auth::id();
        $data['estatus'] = $data['estatus'] ?? 'disponible'; // Estatus por defecto
        
        UnidadDisponible::create($data);

        return redirect()->route('unidades.index')->with('success', 'Unidad disponible registrada correctamente.');
    }

    public function edit($id)
    {
        $user = Auth::user();
        $unidad = UnidadDisponible::with('transporteProveedor.proveedor')->findOrFail($id);
        
        // Row-Level Security: Si es transportista, solo puede editar sus propias unidades
        if ($user->hasRole('transportista') && $unidad->user_id !== $user->_id) {
            abort(403, 'No tienes permisos para editar esta unidad.');
        }
        
        // Si es transportista, solo puede ver su propio proveedor
        if ($user->hasRole('transportista')) {
            $proveedoresAlta = TransporteProveedor::with('proveedor')
                ->where('status', 'alta')
                ->where('user_id', $user->_id)
                ->orderBy('created_at', 'desc')
                ->get();
        } else {
            // Admin puede ver todos los proveedores con alta
            $proveedoresAlta = TransporteProveedor::with('proveedor')
                ->where('status', 'alta')
                ->orderBy('created_at', 'desc')
                ->get();
        }
        
        return view('unidades.edit', compact('unidad', 'proveedoresAlta'));
    }

    public function update(UpdateUnidadRequest $request, $id)
    {
        $user = Auth::user();
        $unidad = UnidadDisponible::findOrFail($id);
        
        // Row-Level Security: Si es transportista, solo puede editar sus propias unidades
        if ($user->hasRole('transportista') && $unidad->user_id !== $user->_id) {
            abort(403, 'No tienes permisos para editar esta unidad.');
        }
        
        $data = $request->validated();

        // Row-Level Security: Si es transportista, validar que el transporte_proveedor_id pertenezca al usuario
        if ($user->hasRole('transportista')) {
            if (isset($data['transporte_proveedor_id'])) {
                $transporteProveedor = TransporteProveedor::find($data['transporte_proveedor_id']);
                if (!$transporteProveedor || $transporteProveedor->user_id !== $user->_id) {
                    return redirect()->route('unidades.edit', $id)
                        ->withErrors(['error' => 'No tienes permisos para usar este proveedor.'])
                        ->withInput();
                }
            }
        }

        if (!empty($data['fecha_disponible'])) {
            $data['fecha_disponible'] = \Carbon\Carbon::parse($data['fecha_disponible']);
        }

        // Procesar unidades disponibles y cantidades
        if (isset($data['unidades_disponibles']) && is_array($data['unidades_disponibles'])) {
            $unidadesFiltradas = [];
            $cantidadesFiltradas = [];
            
            foreach ($data['unidades_disponibles'] as $index => $unidad) {
                if (!empty($unidad) && $unidad !== '') {
                    // Si es "Otros", verificar si hay texto personalizado
                    if ($unidad === 'Otros' && isset($data['unidad_otro_texto'][$index]) && !empty($data['unidad_otro_texto'][$index])) {
                        $unidad = trim($data['unidad_otro_texto'][$index]);
                    }
                    
                    // Solo agregar si no es "Otros" vacío
                    if (!empty($unidad) && $unidad !== 'Otros') {
                        $unidadesFiltradas[] = $unidad;
                        // Obtener la cantidad correspondiente
                        $cantidad = isset($data['cantidades_unidades'][$index]) 
                            ? (int) $data['cantidades_unidades'][$index] 
                            : 1;
                        $cantidadesFiltradas[$unidad] = $cantidad;
                    }
                }
            }
            
            $data['unidades_disponibles'] = $unidadesFiltradas;
            $data['cantidades_unidades'] = $cantidadesFiltradas;
        }

        // Obtener nombre del transportista desde la relación
        if (isset($data['transporte_proveedor_id'])) {
            $transporteProveedor = TransporteProveedor::with('proveedor')->find($data['transporte_proveedor_id']);
            if ($transporteProveedor && $transporteProveedor->proveedor) {
                $data['nombre_transportista'] = $transporteProveedor->proveedor->nombre_empresa;
            }
        }

        $unidad->update($data);

        return redirect()->route('unidades.index')->with('success', 'Unidad disponible actualizada correctamente.');
    }

    /**
     * Buscar proveedores con alta para autocompletado
     */
    public function buscarProveedores(Request $request)
    {
        $user = Auth::user();
        $term = $request->get('term', '');
        
        $query = TransporteProveedor::with('proveedor')
            ->where('status', 'alta');
        
        // Row-Level Security: Si es transportista, solo puede ver su propio proveedor
        if ($user->hasRole('transportista')) {
            $query->where('user_id', $user->_id);
        }
        
        if (!empty($term)) {
            $query->whereHas('proveedor', function($q) use ($term) {
                $q->where('nombre_empresa', 'like', "%{$term}%");
            });
        }
        
        $proveedores = $query->orderBy('created_at', 'desc')
            ->limit(20)
            ->get()
            ->map(function($tp) {
                return [
                    'id' => (string)$tp->_id,
                    'text' => $tp->proveedor->nombre_empresa ?? 'N/A',
                    'nombre_empresa' => $tp->proveedor->nombre_empresa ?? 'N/A',
                ];
            });

        return response()->json($proveedores);
    }
}
