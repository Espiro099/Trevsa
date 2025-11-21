<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Servicio;
use App\Services\EstadoService;
use Illuminate\Support\Facades\Auth;

class EstadoController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Mostrar formulario para cambiar estado
     */
    public function show($id)
    {
        $servicio = Servicio::findOrFail($id);
        $estadoActual = $servicio->estado ?? 'pendiente';
        $estadosPermitidos = EstadoService::obtenerEstadosPermitidos($estadoActual);
        $historial = EstadoService::obtenerHistorial($servicio->_id);

        return view('estado.cambiar', compact('servicio', 'estadoActual', 'estadosPermitidos', 'historial'));
    }

    /**
     * Cambiar estado de un servicio
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'nuevo_estado' => 'required|string',
            'comentario' => 'nullable|string|max:1000',
        ]);

        $servicio = Servicio::findOrFail($id);
        $userId = Auth::id();

        try {
            EstadoService::cambiarEstado(
                $servicio,
                $request->nuevo_estado,
                $request->comentario,
                $userId
            );

            return redirect()
                ->route('registro.edit', $id)
                ->with('success', 'Estado actualizado correctamente.');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withErrors(['error' => $e->getMessage()])
                ->withInput();
        }
    }
}
