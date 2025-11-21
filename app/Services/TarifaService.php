<?php

namespace App\Services;

use App\Models\PrecioDiesel;
use App\Models\Servicio;
use App\Models\TarifaHistorial;

class TarifaService
{
    public static function calcularCostoDiesel($distanciaKm, $consumoPorKm = 0.35)
    {
        if (!$distanciaKm || $distanciaKm <= 0) {
            return null;
        }

        $precioDiesel = PrecioDiesel::precioActual();
        $precioLitro = $precioDiesel ? $precioDiesel->precio_litro : 24.50;

        $litrosNecesarios = $distanciaKm * $consumoPorKm;
        $costoTotal = $litrosNecesarios * $precioLitro;

        return round($costoTotal, 2);
    }

    public static function calcularMargen($tarifaCliente, $costoDiesel, $tarifaProveedor = null)
    {
        if (!$tarifaCliente || $tarifaCliente <= 0) {
            return 0;
        }

        $totalCostos = $costoDiesel ?? 0;
        if ($tarifaProveedor) {
            $totalCostos += $tarifaProveedor;
        }

        return round($tarifaCliente - $totalCostos, 2);
    }

    public static function calcularMargenPorcentual($margen, $tarifaCliente)
    {
        if (!$tarifaCliente || $tarifaCliente <= 0) {
            return 0;
        }

        return round(($margen / $tarifaCliente) * 100, 2);
    }

    public static function guardarHistorial(
        Servicio $servicio,
        array $datosAnteriores,
        array $datosNuevos,
        ?int $userId = null
    ) {
        $cambios = [];

        if (isset($datosNuevos['tarifa_cliente']) && 
            ($datosAnteriores['tarifa_cliente'] ?? null) != $datosNuevos['tarifa_cliente']) {
            $cambios['tarifa_cliente'] = [
                'anterior' => $datosAnteriores['tarifa_cliente'] ?? null,
                'nuevo' => $datosNuevos['tarifa_cliente'],
            ];
        }

        if (isset($datosNuevos['tarifa_proveedor']) && 
            ($datosAnteriores['tarifa_proveedor'] ?? null) != $datosNuevos['tarifa_proveedor']) {
            $cambios['tarifa_proveedor'] = [
                'anterior' => $datosAnteriores['tarifa_proveedor'] ?? null,
                'nuevo' => $datosNuevos['tarifa_proveedor'],
            ];
        }

        if (!empty($cambios)) {
            TarifaHistorial::create([
                'servicio_id' => $servicio->_id,
                'cambios' => $cambios,
                'distancia_km' => $datosNuevos['distancia_km'] ?? $servicio->distancia_km ?? null,
                'costo_diesel' => $datosNuevos['costo_diesel'] ?? $servicio->costo_diesel ?? null,
                'margen_calculado' => $datosNuevos['margen_calculado'] ?? $servicio->margen_calculado ?? null,
                'changed_by' => $userId,
                'changed_at' => \Carbon\Carbon::now(),
            ]);
        }
    }
}
