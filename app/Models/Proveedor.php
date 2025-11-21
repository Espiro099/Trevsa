<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

/**
 * Modelo para Proveedores/Prospectos de Proveedores
 * 
 * Contiene información básica de prospectos de proveedores
 */
class Proveedor extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'proveedores';

    protected $fillable = [
        'nombre_empresa',
        'telefono',
        'email',
        'cantidad_unidades',
        'tipos_unidades',
        'cantidades_unidades',
        'base_linea_transporte',
        'corredor_linea_transporte',
        'nombre_quien_registro',
        'notas',
        'estado_prospecto',
        'created_by',
    ];

    protected $casts = [
        'cantidad_unidades' => 'integer',
        'tipos_unidades' => 'array',
        'cantidades_unidades' => 'array',
    ];

    /**
     * Relación con Servicios (opcional)
     */
    public function servicios()
    {
        return $this->hasMany(Servicio::class, 'proveedor_id');
    }

    /**
     * Relación con TransporteProveedor (Alta)
     */
    public function altaProveedor()
    {
        return $this->hasOne(TransporteProveedor::class, 'proveedor_id');
    }

    /**
     * Verificar si el prospecto tiene alta completa
     */
    public function tieneAltaCompleta()
    {
        return $this->altaProveedor && $this->altaProveedor->status === 'alta';
    }

    /**
     * Obtener ID formateado con nomenclatura PROV-xxx
     * Basado en el orden de creación (más antiguo = número menor)
     */
    public function getFormattedIdAttribute()
    {
        // Obtener el número secuencial basado en la posición del registro por fecha de creación
        $count = self::where('created_at', '<=', $this->created_at)
            ->orderBy('created_at', 'asc')
            ->orderBy('_id', 'asc')
            ->count();
        
        return 'PROV-' . str_pad($count, 3, '0', STR_PAD_LEFT);
    }
}

