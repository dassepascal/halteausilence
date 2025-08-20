<?php

namespace Database\Seeders;

use Carbon\Carbon;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
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
            ],
        ];

        foreach ($users as $userData) {
            User::factory()->create($userData);
        }
    }
}

