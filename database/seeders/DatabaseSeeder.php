<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Seed application collections and admin user
        $this->call([
            TransportistasInvSeeder::class,
            TarifasTrevsaSeeder::class,
            ClientesSeeder::class,
            UnidadesDisponiblesSeeder::class,
            RegistroSolicitudesSeeder::class,
            AdminUserSeeder::class,
        ]);
    }
}
