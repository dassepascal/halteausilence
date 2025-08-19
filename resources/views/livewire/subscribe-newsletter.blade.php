<?php

use Livewire\Volt\Component;
use App\Models\Subscriber;

new class extends Component {
    public string $email = '';
    public string $message = '';

    protected $rules = [
        'email' => 'required|email|unique:subscribers,email',
    ];

    public function submit()
    {
        $this->validate();

        $token = bin2hex(random_bytes(16));
        $subscriber = Subscriber::create([
            'email' => $this->email,
            'confirmation_token' => $token,
            // 'confirmed' => false par défaut
        ]);

        // Envoi d’un mail de confirmation
        // Mail::to($this->email)->send(new ConfirmNewsletter($token));

        $this->message = 'Merci ! Vérifie tes emails pour confirmer l’inscription.';
        $this->email = '';
    }
};
?>

<div>
    <form wire:submit.prevent="submit">
        <input wire:model="email" type="email" placeholder="Entrez votre email" />
        <button type="submit">S’abonner</button>
        <p>{{ $message }}</p>
        <p style="color: red;">{{ $errors->first('email') }}</p>
    </form>
</div>
