<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\RegistroSolicitudes;
use App\Models\Clientes;

class RegistroSolicitudesSeeder extends Seeder
{
    public function run(): void
    {
        $cliente = Clientes::first();

        RegistroSolicitudes::updateOrCreate(
            ['cliente_id' => $cliente?->id ?? 'cliente_demo'],
            [
                'cliente_id' => $cliente?->id ?? 'cliente_demo',
                'origen' => 'CDMX',
                'destino' => 'MTY',
                'fecha_recoleccion' => now(),
                'estatus' => 'Pendiente',
            ]
        );
    }
}
