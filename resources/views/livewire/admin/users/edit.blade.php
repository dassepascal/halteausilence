<?php

use App\Models\User;
use Illuminate\Validation\Rule;
use Livewire\Attributes\{Layout, Title};
use Livewire\Volt\Component;
use Mary\Traits\Toast;

new #[Title('Edit User'), Layout('components.layouts.admin')] 
class extends Component {
	use Toast;

	public User $user;
	public string $name      = '';
	public string $email     = '';
	public string $role      = '';
	public bool $valid       = false;
	public bool $isStudent;

	public function mount(User $user): void
	{
		$this->user = $user;
		$this->fill($this->user);
	}

	public function save()
	{
		$data = $this->validate([
			'name'      => ['required', 'string', 'max:255'],
			'email'     => ['required', 'email', Rule::unique('users')->ignore($this->user->id)],
			'role'      => ['required', Rule::in(['admin', 'redac', 'user'])],
			'valid'     => ['required', 'boolean'],
		]);

		$this->user->update($data);

		$this->success(__('User edited with success.'), redirectTo: '/admin/users/index');
	}

	public function with(): array
	{
		return [
			'roles' => [['name' => __('Administrator'), 'id' => 'admin'], ['name' => __('Redactor'), 'id' => 'redac'], ['name' => __('User'), 'id' => 'user']],
		];
	}
}; ?>

<diV>
    <x-header title="{{ __('Edit an account') }}" separator progress-indicator>
        <x-slot:actions>
            <x-button icon="s-building-office-2" label="{{ __('Dashboard') }}" class="btn-outline lg:hidden"
                link="{{ route('admin') }}" />
        </x-slot:actions>
    </x-header>
    <x-card>
        <x-form wire:submit="save">
            <x-input label="{{ __('Name') }}" wire:model="name" icon="o-user" inline />
            <x-input label="{{ __('E-mail') }}" wire:model="email" icon="o-envelope" inline />
            <br>
            <x-radio label="{{ __('User role') }}" inline label="{{ __('Select a role') }}" :options="$roles"
                wire:model="role" />
            <br>
            <x-toggle label="{{ __('Valid user') }}" inline wire:model="valid" />
            <x-slot:actions>
                <div class="text-right">
                    <x-button label="{{ __('Save') }}" icon="o-paper-airplane" spinner="save" type="submit"
                    class="btn-primary" />
                </div>
            </x-slot:actions>            
        </x-form>
    </x-card>
</diV>
