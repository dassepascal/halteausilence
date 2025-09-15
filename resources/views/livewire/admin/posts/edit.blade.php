<?php

use Livewire\Volt\Component;
use App\Models\{Category, Post};
use Mary\Traits\Toast;
use Livewire\Attributes\{Layout, Validate, Rule, Title};
use Livewire\WithFileUploads;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;


new #[Layout('components.layouts.admin')] class extends Component
{
    use WithFileUploads, Toast;
    #[Rule('required|image|max:2000')]
    public ?TemporaryUploadedFile $photo = null;

    public int $category_id;

    #[Validate('required|string|max:16777215')]
    public string $body = '';

    #[Validate('required|string|max:255')]
    public string $title = '';

    #[Validate('required|max:255|unique:posts,slug|regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/')]
    public string $slug = '';

    #[Validate('required')]
    public bool $active = false;

    #[Validate('required')]
    public bool $pinned = false;

    #[Validate('required|max:70')]
    public string $seo_title = '';

    #[Validate('required|max:160')]
    public string $meta_description = '';

    #[Validate('required|regex:/^[\p{L}\p{M}0-9\s\'\-\_]{1,50}(,[\p{L}\p{M}0-9\s\'\-\_]{1,50})*$/u')]
    public string $meta_keywords = '';

    public ?string $image = null;

    public function mount(): void
    {
        $category = Category::first();
        $this->category_id = $category->id;
    }

    public function updatedTitle($value)
    {
        $this->slug = Str::slug($value);
        $this->seo_title = $value;
    }

    public function save()
    {
        // Valider les données
        $validatedData = $this->validate();
        $date = now()->format('Y/m');

        // Gérer l'upload de la photo
        if ($this->photo) {
            $photoPath = $date . '/' . basename($this->photo->store('photos/' . $date, 'public'));
            $this->image = $photoPath;
        }

        try {
            // Créer le post
            $post = Post::create([
                'title' => $this->title,
                'slug' => $this->slug,
                'body' => $this->body,
                'active' => $this->active,
                'pinned' => $this->pinned,
                'seo_title' => $this->seo_title,
                'meta_description' => $this->meta_description,
                'meta_keywords' => $this->meta_keywords,
                'category_id' => $this->category_id,
                'user_id' => Auth::id(),
                'image' => $this->image, // Ajouter le chemin de l'image si elle existe
            ]);
           // dd($post);

            // Réinitialiser les champs après la sauvegarde
            $this->reset(['title', 'slug', 'body', 'active', 'pinned', 'seo_title', 'meta_description', 'meta_keywords', 'photo','image']);

            // Afficher un message de succès
            $this->success(__('Post added successfully.'));

            // Rediriger vers la liste des posts
            $this->success(__('Menu updated with success.'), redirectTo: '/admin/posts/index');
        } catch (\Exception $e) {
            // Gérer les erreurs

            $this->error(__('An error occurred while saving the post.'));

            // Log l'erreur pour le débogage
            \Log::error('Error saving post: ' . $e->getMessage());
        }
    }

    public function with(): array
    {
        return [
            'categories' => Category::orderBy('title')->get(),
        ];
    }
};
?>

<div>
    <x-header title="{{ __('Add a post') }}" separator progress-indicator>
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
                <x-checkbox label="{{ __('Published') }}" wire:model="active" value="1" />
                <x-checkbox label="{{ __('Pinned') }}" wire:model="pinned" value="1" />
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
            <hr>
            <x-file wire:model="photo" label="{{ __('Featured image') }}"
                hint="{{ __('Click on the image to modify') }}" accept="image/png, image/jpeg">
                <img src="{{ $photo == '' ? '/storage/ask.jpg' : $photo }}" class="h-40" />
            </x-file>
            <x-slot:actions>
                <x-button label="{{ __('Save') }}" icon="o-paper-airplane" spinner="save" type="submit"
                    class="btn-primary" />
            </x-slot:actions>
        </x-form>
    </x-card>
</div>
