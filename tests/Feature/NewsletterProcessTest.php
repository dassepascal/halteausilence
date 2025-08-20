<?php
// tests/Feature/NewsletterProcessTest.php

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);
use App\Models\User;
use App\Models\Newsletter;
use Illuminate\Support\Facades\Mail;
use App\Mail\NewsletterMail;

it('récupère uniquement les utilisateurs abonnés et validés via les scopes', function () {
    // Utilisateur abonné et validé (doit être inclus)
    $user1 = User::factory()->create(['newsletter' => true, 'valid' => true]);
    // Utilisateur abonné mais non validé (exclu)
    User::factory()->create(['newsletter' => true, 'valid' => false]);
    // Utilisateur non abonné mais validé (exclu)
    User::factory()->create(['newsletter' => false, 'valid' => true]);
    // Utilisateur ni abonné ni validé (exclu)
    User::factory()->create(['newsletter' => false, 'valid' => false]);

    $subscribers = User::newsletterSubscribers()->validUsers()->get();

    expect($subscribers)->toHaveCount(1);
    expect($subscribers->first()->id)->toBe($user1->id);
    expect($subscribers->first()->newsletter)->toBeTrue();
    expect($subscribers->first()->valid)->toBeTrue();
});

it('simule le process complet d\'envoi de newsletter via le service', function () {
    Mail::fake();

    // 1. Créer un utilisateur abonné et valide
    $user = User::factory()->create([
        'newsletter' => true,
        'valid' => true,
        'email' => 'test@example.com',
        'name' => 'Test User',
        'firstname' => 'Test',
    ]);

    // 2. Créer une newsletter
    $newsletter = Newsletter::factory()->create([
        'title' => 'Test Pest',
        'subject' => 'Sujet test',
        'content' => 'Contenu test',
        'status' => 'draft',
        'created_by' => $user->id,
    ]);

    // 3. Appeler le service pour envoyer la newsletter
    $result = app(\App\Services\NewsletterService::class)->sendNewsletter($newsletter);

    // Debug si nécessaire
    if (!$result['success'] || $result['sent_count'] === 0) {
        dump('Erreur dans le service:', $result);

        // Vérifier si les routes existent
        dump('Routes disponibles:', collect(app('router')->getRoutes())->map(fn($route) => $route->getName())->filter()->values());
    }

    // 3.1 Vérifier que l'envoi a réussi
    expect($result['success'])->toBeTrue();
    expect($result['sent_count'])->toBe(1);
    expect($result['failed_count'])->toBe(0);

    // 4. Vérifier que la newsletter est marquée comme envoyée
    expect($newsletter->fresh()->status)->toBe('sent');
    expect($newsletter->fresh()->sent_at)->not->toBeNull();

    // 5. Vérifier la présence dans la table newsletter_subscribers
    $this->assertDatabaseHas('newsletter_subscribers', [
        'newsletter_id' => $newsletter->id,
        'user_id' => $user->id,
    ]);

    // 6. Vérifier qu'un mail a été envoyé
    Mail::assertSent(NewsletterMail::class, function ($mail) use ($user) {
        return $mail->hasTo($user->email);
    });

    // 7. Vérifier le nombre d'envois
    expect($newsletter->fresh()->sent_count)->toBe(1);

    // 8. Vérifier que l'enregistrement newsletter_subscribers contient sent_at
    $subscriber = \App\Models\NewsletterSubscriber::where([
        'newsletter_id' => $newsletter->id,
        'user_id' => $user->id,
    ])->first();

    expect($subscriber)->not->toBeNull();
    expect($subscriber->sent_at)->not->toBeNull();
});

it('test l\'envoi d\'email avec les bonnes données', function () {
    Mail::fake();

    $user = User::factory()->create([
        'newsletter' => true,
        'valid' => true,
        'email' => 'test@example.com',
        'name' => 'John',
        'firstname' => 'Doe',
    ]);

    $newsletter = Newsletter::factory()->create([
        'title' => 'Newsletter Test',
        'subject' => 'Sujet de test',
        'content' => '<p>Contenu de test</p>',
        'status' => 'draft',
    ]);

    app(\App\Services\NewsletterService::class)->sendNewsletter($newsletter);

    Mail::assertSent(NewsletterMail::class, function ($mail) use ($user, $newsletter) {
        return $mail->hasTo($user->email)
            && $mail->newsletter->id === $newsletter->id
            && $mail->user->id === $user->id;
    });
});
