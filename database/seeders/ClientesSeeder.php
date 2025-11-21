<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Clientes;

class ClientesSeeder extends Seeder
{
    public function run(): void
    {
        Clientes::updateOrCreate(
            ['rfc' => 'XAXX010101000'],
            [
                'razon_social' => 'Cliente Ejemplo SA de CV',
                'rfc' => 'XAXX010101000',
                'contacto' => 'Juan Perez',
                'telefono' => '+5215511122233',
                'correo' => 'cliente@example.com',
                'direccion' => 'Av. Ejemplo 123, Ciudad',
            ]
        );
    }
}
