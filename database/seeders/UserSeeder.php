<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name'     => 'Administrador',
            'email'    => 'admin@admin.com',
            'password' => bcrypt('123456'),
            'role'     => UserRole::ADMIN->value,
        ]);

        User::create([
            'name'     => 'Usuário Comum',
            'email'    => 'user@user.com',
            'password' => bcrypt('123456'),
            'role'     => UserRole::COMMON->value,
        ]);
    }
}
