<?php

namespace App\Services;

use App\Models\Servicio;
use App\Models\EstadoHistorial;
use Carbon\Carbon;

class EstadoService
{
    /**
     * Definición de estados válidos
     */
    const ESTADOS = [
        'pendiente',
        'confirmado',
        'en_carga',
        'en_transito',
        'entregado',
        'facturado',
        'cancelado',
    ];

    /**
     * Transiciones permitidas entre estados
     * Clave: estado actual, Valor: array de estados permitidos
     */
    const TRANSICIONES = [
        'pendiente' => ['confirmado', 'cancelado'],
        'confirmado' => ['en_carga', 'cancelado'],
        'en_carga' => ['en_transito', 'cancelado'],
        'en_transito' => ['entregado', 'cancelado'],
        'entregado' => ['facturado'],
        'facturado' => [], // Estado final, no se puede cambiar
        'cancelado' => [], // Estado final, no se puede cambiar
    ];

    /**
     * Verificar si una transición es válida
     *
     * @param string $estadoActual
     * @param string $estadoNuevo
     * @return bool
     */
    public static function esTransicionValida(string $estadoActual, string $estadoNuevo): bool
    {
        // No permitir cambiar al mismo estado
        if ($estadoActual === $estadoNuevo) {
            return false;
        }

        // Verificar que ambos estados existan
        if (!in_array($estadoActual, self::ESTADOS) || !in_array($estadoNuevo, self::ESTADOS)) {
            return false;
        }

        // Verificar transición permitida
        $transicionesPermitidas = self::TRANSICIONES[$estadoActual] ?? [];
        
        return in_array($estadoNuevo, $transicionesPermitidas);
    }

    /**
     * Obtener estados permitidos desde un estado actual
     *
     * @param string $estadoActual
     * @return array
     */
    public static function obtenerEstadosPermitidos(string $estadoActual): array
    {
        return self::TRANSICIONES[$estadoActual] ?? [];
    }

    /**
     * Cambiar el estado de un servicio
     *
     * @param Servicio $servicio
     * @param string $nuevoEstado
     * @param string|null $comentario
     * @param int|null $userId
     * @return bool
     * @throws \Exception
     */
    public static function cambiarEstado(
        Servicio $servicio,
        string $nuevoEstado,
        ?string $comentario = null,
        ?int $userId = null
    ): bool {
        $estadoActual = $servicio->estado ?? 'pendiente';

        // Validar transición
        if (!self::esTransicionValida($estadoActual, $nuevoEstado)) {
            throw new \Exception(
                "No se puede cambiar el estado de '{$estadoActual}' a '{$nuevoEstado}'. " .
                "Estados permitidos: " . implode(', ', self::obtenerEstadosPermitidos($estadoActual))
            );
        }

        // Guardar historial antes de cambiar
        EstadoHistorial::create([
            'servicio_id' => $servicio->_id,
            'estado_anterior' => $estadoActual,
            'estado_nuevo' => $nuevoEstado,
            'comentario' => $comentario,
            'changed_by' => $userId,
            'changed_at' => Carbon::now(),
        ]);

        // Actualizar estado del servicio
        $servicio->estado = $nuevoEstado;
        $servicio->save();

        return true;
    }

    /**
     * Obtener historial de cambios de estado de un servicio
     *
     * @param string $servicioId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function obtenerHistorial(string $servicioId)
    {
        return EstadoHistorial::where('servicio_id', $servicioId)
            ->orderBy('changed_at', 'desc')
            ->get();
    }

    /**
     * Obtener etiqueta legible de un estado
     *
     * @param string $estado
     * @return string
     */
    public static function obtenerEtiqueta(string $estado): string
    {
        $etiquetas = [
            'pendiente' => 'Pendiente',
            'confirmado' => 'Confirmado',
            'en_carga' => 'En Carga',
            'en_transito' => 'En Tránsito',
            'entregado' => 'Entregado',
            'facturado' => 'Facturado',
            'cancelado' => 'Cancelado',
        ];

        return $etiquetas[$estado] ?? ucfirst($estado);
    }

    /**
     * Obtener color CSS para un estado
     *
     * @param string $estado
     * @return string
     */
    public static function obtenerColor(string $estado): string
    {
        $colores = [
            'pendiente' => 'bg-gray-100 text-gray-800',
            'confirmado' => 'bg-blue-100 text-blue-800',
            'en_carga' => 'bg-yellow-100 text-yellow-800',
            'en_transito' => 'bg-orange-100 text-orange-800',
            'entregado' => 'bg-green-100 text-green-800',
            'facturado' => 'bg-purple-100 text-purple-800',
            'cancelado' => 'bg-red-100 text-red-800',
        ];

        return $colores[$estado] ?? 'bg-gray-100 text-gray-800';
    }
}
