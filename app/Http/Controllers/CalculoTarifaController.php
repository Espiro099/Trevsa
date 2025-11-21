<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\DistanciaService;
use App\Services\TarifaService;

class CalculoTarifaController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function calcularDistancia(Request $request)
    {
        $request->validate([
            'origen' => 'required|string',
            'destino' => 'required|string',
        ]);

        $distancia = DistanciaService::calcularDistancia(
            $request->origen,
            $request->destino
        );

        if ($distancia) {
            return response()->json([
                'success' => true,
                'distancia_km' => $distancia,
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'No se pudo calcular la distancia. Verifique que las ciudades estén bien escritas.',
        ], 400);
    }

    public function calcularTarifa(Request $request)
    {
        $request->validate([
            'distancia_km' => 'required|numeric|min:0',
            'tarifa_cliente' => 'nullable|numeric|min:0',
            'tarifa_proveedor' => 'nullable|numeric|min:0',
        ]);

        $distanciaKm = $request->distancia_km;
        $costoDiesel = TarifaService::calcularCostoDiesel($distanciaKm);
        
        $tarifaCliente = $request->tarifa_cliente ?? 0;
        $tarifaProveedor = $request->tarifa_proveedor ?? 0;
        
        $margen = TarifaService::calcularMargen(
            $tarifaCliente,
            $costoDiesel,
            $tarifaProveedor
        );
        
        $margenPorcentual = TarifaService::calcularMargenPorcentual(
            $margen,
            $tarifaCliente
        );

        return response()->json([
            'success' => true,
            'distancia_km' => $distanciaKm,
            'costo_diesel' => $costoDiesel,
            'tarifa_cliente' => $tarifaCliente,
            'tarifa_proveedor' => $tarifaProveedor,
            'margen_calculado' => $margen,
            'margen_porcentual' => $margenPorcentual,
        ]);
    }
}
