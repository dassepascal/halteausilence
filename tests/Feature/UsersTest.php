<?php
use Illuminate\Support\Facades\DB;

test('debug user seeder', function () {
    DB::enableQueryLog();
    $existingUsers = \App\Models\User::all()->toArray();
    // dump('Users before seeding:', $existingUsers);
    $this->seed(\Database\Seeders\UserSeeder::class);
    // dump('Query log:', \DB::getQueryLog());
    $users = \App\Models\User::all()->toArray();
    // dump('Users after seeding:', $users);
    $this->assertDatabaseCount('users', 3);
    $this->assertDatabaseHas('users', ['email' => 'admin@example.com']);
});
