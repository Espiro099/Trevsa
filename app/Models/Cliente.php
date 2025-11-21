<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

/**
 * Modelo para Clientes/Prospectos de Clientes
 * 
 * Contiene información de prospectos y clientes potenciales
 */
class Cliente extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'clientes';

    public $timestamps = true;

    protected $fillable = [
        'nombre_empresa',
        'nombre_contacto',
        'telefono',
        'email',
        'ciudad',
        'estado',
        'industria',
        'comentarios',
        'estado_prospecto',
        'created_by',
    ];

    protected $casts = [
        'telefono' => 'string',
    ];

    /**
     * Relación con Servicios (opcional)
     */
    public function servicios()
    {
        return $this->hasMany(Servicio::class, 'cliente_id');
    }
}

