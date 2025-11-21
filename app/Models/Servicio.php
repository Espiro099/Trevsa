<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

/**
 * Modelo para Servicios/Solicitudes de Transporte
 * 
 * Contiene información sobre los servicios de transporte solicitados por clientes
 */
class Servicio extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'servicios';

    protected $fillable = [
        'cliente_id',
        'cliente_nombre',
        'proveedor_id',
        'proveedor_nombre',
        'tipo_transporte',
        'tipo_carga',
        'peso_carga',
        'origen',
        'destino',
        'fecha_servicio',
        'hora_servicio',
        'tarifa_cliente',
        'tarifa_proveedor',
        'distancia_km',
        'costo_diesel',
        'margen_calculado',
        'estado',
        'comentarios',
        'created_by',
    ];

    protected $casts = [
        'fecha_servicio' => 'datetime',
        'tarifa_cliente' => 'float',
        'tarifa_proveedor' => 'float',
        'peso_carga' => 'float',
        'distancia_km' => 'float',
        'costo_diesel' => 'float',
        'margen_calculado' => 'float',
    ];

    /**
     * Relación con Cliente (opcional, por referencia)
     */
    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'cliente_id');
    }

    /**
     * Relación con Proveedor (opcional, por referencia)
     */
    public function proveedor()
    {
        return $this->belongsTo(Proveedor::class, 'proveedor_id');
    }

    /**
     * Relación con Historial de Estados
     */
    public function historialEstados()
    {
        return $this->hasMany(EstadoHistorial::class, 'servicio_id');
    }

    /**
     * Relación con Historial de Tarifas
     */
    public function historialTarifas()
    {
        return $this->hasMany(TarifaHistorial::class, 'servicio_id');
    }
}

