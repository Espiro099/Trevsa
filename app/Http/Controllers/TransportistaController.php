<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transportista;

class TransportistaController extends Controller
{
    public function __construct()
    {
        // Ensure authentication for controller routes
        $this->middleware('auth');
    }

    /**
     * Mostrar listado con paginación básica.
     */
    public function index()
    {
        // Usar paginación simple para evitar cargar todo en memoria
        $transportistas = Transportista::paginate(10);
        return view('transportistas.index', compact('transportistas'));
    }


    /**
     * Guardar nuevo transportista.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'transportista' => 'required|string|max:255',
            'nombre' => 'required|string|max:255',
            'estatus' => 'required|string',
            'telefono' => 'nullable|string|max:50',
            'cantidad_unidades_53ft' => 'nullable|integer|min:0',
            'tipo_viaje' => 'nullable|string',
            'notas' => 'nullable|string',
        ]);

        $data['created_by'] = auth()->id();
        Transportista::create($data);

        return redirect()->route('transportistas.index')->with('success', 'Transportista creado correctamente.');
    }

    /**
     * Mostrar formulario de creación.
     */
    public function create()
    {
        return view('transportistas.create');
    }

    /**
     * Mostrar formulario de edición.
     */
    public function edit($id)
    {
        $transportista = Transportista::find($id);
        return view('transportistas.edit', compact('transportista'));
    }

    /**
     * Actualizar transportista.
     */
    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'transportista' => 'required|string|max:255',
            'nombre' => 'required|string|max:255',
            'estatus' => 'required|string',
            'telefono' => 'nullable|string|max:50',
            'cantidad_unidades_53ft' => 'nullable|integer|min:0',
            'tipo_viaje' => 'nullable|string',
            'notas' => 'nullable|string',
        ]);

        $t = Transportista::find($id);
        if ($t) {
            $t->update($data);
        }

        return redirect()->route('transportistas.index')->with('success', 'Transportista actualizado.');
    }

    /**
     * Eliminar transportista.
     */
    public function destroy($id)
    {
        $t = Transportista::find($id);
        if ($t) {
            $t->delete();
        }

        return redirect()->route('transportistas.index')->with('success', 'Transportista eliminado.');
    }
}
