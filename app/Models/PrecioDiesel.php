<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

/**
 * Modelo para Precio del Diesel
 * 
 * Permite almacenar y consultar el precio actual del diesel
 */
class PrecioDiesel extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'precio_diesel';

    protected $fillable = [
        'precio_litro',
        'precio_galon',
        'fecha_vigencia',
        'activo',
        'notas',
        'created_by',
    ];

    protected $casts = [
        'precio_litro' => 'float',
        'precio_galon' => 'float',
        'fecha_vigencia' => 'datetime',
        'activo' => 'boolean',
    ];

    /**
     * Obtener el precio actual del diesel
     */
    public static function precioActual()
    {
        return self::where('activo', true)
            ->orderBy('fecha_vigencia', 'desc')
            ->first();
    }
}
