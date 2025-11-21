<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tarifa;
use App\Models\PrecioDiesel;
use App\Models\TarifaHistorial;
use App\Services\DistanciaService;
use App\Services\TarifaService;
use Carbon\Carbon;

class TarifasController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Sistema de Cálculo Automático de Tarifas
     */
    public function index()
    {
        $precioDiesel = PrecioDiesel::precioActual();
        return view('tarifas.index', compact('precioDiesel'));
    }

    /**
     * Calcular distancia y tarifas
     */
    public function calcular(Request $request)
    {
        $request->validate([
            'origen' => 'required|string|max:255',
            'destino' => 'required|string|max:255',
            'tarifa_cliente' => 'required|numeric|min:0',
            'tarifa_proveedor' => 'nullable|numeric|min:0',
        ]);

        // Calcular distancia
        $distancia = DistanciaService::calcularDistancia(
            $request->origen,
            $request->destino
        );

        if (!$distancia) {
            return back()->withErrors(['error' => 'No se pudo calcular la distancia. Verifique que las ciudades estén bien escritas.']);
        }

        // Calcular costos
        $costoDiesel = TarifaService::calcularCostoDiesel($distancia);
        $margen = TarifaService::calcularMargen(
            $request->tarifa_cliente,
            $costoDiesel,
            $request->tarifa_proveedor
        );
        $margenPorcentual = TarifaService::calcularMargenPorcentual(
            $margen,
            $request->tarifa_cliente
        );

        return view('tarifas.resultado', compact(
            'distancia',
            'costoDiesel',
            'margen',
            'margenPorcentual',
            'request'
        ));
    }

    /**
     * Guardar cálculo de tarifa
     */
    public function guardarCalculo(Request $request)
    {
        $request->validate([
            'origen' => 'required|string|max:255',
            'destino' => 'required|string|max:255',
            'distancia_km' => 'required|numeric|min:0',
            'tarifa_cliente' => 'required|numeric|min:0',
            'tarifa_proveedor' => 'nullable|numeric|min:0',
            'costo_diesel' => 'required|numeric|min:0',
            'margen_calculado' => 'required|numeric',
        ]);

        // Guardar en modelo Tarifa para referencia futura
        $tarifa = Tarifa::create([
            'origen' => $request->origen,
            'destino' => $request->destino,
            'precio' => $request->tarifa_cliente,
            'moneda' => 'MXN',
            'vigente_desde' => Carbon::now(),
            'activa' => true,
            'created_by' => $request->user()->id ?? null,
        ]);

        return redirect()->route('tarifas.index')
            ->with('success', 'Cálculo de tarifa guardado correctamente. Puede consultarlo en el historial.');
    }

    /**
     * Gestión de Precio del Diesel
     */
    public function precioDiesel()
    {
        $precioActual = PrecioDiesel::precioActual();
        $historial = PrecioDiesel::orderBy('fecha_vigencia', 'desc')->paginate(20);
        return view('tarifas.precio-diesel', compact('precioActual', 'historial'));
    }

    /**
     * Actualizar precio del diesel
     */
    public function actualizarPrecioDiesel(Request $request)
    {
        $request->validate([
            'precio_litro' => 'required|numeric|min:0',
            'fecha_vigencia' => 'required|date',
            'notas' => 'nullable|string|max:500',
        ]);

        // Desactivar precios anteriores
        PrecioDiesel::where('activo', true)->update(['activo' => false]);

        // Crear nuevo precio
        PrecioDiesel::create([
            'precio_litro' => $request->precio_litro,
            'precio_galon' => $request->precio_litro * 3.78541, // Conversión aproximada
            'fecha_vigencia' => Carbon::parse($request->fecha_vigencia),
            'activo' => true,
            'notas' => $request->notas,
            'created_by' => $request->user()->id ?? null,
        ]);

        return redirect()->route('tarifas.precio-diesel')
            ->with('success', 'Precio del diesel actualizado correctamente.');
    }

    /**
     * Historial de cálculos
     */
    public function historial(Request $request)
    {
        $query = TarifaHistorial::query()->orderBy('changed_at', 'desc');

        if ($request->filled('servicio_id')) {
            $query->where('servicio_id', $request->servicio_id);
        }

        $historial = $query->paginate(20);

        return view('tarifas.historial', compact('historial'));
    }
}
