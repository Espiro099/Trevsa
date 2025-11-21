<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreClienteRequest;
use App\Http\Requests\UpdateClienteRequest;
use Illuminate\Http\Request;
use App\Models\Cliente;

class ClientesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $query = Cliente::query();

        // Búsqueda general (solo nombre y ciudad)
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->where('nombre_empresa', 'like', "%{$search}%")
                  ->orWhere('nombre_contacto', 'like', "%{$search}%")
                  ->orWhere('ciudad', 'like', "%{$search}%");
            });
        }

        // Filtro por ciudad
        if ($request->filled('ciudad')) {
            $query->where('ciudad', 'like', "%{$request->get('ciudad')}%");
        }

        // Ordenamiento
        $sortField = $request->get('sort', '_id');
        $sortDirection = $request->get('direction', 'desc');
        
        // Validar campo de ordenamiento para evitar inyección
        $allowedSortFields = ['_id', 'nombre_empresa', 'ciudad', 'estado', 'created_at'];
        if (!in_array($sortField, $allowedSortFields)) {
            $sortField = '_id';
        }
        
        $allowedDirections = ['asc', 'desc'];
        if (!in_array($sortDirection, $allowedDirections)) {
            $sortDirection = 'desc';
        }
        
        $query->orderBy($sortField, $sortDirection);

        // Paginación con parámetros de búsqueda preservados
        $perPage = $request->get('per_page', 10);
        $perPage = in_array($perPage, [10, 25, 50, 100]) ? $perPage : 10;
        
        $clientes = $query->paginate($perPage)->withQueryString();

        // Obtener valores únicos para filtros
        $ciudades = Cliente::distinct('ciudad')->whereNotNull('ciudad')->pluck('ciudad');

        return view('clientes.index', compact('clientes', 'ciudades'));
    }

    public function create()
    {
        return view('clientes.create');
    }

    public function store(StoreClienteRequest $request)
    {
        try {
            $data = $request->validated();

            // Asignar usuario que crea el registro
            $data['created_by'] = $request->user()->_id ?? $request->user()->id ?? null;

            // Crear el prospecto cliente en MongoDB
            $cliente = Cliente::create($data);

            // Verificar que se haya creado correctamente
            if (!$cliente || !$cliente->_id) {
                return redirect()
                    ->back()
                    ->withInput()
                    ->withErrors(['error' => 'No se pudo crear el prospecto. Por favor, intenta nuevamente.']);
            }

            return redirect()
                ->route('clientes.index')
                ->with('success', 'Prospecto de cliente creado correctamente.');
                
        } catch (\Exception $e) {
            \Log::error('Error al crear prospecto cliente', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()
                ->back()
                ->withInput()
                ->withErrors(['error' => 'Hubo un error al guardar el prospecto. Por favor, intenta nuevamente.']);
        }
    }

    public function edit($id)
    {
        $cliente = Cliente::find($id);
        return view('clientes.edit', compact('cliente'));
    }

    public function update(UpdateClienteRequest $request, $id)
    {
        $data = $request->validated();

        $cliente = Cliente::find($id);
        if ($cliente) {
            $cliente->update($data);
        }

        return redirect()->route('clientes.index')->with('success', 'Prospecto de cliente actualizado correctamente.');
    }
}
