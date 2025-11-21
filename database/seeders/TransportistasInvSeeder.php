<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TransportistasInv;

class TransportistasInvSeeder extends Seeder
{
    public function run(): void
    {
        TransportistasInv::updateOrCreate(
            ['transportista' => 'TREVSA-001'],
            [
                'transportista' => 'TREVSA-001',
                'nombre' => 'Transportes Ejemplo S.A.',
                'estatus_tpttes' => 'Activo',
                'telefono' => '+5215512345678',
                'qty_unidades_53ft' => 5,
                'tipo_viaje' => 'Nacional',
            ]
        );
    }
}
