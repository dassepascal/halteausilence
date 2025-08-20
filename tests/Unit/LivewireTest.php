<?php

use function Pest\Livewire\livewire;

test('livewire est installé et fonctionne', function () {
    // Vérifiez que la fonction livewire existe
    expect(function_exists('Pest\Livewire\livewire'))->toBeTrue();

    // Vérifiez que vous pouvez accéder à la page de connexion
    $response = $this->get('/login');
    $response->assertStatus(200);

    // Vérifiez que la page contient des éléments Livewire
    $response->assertSee('wire:');
});
