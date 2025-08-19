<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\{Layout, Validate, Title};
use Livewire\Volt\Component;
use App\Notifications\UserRegistered;
use Mary\Traits\Toast;

new #[Title('Register')] #[Layout('components.layouts.auth')] 
class extends Component {

    use Toast;

    #[Validate('required|string|max:255|unique:users')]
    public string $name = '';

    #[Validate('required|email|unique:users')]
    public string $email = '';

    #[Validate('required|confirmed')]
    public string $password = '';

    #[Validate('required')]
    public string $password_confirmation = '';

    #[Validate('sometimes|nullable')]
    public ?string $gender = null;

    public function register()
    {
        if ($this->gender) {
            abort(403);
        }

        $data = $this->validate();

        $user = $this->createUser($data);

        auth()->login($user);

        request()->session()->regenerate();

        $this->success(__('Registration successful!'), redirectTo: '/');
    }

    protected function createUser(array $data): User
    {
        $data['password'] = Hash::make($data['password']);
        return User::create($data);
    }

}; ?>

<div>
    <x-card class="flex items-center justify-center h-screen" title="{{ __('Register') }}" shadow separator
        progress-indicator>

        <x-form wire:submit="register" class="w-full sm:min-w-[30vw]">
            <x-input label="{{ __('Name') }}" wire:model="name" icon="o-user" inline required />
            <x-input label="{{ __('E-mail') }}" wire:model="email" icon="o-envelope" inline required />
            <x-input label="{{ __('Password') }}" wire:model="password" type="password" icon="o-key" inline required />
            <x-input label="{{ __('Confirm Password') }}" wire:model="password_confirmation" type="password"
                icon="o-key" inline required />
            <div style="display: none;">
                <x-input wire:model="gender" type="text" inline />
            </div>
            <x-slot:actions>
                <x-button label="{{ __('Already registered?') }}" class="btn-ghost" link="/login" />
                <x-button label="{{ __('Register') }}" type="submit" icon="o-paper-airplane" class="btn-primary"
                    spinner="login" />
            </x-slot:actions>
        </x-form>

    </x-card>
</div>
