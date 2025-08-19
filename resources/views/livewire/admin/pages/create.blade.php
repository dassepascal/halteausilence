<?php

use App\Models\Page;
use illuminate\Support\Str;
use Livewire\Attributes\{Layout, Validate, Title};
use Livewire\Volt\Component;
use Mary\Traits\Toast;

new #[Title('Create Page'), Layout('components.layouts.admin')] 
class extends Component {
	use Toast;

	#[Validate('required|max:65000')]
	public string $body = '';

	#[Validate('required|max:255')]
	public string $title = '';

	#[Validate('required|max:255|unique:posts,slug|regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/')]
	public string $slug = '';

	#[Validate('required')]
	public bool $active = false;

	#[Validate('required|max:70')]
	public string $seo_title = '';

	#[Validate('required|max:160')]
	public string $meta_description = '';

	#[Validate('required|regex:/^[A-Za-z0-9-éèàù]{1,50}?(,[A-Za-z0-9-éèàù]{1,50})*$/')]
	public string $meta_keywords = '';

	public function updatedTitle($value): void
	{
		$this->generateSlug($value);

		$this->seo_title = $value;
	}

	public function save()
	{
		$data = $this->validate();
		Page::create($data);

		$this->success(__('Page added with success.'), redirectTo: '/admin/pages/index');
	}

	private function generateSlug(string $title): void
	{
		$this->slug = Str::of($title)->slug('-');
	}
}; ?>

<div>
    <x-header title="{{ __('Add a page') }}" separator progress-indicator>
        <x-slot:actions class="lg:hidden">
            <x-button icon="s-building-office-2" label="{{ __('Dashboard') }}" class="btn-outline"
                link="{{ route('admin') }}" />
        </x-slot:actions>
    </x-header>
    @include('livewire.admin.pages.page-form')
</div>
