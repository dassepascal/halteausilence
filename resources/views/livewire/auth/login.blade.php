<?php

use Livewire\Attributes\{Layout, Validate, Title};
use Livewire\Volt\Component;

new #[Title('Login')] #[Layout('components.layouts.auth')] 
class extends Component {

    #[Validate('required|email')]
    public string $email = '';

    #[Validate('required')]
    public string $password = '';

    public function login()
    {
        $credentials = $this->validate();

		if (auth()->attempt($credentials)) {
			// Régénération de la session
			request()->session()->regenerate();

			if (auth()->user()->isAdmin()) {
				return redirect()->intended('/admin/dashboard');
			}

			// Redirection vers la page d'origine ou la page d'accueil
			return redirect()->intended('/');
		}

        $this->addError('email', __('The provided credentials do not match our records.'));
    }
}; ?>

<div>
    <x-card class="flex items-center justify-center h-screen" title="{{ __('Login') }}" shadow separator
        progress-indicator>
        <x-form wire:submit="login">
            <x-input label="{{ __('E-mail') }}" wire:model="email" icon="o-envelope" type="email" inline required />
            <x-input label="{{ __('Password') }}" wire:model="password" type="password" icon="o-key" type="password"
                inline required />
            <x-checkbox label="{{ __('Remember me') }}" wire:model="remember" />
            <x-slot:actions>
                <div class="flex flex-col space-y-2 flex-end sm:flex-row sm:space-y-0 sm:space-x-2">
                    <x-button label="{{ __('Login') }}" type="submit" icon="o-paper-airplane"
                        class="ml-2 btn-primary sm:order-1" />
                    <div class="flex flex-col space-y-2 flex-end sm:flex-row sm:space-y-0 sm:space-x-2">
                        <x-button label="{{ __('Forgot your password?') }}" class="btn-ghost" link="/forgot-password" />
                        <x-button label="{{ __('Create an account') }}" class="btn-ghost" link="/register" />
                    </div>
                </div>
            </x-slot:actions>
        </x-form>
    </x-card>
</div>
