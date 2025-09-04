<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

test('allows an admin to view manage users page', function () {
    $adminUser = User::factory()->create([
        'email' => 'admin_' . uniqid() . '@example.com', // Email unique
        'role' => 'admin',
        'password' => bcrypt('password'),
    ]);

    $this->actingAs($adminUser)
         ->get('/manage-users')
         ->assertOk();
});
