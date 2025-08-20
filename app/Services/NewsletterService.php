<?php

// app/Services/NewsletterService.php
namespace App\Services;

use App\Models\Newsletter;
use App\Models\User;
use App\Mail\NewsletterMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class NewsletterService
{
    public function sendNewsletter(Newsletter $newsletter): array
    {
        if ($newsletter->status === 'sent') {
            return [
                'success' => false,
                'message' => 'Cette newsletter a déjà été envoyée.'
            ];
        }

        // Récupérer tous les abonnés valides
        $subscribers = User::newsletterSubscribers()->validUsers()->get();

        if ($subscribers->isEmpty()) {
            return [
                'success' => false,
                'message' => 'Aucun abonné trouvé.'
            ];
        }

        $sentCount = 0;
        $failedCount = 0;

        foreach ($subscribers as $subscriber) {
            try {
                // Envoyer l'email
                Mail::to($subscriber->email)->send(new NewsletterMail($newsletter, $subscriber));
                \App\Models\NewsletterSubscriber::create([
                    'newsletter_id' => $newsletter->id,
                    'user_id' => $subscriber->id,
                    'sent_at' => now(),
                    'opened' => false,
                    'clicked' => false,
                ]);
              
                $sentCount++;
            } catch (\Exception $e) {
                $failedCount++;
                Log::error('Newsletter send failed for user '.$subscriber->id.': '.$e->getMessage());
            }
        }

        // Marquer la newsletter comme envoyée
        $newsletter->update([
            'status' => 'sent',
            'sent_at' => now(),
            'sent_count' => $sentCount,
        ]);

        return [
            'success' => true,
            'message' => "Newsletter envoyée avec succès à {$sentCount} abonnés." .
                        ($failedCount > 0 ? " {$failedCount} échecs." : ''),
            'sent_count' => $sentCount,
            'failed_count' => $failedCount,
        ];
    }

    public function scheduleNewsletter(Newsletter $newsletter, $scheduledAt): bool
    {
        $newsletter->update([
            'status' => 'scheduled',
            'scheduled_at' => $scheduledAt,
        ]);

        return true;
    }

    public function processScheduledNewsletters(): array
    {
        $newsletters = Newsletter::where('status', 'scheduled')
            ->where('scheduled_at', '<=', now())
            ->get();

        $results = [];

        foreach ($newsletters as $newsletter) {
            $result = $this->sendNewsletter($newsletter);
            $results[] = [
                'newsletter_id' => $newsletter->id,
                'newsletter_title' => $newsletter->title,
                'result' => $result,
            ];
        }

        return $results;
    }

    public function getNewsletterStats(Newsletter $newsletter): array
    {
        $subscribers = $newsletter->subscriberRecords();

        $totalSent = $subscribers->count();
        $totalOpened = $subscribers->where('opened', true)->count();
        $totalClicked = $subscribers->where('clicked', true)->count();

        return [
            'total_sent' => $totalSent,
            'total_opened' => $totalOpened,
            'total_clicked' => $totalClicked,
            'open_rate' => $totalSent > 0 ? round(($totalOpened / $totalSent) * 100, 2) : 0,
            'click_rate' => $totalSent > 0 ? round(($totalClicked / $totalSent) * 100, 2) : 0,
        ];
    }

    public function trackOpen(Newsletter $newsletter, User $user): void
    {
        $subscriber = $newsletter->subscriberRecords()
            ->where('user_id', $user->id)
            ->first();

        if ($subscriber && !$subscriber->opened) {
            $subscriber->update([
                'opened' => true,
                'opened_at' => now(),
            ]);
        }
    }

    public function trackClick(Newsletter $newsletter, User $user): void
    {
        $subscriber = $newsletter->subscriberRecords()
            ->where('user_id', $user->id)
            ->first();

        if ($subscriber) {
            if (!$subscriber->opened) {
                $subscriber->update([
                    'opened' => true,
                    'opened_at' => now(),
                ]);
            }

            if (!$subscriber->clicked) {
                $subscriber->update([
                    'clicked' => true,
                    'clicked_at' => now(),
                ]);
            }
        }
    }

    public function unsubscribeUser(User $user, $token = null): bool
    {
        // Vérifier le token si fourni (pour les liens de désinscription)
        if ($token && !$this->validateUnsubscribeToken($user, $token)) {
            return false;
        }

        $user->update(['newsletter' => false]);
        return true;
    }

    public function validateUnsubscribeToken(User $user, string $token): bool
    {
        $expectedToken = hash('sha256', $user->id . $user->email . config('app.key'));
        return hash_equals($expectedToken, $token);
    }

    public function generateUnsubscribeToken(User $user): string
    {
        return hash('sha256', $user->id . $user->email . config('app.key'));
    }

    public function generateTrackingPixelUrl(Newsletter $newsletter, User $user): string
    {
        return route('newsletter.track.open', [
            'newsletter' => $newsletter->id,
            'user' => $user->id,
            'token' => $this->generateTrackingToken($newsletter, $user),
        ]);
    }

    public function generateTrackingToken(Newsletter $newsletter, User $user): string
    {
        return hash('sha256', $newsletter->id . $user->id . config('app.key'));
    }

    public function validateTrackingToken(Newsletter $newsletter, User $user, string $token): bool
    {
        $expectedToken = $this->generateTrackingToken($newsletter, $user);
        return hash_equals($expectedToken, $token);
    }
}
