<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\UnidadesDisponibles;

class UnidadesDisponiblesSeeder extends Seeder
{
    public function run(): void
    {
        UnidadesDisponibles::updateOrCreate(
            ['unidad_id' => 'UD-1001'],
            [
                'unidad_id' => 'UD-1001',
                'tipo' => 'Remolque 53ft',
                'placas' => 'ABC-1234',
                'estatus' => 'Disponible',
                'ubicacion' => 'CDMX',
            ]
        );
    }
}
