<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

/**
 * Modelo para Transportistas
 * 
 * Contiene información de transportistas en inventario
 */
class Transportista extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'transportistas';

    protected $fillable = [
        'transportista',
        'nombre',
        'estatus',
        'telefono',
        'cantidad_unidades_53ft',
        'tipo_viaje',
        'notas',
        'created_by',
    ];

    protected $casts = [
        'cantidad_unidades_53ft' => 'integer',
    ];
}

