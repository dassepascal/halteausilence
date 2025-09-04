<?php

namespace Database\Seeders;

use Carbon\Carbon;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'firstname'  => 'Super',
                'name'       => 'Admin',
                'email'      => 'admin@example.com',
                'role'       => 'admin',
                'newsletter' => true,
                'valid'      => true,
                'created_at' => Carbon::now()->subYears(3),
                'updated_at' => Carbon::now()->subYears(3),
                'password'   => bcrypt('password'), // Ajoutez un mot de passe
            ],
            [
                'firstname'  => 'Rédacteur',
                'name'       => 'Redac',
                'email'      => 'redac@example.com',
                'role'       => 'redac',
                'newsletter' => true,
                'valid'      => true,
                'created_at' => Carbon::now()->subYears(3),
                'updated_at' => Carbon::now()->subYears(3),
                'password'   => bcrypt('password'),
            ],
            [
                'firstname'  => 'Utilisateur',
                'name'       => 'User',
                'email'      => 'user@example.com',
                'role'       => 'user',
                'newsletter' => false,
                'valid'      => true,
                'created_at' => Carbon::now()->subYears(2),
                'updated_at' => Carbon::now()->subYears(2),
                'password'   => bcrypt('password'),
            ],
        ];

        foreach ($users as $userData) {
            User::firstOrCreate(
                ['email' => $userData['email']], // Vérifie l'unicité sur l'email
                $userData
            );
        }
    }
}
