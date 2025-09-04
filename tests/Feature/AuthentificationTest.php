<?php

use App\Models\User;
use Livewire\Volt\Volt;

test('users can authenticate using the login screen', function () {
    // 1) Créer un utilisateur avec un mot de passe correspondant à 'password'
   $component = Volt::test('auth.login')
    ->set('email', $user->email)
    ->set('password', 'password')
    ->call('login');

// L’utilisateur standard doit être redirigé vers "/"
$component->assertRedirect('/');
$this->assertAuthenticated();
// auth()->logout(); // On se déconnecte pour le test suivant

// 2) Tester un utilisateur Rédacteur (redac)
$redac = User::factory()->create([
    'role'     => 'redac',
    'password' => bcrypt('password'),
]);

$component = Volt::test('auth.login')
    ->set('email', $redac->email)
    ->set('password', 'password')
    ->call('login');

// L’utilisateur Rédacteur doit être redirigé vers "/admin/dashboard"
$component->assertRedirect('/admin/dashboard');
$this->assertAuthenticated();
auth()->logout();

// 3) Tester un utilisateur Admin
$admin = User::factory()->create([
    'role'     => 'admin',
    'password' => bcrypt('password'),
]);

$component = Volt::test('auth.login')
    ->set('email', $admin->email)
    ->set('password', 'password')
    ->call('login');
    dump($component);

// L’utilisateur Admin doit être redirigé vers "/admin/dashboard"
$component->assertRedirect('/admin/dashboard');
$this->assertAuthenticated();
});
