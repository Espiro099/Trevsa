<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProveedorRequest;
use App\Http\Requests\UpdateProveedorRequest;
use Illuminate\Http\Request;
use App\Models\Proveedor;
use Maatwebsite\Excel\Facades\Excel;

class ProveedoresController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $query = Proveedor::query();
        
        // Búsqueda por nombre
        if ($request->has('search') && $request->search) {
            $searchTerm = $request->search;
            $query->where('nombre_empresa', 'like', "%{$searchTerm}%");
        }
        
        $proveedores = $query->orderBy('created_at', 'desc')->paginate(10);
        
        return view('proveedores.index', compact('proveedores'));
    }

    public function create()
    {
        return view('proveedores.create');
    }

    public function store(StoreProveedorRequest $request)
    {
        $data = $request->validated();

        // Procesar cantidades de unidades alineadas con los tipos seleccionados
        $cantidadesFiltradas = [];
        foreach ($data['tipos_unidades'] as $tipo) {
            if (isset($data['cantidades_unidades'][$tipo]) && $data['cantidades_unidades'][$tipo] > 0) {
                $cantidadesFiltradas[$tipo] = (int) $data['cantidades_unidades'][$tipo];
            }
        }
        $data['cantidades_unidades'] = $cantidadesFiltradas;

        $data['created_by'] = $request->user()->id ?? null;
        $data['estado_prospecto'] = $data['estado_prospecto'] ?? 'pendiente';

        Proveedor::create($data);

        return redirect()->route('prospectos_proveedores.index')->with('success', 'Prospecto de proveedor registrado.');
    }

    public function edit($id)
    {
        $proveedor = Proveedor::find($id);
        return view('proveedores.edit', compact('proveedor'));
    }

    public function update(UpdateProveedorRequest $request, $id)
    {
        $data = $request->validated();

        $cantidadesFiltradas = [];
        foreach ($data['tipos_unidades'] as $tipo) {
            if (isset($data['cantidades_unidades'][$tipo]) && $data['cantidades_unidades'][$tipo] > 0) {
                $cantidadesFiltradas[$tipo] = (int) $data['cantidades_unidades'][$tipo];
            }
        }
        $data['cantidades_unidades'] = $cantidadesFiltradas;

        $proveedor = Proveedor::find($id);
        if ($proveedor) {
            $proveedor->update($data);
        }

        return redirect()->route('prospectos_proveedores.index')->with('success', 'Prospecto de proveedor actualizado correctamente.');
    }

    // Exportar tipos_unidades de todos los proveedores a xlsx
    public function exportTiposUnidades()
    {
        $proveedores = Proveedor::all();
        $tipos = [];
        foreach ($proveedores as $p) {
            $tiposUnidades = is_array($p->tipos_unidades) 
                ? implode(', ', $p->tipos_unidades) 
                : $p->tipos_unidades;
            $tipos[] = [
                'Proveedor' => $p->nombre_empresa,
                'Tipos de Unidades' => $tiposUnidades,
            ];
        }
        $filename = 'tipos_unidades_proveedores_' . date('Ymd_His') . '.xlsx';
        return Excel::download(new \App\Exports\TiposUnidadesExport($tipos), $filename);
    }
}
