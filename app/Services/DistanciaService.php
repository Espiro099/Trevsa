<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class DistanciaService
{
    /**
     * Calcular distancia usando OpenRouteService (gratis con límite)
     */
    public static function calcularDistanciaOpenRoute($origen, $destino, $apiKey = null)
    {
        try {
            if (!$apiKey) {
                return null;
            }

            $cacheKey = 'distancia_' . md5($origen . '_' . $destino);
            $cached = Cache::get($cacheKey);
            if ($cached) {
                return $cached;
            }

            $coordsOrigen = self::geocodeOpenRoute($origen, $apiKey);
            $coordsDestino = self::geocodeOpenRoute($destino, $apiKey);

            if (!$coordsOrigen || !$coordsDestino) {
                return null;
            }

            $response = Http::withHeaders([
                'Authorization' => $apiKey,
                'Content-Type' => 'application/json'
            ])->post('https://api.openrouteservice.org/v2/directions/driving-car', [
                'coordinates' => [
                    [$coordsOrigen['lon'], $coordsOrigen['lat']],
                    [$coordsDestino['lon'], $coordsDestino['lat']]
                ]
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $distance = $data['routes'][0]['summary']['distance'] / 1000;
                Cache::put($cacheKey, $distance, now()->addDays(7));
                return round($distance, 2);
            }
        } catch (\Exception $e) {
            Log::warning('Error calculando distancia: ' . $e->getMessage());
        }

        return null;
    }

    private static function geocodeOpenRoute($direccion, $apiKey)
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => $apiKey,
            ])->get('https://api.openrouteservice.org/geocoding', [
                'text' => $direccion,
                'size' => 1
            ]);

            if ($response->successful()) {
                $data = $response->json();
                if (!empty($data['features'])) {
                    return $data['features'][0]['geometry']['coordinates'];
                }
            }
        } catch (\Exception $e) {
            Log::warning('Error geocodificando: ' . $e->getMessage());
        }

        return null;
    }

    /**
     * Calcular distancia aproximada usando ciudades conocidas
     */
    public static function calcularDistanciaAproximada($origen, $destino)
    {
        $ciudades = [
            'monterrey' => ['lat' => 25.6866, 'lon' => -100.3161],
            'guadalajara' => ['lat' => 20.6597, 'lon' => -103.3496],
            'cdmx' => ['lat' => 19.4326, 'lon' => -99.1332],
            'méxico' => ['lat' => 19.4326, 'lon' => -99.1332],
            'mérida' => ['lat' => 20.9674, 'lon' => -89.5926],
            'cancún' => ['lat' => 21.1619, 'lon' => -86.8515],
            'puebla' => ['lat' => 19.0414, 'lon' => -98.2063],
            'querétaro' => ['lat' => 20.5881, 'lon' => -100.3881],
        ];

        $coordsOrigen = null;
        $coordsDestino = null;

        foreach ($ciudades as $ciudad => $coords) {
            if (stripos($origen, $ciudad) !== false) {
                $coordsOrigen = $coords;
            }
            if (stripos($destino, $ciudad) !== false) {
                $coordsDestino = $coords;
            }
        }

        if (!$coordsOrigen || !$coordsDestino) {
            return null;
        }

        return self::haversineDistance(
            $coordsOrigen['lat'],
            $coordsOrigen['lon'],
            $coordsDestino['lat'],
            $coordsDestino['lon']
        );
    }

    private static function haversineDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371;
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);
        $a = sin($dLat / 2) * sin($dLat / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLon / 2) * sin($dLon / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        return round($earthRadius * $c, 2);
    }

    public static function calcularDistancia($origen, $destino)
    {
        $apiKey = env('OPENROUTESERVICE_API_KEY');
        if ($apiKey) {
            $distancia = self::calcularDistanciaOpenRoute($origen, $destino, $apiKey);
            if ($distancia) {
                return $distancia;
            }
        }

        return self::calcularDistanciaAproximada($origen, $destino);
    }
}
