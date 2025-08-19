<?php

use App\Models\{Category, Post};
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Attributes\{Layout, Title};
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\Volt\Component;
use Livewire\WithFileUploads;
use Mary\Traits\Toast;

new #[Title('Edit Post'), Layout('components.layouts.admin')]
class extends Component {
	use WithFileUploads, Toast;

	public int $postId;
	public ?Collection $categories;
	public int $category_id;
	public Post $post;
	public string $body                  = '';
	public string $title                 = '';
	public string $slug                  = '';
	public bool $active                  = false;
	public bool $pinned                  = false;
	public string $seo_title             = '';
	public string $meta_description      = '';
	public string $meta_keywords         = '';
	public ?TemporaryUploadedFile $photo = null;

	public function mount(Post $post): void
	{
		if (Auth()->user()->isRedac() && $post->user_id !== Auth()->id()) {
			abort(403);
		}

		$this->post = $post;
		$this->fill($this->post);
		$this->categories = Category::orderBy('title')->get();
	}

    public function updatedTitle($value)
	{
        $this->slug      = Str::slug($value);
        $this->seo_title = $value;
	}

	public function save()
	{
		$data = $this->validate([
			'title'            => 'required|string|max:255',
			'body'             => 'required|string|max:16777215',
			'category_id'      => 'required',
			'photo'            => 'nullable|image|max:2000',
			'active'           => 'required',
			'pinned'           => 'required',
			'slug'             => ['required', 'string', 'max:255', 'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/', Rule::unique('posts')->ignore($this->post->id)],
			'seo_title'        => 'required|max:70',
			'meta_description' => 'required|max:160',
			'meta_keywords'    => 'required|regex:/^[\p{L}\p{M}0-9\s\'\-\_]{1,50}(,[\p{L}\p{M}0-9\s\'\-\_]{1,50})*$/u',
		]);


		if ($this->photo) {
			$date          = now()->format('Y/m');
			$path          = $date . '/' . basename($this->photo->store('photos/' . $date, 'public'));
			$data['image'] = $path;
		}

		//$data['body'] = replaceAbsoluteUrlsWithRelative($data['body']);

		$this->post->update(
			$data + [
				'category_id' => $this->category_id,
			],
		);

		$this->success(__('Post updated with success.'));
	}
}; ?>


<div>
    <x-header title="{{ __('Edit a post') }}" separator progress-indicator>
        <x-slot:actions>
            <x-button icon="s-building-office-2" label="{{ __('Dashboard') }}" class="btn-outline lg:hidden"
                link="{{ route('admin') }}" />
        </x-slot:actions>
    </x-header>

    <x-card>
        <x-form wire:submit="save">
           	<x-select label="{{ __('Category') }}" option-label="title" :options="$categories" wire:model="category_id"
                wire:change="$refresh" />
			<br>
            <div class="flex gap-6">
                <x-checkbox label="{{ __('Published') }}" wire:model="active" />
                <x-checkbox label="{{ __('Pinned') }}" wire:model="pinned" />
            </div>
            <x-input type="text" wire:model="title" label="{{ __('Title') }}"
                placeholder="{{ __('Enter the title') }}" wire:change="$refresh" />
            <x-input type="text" wire:model="slug" label="{{ __('Slug') }}" />

            <x-editor wire:model="body" label="{{ __('Content') }}" :config="config('tinymce.config')"
                folder="{{ 'photos/' . now()->format('Y/m') }}" />
            <x-card title="{{ __('SEO') }}" shadow separator>
                <x-input placeholder="{{ __('Title') }}" wire:model="seo_title" hint="{{ __('Max 70 chars') }}" />
                <br>
                <x-textarea label="{{ __('META Description') }}" wire:model="meta_description"
                    hint="{{ __('Max 160 chars') }}" rows="2" inline />
                <br>
                <x-textarea label="{{ __('META Keywords') }}" wire:model="meta_keywords"
                    hint="{{ __('Keywords separated by comma') }}" rows="1" inline />
            </x-card>
            <x-file wire:model="photo" label="{{ __('Featured image') }}"
                hint="{{ __('Click on the image to modify') }}" accept="image/png, image/jpeg">
                <img src="{{ asset('storage/photos/' . $post->image) }}" class="h-40" />
            </x-file>
            <x-slot:actions>
                <x-button label="{{ __('Preview') }}" icon="m-sun" link="{{ '/posts/' . $post->slug }}" external
                    class="btn-outline" />
                <x-button label="{{ __('Save') }}" icon="o-paper-airplane" spinner="save" type="submit"
                    class="btn-primary" />
            </x-slot:actions>
        </x-form>
    </x-card>
</div>
