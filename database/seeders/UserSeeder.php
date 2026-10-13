<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            ['name' => 'Assessoria de Comunicação', 'email' => 'comunicacao@example.test',
                'role' => 'communication_admin', 'department' => 'Comunicação'],
            ['name' => 'Coordenação de Educação', 'email' => 'educacao@example.test',
                'role' => 'coordinator', 'department' => 'Educação'],
            ['name' => 'Coordenação de Saúde', 'email' => 'saude@example.test',
                'role' => 'coordinator', 'department' => 'Saúde'],
        ];

        foreach ($users as $user) {
            if (DB::table('users')->where('email', $user['email'])->exists()) {
                continue;
            }

            DB::table('users')->insert([
                ...$user,
                'password' => Hash::make(Str::random(40)),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
