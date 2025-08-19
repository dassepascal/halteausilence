<?php

use Livewire\Attributes\Validate;
use Livewire\Volt\Component;

new class() extends Component {

	#[Validate('required|string|max:100')]
	public string $search = '';

	public function save()
	{
		$data = $this->validate();

		return redirect('/search/' . $data['search']);
	}
};
?>

<div>
    <form wire:submit.prevent="save">
        <x-input placeholder="{{ __('Search') }}..." wire:model="search" clearable icon="o-magnifying-glass" />
    </form>
</div>
