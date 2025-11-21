<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@trevsa.local'],
            [
                'name' => 'Administrador TREVSA',
                'password' => bcrypt('password'), // cambie la contraseña en producción
                'role' => 'admin',
                'roles' => ['admin'],
                'permissions' => [],
            ]
        );
    }
}
