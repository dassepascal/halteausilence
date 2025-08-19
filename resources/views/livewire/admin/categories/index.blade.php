<?php

use Livewire\Volt\Component;
use App\Models\Category;
use Livewire\WithPagination;
use Livewire\Attributes\{Layout,Validate};
use Illuminate\Support\Str;
use Mary\Traits\Toast;

new #[Layout('components.layouts.admin')] 
class extends Component {
    use Toast, WithPagination;

    public array $sortBy = ['column' => 'title', 'direction' => 'asc'];
     #[Validate('required|max:255|unique:categories,title')]
	public string $title = '';

    #[Validate('required|max:255|unique:posts,slug|regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/')]
    public string $slug = '';

    public function updatedTitle($value): void
	{
		$this->generateSlug($value);
	}

    private function generateSlug(string $title): void
	{
		$this->slug = Str::of($title)->slug('-');
	}

    public function save(): void
	{
		$data = $this->validate();
		Category::create($data);
		$this->success(__('Category created with success.'));
	}

    public function headers(): array
	{
		return [['key' => 'title', 'label' => __('Title')], ['key' => 'slug', 'label' => 'Slug']];
	}
    public function delete(Category $category): void
{
    $category->delete();
    $this->success(__('Category deleted with success.'));
}

	public function with(): array
	{
		return [
			'categories' => Category::orderBy(...array_values($this->sortBy))->paginate(10),
			'headers'    => $this->headers(),
		];
	}

}; ?>

<div>
    <x-header title="{{ __('Categories') }}" separator progress-indicator>
        <x-slot:actions class="lg:hidden">
            <x-button icon="s-building-office-2" label="{{ __('Dashboard') }}" class="btn-outline"
                link="{{ route('admin') }}" />
        </x-slot:actions>
    </x-header>
    <x-card>
        <x-table striped :headers="$headers" :rows="$categories" :sort-by="$sortBy" link="/admin/categories/{id}/edit"
            with-pagination>
            @scope('actions', $category)
        <x-popover>
            <x-slot:trigger>
                <x-button icon="o-trash" wire:click="delete({{ $category->id }})"
                    wire:confirm="{{ __('Are you sure to delete this category?') }}" spinner
                    class="text-red-500 btn-ghost btn-sm" />
            </x-slot:trigger>
            <x-slot:content class="pop-small">
                @lang('Delete')
            </x-slot:content>
        </x-popover>
    @endscope
        </x-table>
    </x-card>
     <x-card title="{{ __('Create a new category') }}">
        @include('livewire.admin.categories.category-form')
    </x-card>
</div>
