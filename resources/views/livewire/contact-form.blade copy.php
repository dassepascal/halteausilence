<?php
// resources/views/livewire/contact-form.blade.php

use App\Mail\ContactMessage;
use App\Models\Contact;
use Illuminate\Support\Facades\Mail;
use Livewire\Attributes\Validate;
use Livewire\Volt\Component;

new class extends Component
{
    #[Validate('required|string|max:255')]
    public string $name = '';

    #[Validate('required|email|max:255')]
    public string $email = '';

    #[Validate('required|string|max:255')]
    public string $subject = '';

    #[Validate('required|string|min:10')]
    public string $message = '';

    public bool $showSuccess = false;

    public function submit()
    {
        $this->validate();

        $contact = Contact::create([
            'name' => $this->name,
            'email' => $this->email,
            'subject' => $this->subject,
            'message' => $this->message,
        ]);

        // Envoyer l'email
        try {
            Mail::to(config('mail.contact_email', 'admin@example.com'))
                ->send(new ContactMessage($contact));
        } catch (\Exception $e) {
            // Log l'erreur mais ne pas faire échouer le processus
            logger()->error('Erreur envoi email contact: ' . $e->getMessage());
        }

        // Reset du formulaire
        $this->reset(['name', 'email', 'subject', 'message']);
        $this->showSuccess = true;

        // Masquer le message de succès après 5 secondes
        $this->dispatch('contact-sent');
    }

    public function hideSuccess()
    {
        $this->showSuccess = false;
    }
}; ?>

<div class="max-w-2xl mx-auto p-6">
    <div class="bg-white rounded-lg shadow-md p-8">
        <h2 class="text-2xl font-bold text-gray-900 mb-6">Nous contacter</h2>

        @if($showSuccess)
            <div class="mb-6 p-4 bg-green-100 border border-green-400 text-green-700 rounded-md">
                <div class="flex justify-between items-center">
                    <span>✅ Votre message a été envoyé avec succès ! Nous vous répondrons rapidement.</span>
                    <button wire:click="hideSuccess" class="text-green-700 hover:text-green-900">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>
        @endif

        <form wire:submit="submit" class="space-y-6">
            <!-- Nom -->
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                    Nom complet *
                </label>
                <input
                    type="text"
                    id="name"
                    wire:model="name"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('name') border-red-500 @enderror"
                    placeholder="Votre nom complet"
                >
                @error('name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Email -->
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                    Adresse email *
                </label>
                <input
                    type="email"
                    id="email"
                    wire:model="email"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('email') border-red-500 @enderror"
                    placeholder="votre@email.com"
                >
                @error('email')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Sujet -->
            <div>
                <label for="subject" class="block text-sm font-medium text-gray-700 mb-2">
                    Sujet *
                </label>
                <input
                    type="text"
                    id="subject"
                    wire:model="subject"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('subject') border-red-500 @enderror"
                    placeholder="Sujet de votre message"
                >
                @error('subject')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Message -->
            <div>
                <label for="message" class="block text-sm font-medium text-gray-700 mb-2">
                    Message *
                </label>
                <textarea
                    id="message"
                    wire:model="message"
                    rows="5"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('message') border-red-500 @enderror"
                    placeholder="Votre message (minimum 10 caractères)"
                ></textarea>
                @error('message')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Bouton de soumission -->
            <div>
                <button
                    type="submit"
                    wire:loading.attr="disabled"
                    class="w-full bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed transition duration-200"
                >
                    <span wire:loading.remove>Envoyer le message</span>
                    <span wire:loading class="flex items-center justify-center">
                        <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Envoi en cours...
                    </span>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('livewire:init', () => {
        Livewire.on('contact-sent', () => {
            setTimeout(() => {
                Livewire.dispatch('hideSuccess');
            }, 5000);
        });
    });
</script>
