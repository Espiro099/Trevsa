<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreServicioRequest;
use App\Http\Requests\UpdateServicioRequest;
use Illuminate\Http\Request;
use App\Models\Servicio;
use App\Models\Cliente;
use App\Models\Proveedor;

class RegistroSolicitudesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $query = Servicio::query();

        // Búsqueda general
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->where('cliente_nombre', 'like', "%{$search}%")
                  ->orWhere('proveedor_nombre', 'like', "%{$search}%")
                  ->orWhere('origen', 'like', "%{$search}%")
                  ->orWhere('destino', 'like', "%{$search}%")
                  ->orWhere('tipo_transporte', 'like', "%{$search}%")
                  ->orWhere('tipo_carga', 'like', "%{$search}%");
            });
        }

        // Filtro por estado
        if ($request->filled('estado')) {
            $query->where('estado', $request->get('estado'));
        }

        // Filtro por tipo de transporte
        if ($request->filled('tipo_transporte')) {
            $query->where('tipo_transporte', $request->get('tipo_transporte'));
        }

        // Filtro por rango de fechas
        if ($request->filled('fecha_desde')) {
            $query->where('fecha_servicio', '>=', \Carbon\Carbon::parse($request->get('fecha_desde')));
        }
        if ($request->filled('fecha_hasta')) {
            $query->where('fecha_servicio', '<=', \Carbon\Carbon::parse($request->get('fecha_hasta')));
        }

        // Ordenamiento
        $sortField = $request->get('sort', '_id');
        $sortDirection = $request->get('direction', 'desc');
        
        $allowedSortFields = ['_id', 'cliente_nombre', 'origen', 'destino', 'fecha_servicio', 'estado', 'created_at'];
        if (!in_array($sortField, $allowedSortFields)) {
            $sortField = '_id';
        }
        
        $allowedDirections = ['asc', 'desc'];
        if (!in_array($sortDirection, $allowedDirections)) {
            $sortDirection = 'desc';
        }
        
        $query->orderBy($sortField, $sortDirection);

        // Paginación
        $perPage = $request->get('per_page', 10);
        $perPage = in_array($perPage, [10, 25, 50, 100]) ? $perPage : 10;
        
        $solicitudes = $query->paginate($perPage)->withQueryString();

        // Obtener valores únicos para filtros
        $estados = Servicio::distinct('estado')->whereNotNull('estado')->pluck('estado');
        $tiposTransporte = Servicio::distinct('tipo_transporte')->whereNotNull('tipo_transporte')->pluck('tipo_transporte');

        return view('registro.index', compact('solicitudes', 'estados', 'tiposTransporte'));
    }

    public function create()
    {
        $clientes = Cliente::all();
        $proveedores = Proveedor::all();
        return view('registro.create', compact('clientes', 'proveedores'));
    }

    public function store(StoreServicioRequest $request)
    {
        $data = $request->validated();

        // Normalize date
        if (!empty($data['fecha_servicio'])) {
            $data['fecha_servicio'] = \Carbon\Carbon::parse($data['fecha_servicio']);
        }

        // Si no se proporcionó cliente_nombre pero sí cliente_id, obtenerlo
        if (empty($data['cliente_nombre']) && !empty($data['cliente_id'])) {
            $cliente = Cliente::find($data['cliente_id']);
            if ($cliente) {
                $data['cliente_nombre'] = $cliente->nombre_empresa;
            }
        }

        // Si no se proporcionó proveedor_nombre pero sí proveedor_id, obtenerlo
        if (empty($data['proveedor_nombre']) && !empty($data['proveedor_id'])) {
            $proveedor = Proveedor::find($data['proveedor_id']);
            if ($proveedor) {
                $data['proveedor_nombre'] = $proveedor->nombre_empresa;
            }
        }

        // Agregar usuario que crea
        $data['created_by'] = $request->user()->id ?? null;
        
        // Estado por defecto
        if (empty($data['estado'])) {
            $data['estado'] = 'pendiente';
        }

        // Guardar datos anteriores para historial (vacío porque es nuevo)
        $datosAnteriores = [];

        $servicio = Servicio::create($data);

        // Guardar historial de tarifa si hay datos relevantes
        if (!empty($data['tarifa_cliente']) || !empty($data['tarifa_proveedor'])) {
            \App\Services\TarifaService::guardarHistorial(
                $servicio,
                $datosAnteriores,
                $data,
                $request->user()->id ?? null
            );
        }

        // Registrar estado inicial en el historial
        if ($servicio) {
            \App\Models\EstadoHistorial::create([
                'servicio_id' => $servicio->_id,
                'estado_anterior' => null,
                'estado_nuevo' => $data['estado'],
                'comentario' => 'Estado inicial del servicio',
                'changed_by' => $data['created_by'],
                'changed_at' => \Carbon\Carbon::now(),
            ]);
        }

        return redirect()->route('registro.index')->with('success', 'Servicio registrado correctamente.');
    }

    public function edit($id)
    {
        $solicitud = Servicio::find($id);
        $clientes = Cliente::all();
        $proveedores = Proveedor::all();
        return view('registro.edit', compact('solicitud', 'clientes', 'proveedores'));
    }

    public function update(UpdateServicioRequest $request, $id)
    {
        $data = $request->validated();

        if (!empty($data['fecha_servicio'])) {
            $data['fecha_servicio'] = \Carbon\Carbon::parse($data['fecha_servicio']);
        }

        // Si no se proporcionó cliente_nombre pero sí cliente_id, obtenerlo
        if (empty($data['cliente_nombre']) && !empty($data['cliente_id'])) {
            $cliente = Cliente::find($data['cliente_id']);
            if ($cliente) {
                $data['cliente_nombre'] = $cliente->nombre_empresa;
            }
        }

        // Si no se proporcionó proveedor_nombre pero sí proveedor_id, obtenerlo
        if (empty($data['proveedor_nombre']) && !empty($data['proveedor_id'])) {
            $proveedor = Proveedor::find($data['proveedor_id']);
            if ($proveedor) {
                $data['proveedor_nombre'] = $proveedor->nombre_empresa;
            }
        }

        // Estado por defecto
        if (empty($data['estado'])) {
            $data['estado'] = 'pendiente';
        }

        $solicitud = Servicio::find($id);
        if ($solicitud) {
            // Guardar datos anteriores para historial
            $datosAnteriores = [
                'tarifa_cliente' => $solicitud->tarifa_cliente,
                'tarifa_proveedor' => $solicitud->tarifa_proveedor,
            ];

            $solicitud->update($data);

            // Guardar historial si hubo cambios en tarifas
            \App\Services\TarifaService::guardarHistorial(
                $solicitud,
                $datosAnteriores,
                $data,
                $request->user()->id ?? null
            );
        }

        return redirect()->route('registro.index')->with('success', 'Servicio actualizado correctamente.');
    }
}
