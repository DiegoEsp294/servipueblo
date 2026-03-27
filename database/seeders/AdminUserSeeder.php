<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run()
    {
        $user = User::firstOrCreate(
            ['email' => 'admin@servipueblo.com'],
            [
                'name'     => 'Administrador',
                'password' => Hash::make('admin1234'),
                'role'     => 'admin',
            ]
        );

        // Garantizar que el admin siempre tenga role correcto
        if ($user->role !== 'admin') {
            $user->update(['role' => 'admin']);
        }
    }
}
