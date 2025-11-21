<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

/**
 * Modelo para Historial de Cambios de Estado de Servicios
 */
class EstadoHistorial extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'estado_historial';

    protected $fillable = [
        'servicio_id',
        'estado_anterior',
        'estado_nuevo',
        'comentario',
        'changed_by',
        'changed_at',
    ];

    protected $casts = [
        'changed_at' => 'datetime',
    ];

    /**
     * Relación con Servicio
     */
    public function servicio()
    {
        return $this->belongsTo(Servicio::class, 'servicio_id');
    }

    /**
     * Relación con Usuario que hizo el cambio
     */
    public function usuario()
    {
        return $this->belongsTo(User::class, 'changed_by');
    }
}
