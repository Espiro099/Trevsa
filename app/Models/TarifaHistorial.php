<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class TarifaHistorial extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'tarifa_historial';

    protected $fillable = [
        'servicio_id',
        'cambios',
        'distancia_km',
        'costo_diesel',
        'margen_calculado',
        'changed_by',
        'changed_at',
    ];

    protected $casts = [
        'cambios' => 'array',
        'distancia_km' => 'float',
        'costo_diesel' => 'float',
        'margen_calculado' => 'float',
        'changed_at' => 'datetime',
    ];

    public function servicio()
    {
        return $this->belongsTo(Servicio::class, 'servicio_id');
    }

    public static function obtenerHistorial($servicioId)
    {
        return self::where('servicio_id', $servicioId)
            ->orderBy('changed_at', 'desc')
            ->get();
    }
}
