<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

// Configuration du composant Livewire (ajustez selon votre structure)
const LOGIN_COMPONENT = 'auth.login';

test('les champs email et mot de passe sont requis', function () {
    Livewire::test(LOGIN_COMPONENT)
        ->set('email', '')
        ->set('password', '')
        ->call('login')
        ->assertHasErrors(['email', 'password']);
});

test('l\'email doit être valide', function () {
    Livewire::test(LOGIN_COMPONENT)
        ->set('email', 'not-an-email')
        ->set('password', 'password')
        ->call('login')
        ->assertHasErrors(['email']);
});

test('redirection après connexion réussie', function (string $email, string $password, string $role, string $redirectPath) {
    // Créer l'utilisateur avec les attributs appropriés
    User::factory()->create([
        'email' => $email,
        'password' => Hash::make($password),
        'role' => $role,
        'valid' => true,
    ]);

    $component = Livewire::test(LOGIN_COMPONENT)
        ->set('email', $email)
        ->set('password', $password)
        ->call('login');

    // Debug : Vérifier s'il y a des erreurs
    if ($component->instance()->getErrorBag()->any()) {
        dump('Erreurs trouvées:', $component->instance()->getErrorBag()->all());
    }

    // Debug : Vérifier l'état de l'authentification
    dump('Utilisateur authentifié:', auth()->check());
    if (auth()->check()) {
        dump('Rôle utilisateur:', auth()->user()->role);
    }

    $component->assertHasNoErrors()
        ->assertRedirect($redirectPath);
})->with([
    // Testons d'abord seulement l'utilisateur normal
    'utilisateur normal' => ['user@example.com', 'password', 'user', '/'],
]);

test('affiche une erreur lors d\'une tentative de connexion échouée', function () {
    // Créer un utilisateur pour le test
    User::factory()->valid()->create([
        'email' => 'user@example.com',
        'password' => Hash::make('correct-password'),
    ]);

    Livewire::test(LOGIN_COMPONENT)
        ->set('email', 'user@example.com')
        ->set('password', 'wrong-password')
        ->call('login')
        ->assertHasErrors(['email']);
});

test('connexion avec des identifiants valides fonctionne', function () {
    User::factory()->create([
        'email' => 'test@example.com',
        'password' => Hash::make('password123'),
        'valid' => true,
        'role' => 'user',
    ]);

    Livewire::test(LOGIN_COMPONENT)
        ->set('email', 'test@example.com')
        ->set('password', 'password123')
        ->call('login')
        ->assertHasNoErrors()
        ->assertRedirect('/');
});

test('utilisateur reste sur la page en cas d\'erreur', function () {
    Livewire::test(LOGIN_COMPONENT)
        ->set('email', 'nonexistent@example.com')
        ->set('password', 'password')
        ->call('login')
        ->assertHasErrors()
        ->assertNoRedirect();
});

// Test pour vérifier si le champ 'valid' affecte la connexion
test('utilisateur invalide ne peut pas se connecter', function () {
    User::factory()->create([
        'email' => 'invalid@example.com',
        'password' => Hash::make('password123'),
        'role' => 'user',
        'valid' => false, // Utilisateur non valide
    ]);

    Livewire::test(LOGIN_COMPONENT)
        ->set('email', 'invalid@example.com')
        ->set('password', 'password123')
        ->call('login')
        ->assertHasErrors(['email']); // Devrait avoir une erreur car l'utilisateur n'est pas valide
});

// Test de diagnostic pour vérifier la configuration
test('le composant login existe et peut être instancié', function () {
    expect(LOGIN_COMPONENT)->toBe('auth.login');

    $component = Livewire::test(LOGIN_COMPONENT);
    expect($component)->not->toBeNull();

    // Vérifier que les propriétés email et password existent
    $component->assertSet('email', '');
    $component->assertSet('password', '');
});

// Test pour vérifier que la méthode login existe
test('la méthode login existe sur le composant', function () {
    $component = Livewire::test(LOGIN_COMPONENT);

    // Tenter d'appeler login sans données pour voir si la méthode existe
    $component->call('login');

    // Si on arrive ici, la méthode existe (même si elle peut échouer)
    expect(true)->toBeTrue();
});
