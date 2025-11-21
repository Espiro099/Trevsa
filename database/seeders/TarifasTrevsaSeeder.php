<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TarifasTrevsa;

class TarifasTrevsaSeeder extends Seeder
{
    public function run(): void
    {
        TarifasTrevsa::updateOrCreate(
            ['origen' => 'CDMX', 'destino' => 'GDL'],
            [
                'origen' => 'CDMX',
                'destino' => 'GDL',
                'precio' => 1200.50,
                'moneda' => 'MXN',
            ]
        );
    }
}
