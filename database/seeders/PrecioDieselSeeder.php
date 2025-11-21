<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PrecioDiesel;
use Carbon\Carbon;

class PrecioDieselSeeder extends Seeder
{
    public function run(): void
    {
        // Desactivar todos los precios anteriores
        PrecioDiesel::where('activo', true)->update(['activo' => false]);

        // Crear precio actual del diesel
        PrecioDiesel::create([
            'precio_litro' => 24.50,
            'precio_galon' => 92.70,
            'fecha_vigencia' => Carbon::now(),
            'activo' => true,
            'notas' => 'Precio inicial configurado por sistema',
            'created_by' => null,
        ]);
    }
}
