<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

/**
 * Modelo para Tarifas
 * 
 * Contiene información de tarifas de transporte
 */
class Tarifa extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'tarifas';

    protected $fillable = [
        'origen',
        'destino',
        'precio',
        'moneda',
        'vigente_desde',
        'vigente_hasta',
        'activa',
        'created_by',
    ];

    protected $casts = [
        'precio' => 'float',
        'vigente_desde' => 'datetime',
        'vigente_hasta' => 'datetime',
        'activa' => 'boolean',
    ];
}

