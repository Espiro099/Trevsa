<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

/**
 * Modelo para Unidades Disponibles
 * 
 * Contiene información de unidades de transporte disponibles
 */
class UnidadDisponible extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'unidades_disponibles';

    protected $fillable = [
        'transporte_proveedor_id', // Relación con altas proveedores
        'user_id', // ID del usuario/transportista que creó el registro (para Row-Level Security)
        'nombre_transportista', // Nombre del transportista (de la alta)
        'unidades_disponibles', // Array de unidades seleccionadas (hasta 3)
        'cantidades_unidades', // Array de cantidades por unidad
        'lugar_disponible',
        'fecha_disponible',
        'hora_disponible',
        'destino_sugerido',
        'notas',
        'estatus', // Campo de estatus para futuro funcionamiento
        'created_by',
    ];

    protected $casts = [
        'fecha_disponible' => 'datetime',
        'unidades_disponibles' => 'array',
        'cantidades_unidades' => 'array',
    ];

    /**
     * Relación con TransporteProveedor (Alta Proveedor)
     */
    public function transporteProveedor()
    {
        return $this->belongsTo(TransporteProveedor::class, 'transporte_proveedor_id');
    }

    /**
     * Relación con User (Transportista)
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Scope para filtrar unidades por usuario (Row-Level Security)
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Obtener el nombre del transportista desde la relación
     */
    public function getNombreTransportistaAttribute($value)
    {
        if ($value) {
            return $value;
        }
        
        if ($this->transporteProveedor && $this->transporteProveedor->proveedor) {
            return $this->transporteProveedor->proveedor->nombre_empresa ?? null;
        }
        
        return null;
    }
}

