<?php

use Livewire\Volt\Component;
use Livewire\WithPagination;
use App\Models\Newsletter;
use App\Models\NewsletterCategory;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| Newsletter Manager Volt Component
|--------------------------------------------------------------------------
|
| Placez ce fichier (par ex.) dans:
| resources/views/livewire/newsletter-manager.volt.php
| Vous profitez ainsi de Volt : logique de composant et template Blade
| dans un seul fichier extrêmement lisible.
*/

new class extends Component
{
    use WithPagination;

    // État de formulaire & UI
    public string $title = '';
    public string $subject = '';
    public string $content = '';
    public string $status = 'draft';
    public ?string $scheduled_at = '';
    public array $selectedCategories = [];
    public ?int $editingId = null;
    public bool $showModal = false;

    // Filtres & recherche
    public string $search = '';
    public string $statusFilter = 'all';

    // Validation
    protected array $rules = [
        'title'             => 'required|string|max:255',
        'subject'           => 'required|string|max:255',
        'content'           => 'required|string',
        'status'            => 'required|in:draft,scheduled,sent',
        'scheduled_at'      => 'nullable|date|after:now',
        'selectedCategories'=> 'array',
    ];

    public function mount(): void
    {
        if (! Auth::user()?->canManageNewsletters()) {
            abort(403, 'Accès non autorisé');
        }
    }

    /**
     * Données envoyées au template.
     */
    public function with(): array
    {
        $newsletters = Newsletter::query()
            ->when($this->search, fn($q) => $q->where('title', 'like', "%{$this->search}%")
                                             ->orWhere('subject', 'like', "%{$this->search}%"))
            ->when($this->statusFilter !== 'all', fn($q) => $q->where('status', $this->statusFilter))
            ->with(['creator','categories'])
            ->orderByDesc('created_at')
            ->paginate(10);

        $categories = NewsletterCategory::active()->get();

        // Correction ici : compter les utilisateurs abonnés et valides
        $subscribersCount = User::query()
            ->where('newsletter', true)
            ->where('valid', true)
            ->count();

        return compact('newsletters','categories','subscribersCount');
    }

    /* ---------------------------------------------------------------------
    | Actions CRUD & Business
    |---------------------------------------------------------------------*/

    public function openModal(?int $id = null): void
    {
        $this->resetValidation();
        $this->reset(['title','subject','content','status','scheduled_at','selectedCategories']);

        if ($id) {
            $n = Newsletter::with('categories')->findOrFail($id);
            $this->editingId          = $n->id;
            $this->title              = $n->title;
            $this->subject            = $n->subject;
            $this->content            = $n->content;
            $this->status             = $n->status;
            $this->scheduled_at       = optional($n->scheduled_at)->format('Y-m-d\\TH:i');
            $this->selectedCategories = $n->categories->pluck('id')->toArray();
        } else {
            $this->editingId = null;
        }

        $this->showModal = true;
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->reset(['title','subject','content','status','scheduled_at','selectedCategories','editingId']);
        $this->resetValidation();
    }

    public function save(): void
    {
        $this->validate();

        $data = [
            'title'        => $this->title,
            'subject'      => $this->subject,
            'content'      => $this->content,
            'status'       => $this->status,
            'scheduled_at' => $this->scheduled_at ?: null,
        ];

        $newsletter = $this->editingId
            ? tap(Newsletter::findOrFail($this->editingId))->update($data)
            : Newsletter::create($data + ['created_by' => Auth::id()]);

        $newsletter->categories()->sync($this->selectedCategories);

        session()->flash('message', 'Newsletter '.($this->editingId ? 'mise à jour' : 'créée').' avec succès !');
        $this->closeModal();
    }

    public function delete(int $id): void
    {
        $n = Newsletter::findOrFail($id);
        if ($n->status === 'sent') {
            session()->flash('error', 'Impossible de supprimer une newsletter déjà envoyée.');
            return;
        }
        $n->delete();
        session()->flash('message', 'Newsletter supprimée avec succès !');
    }

    public function sendNewsletter(int $id): void
    {
        $n = Newsletter::findOrFail($id);
        if ($n->status === 'sent') {
            session()->flash('error', 'Cette newsletter a déjà été envoyée.');
            return;
        }

        // Correction ici : récupérer les utilisateurs abonnés et valides
        $subs = User::where('newsletter', true)
            ->where('valid', true)
            ->get();

        foreach ($subs as $user) {
            $n->subscribers()->attach($user->id, ['sent_at' => now()]);
        }

        $n->update([
            'status'     => 'sent',
            'sent_at'    => now(),
            'sent_count' => $subs->count(),
        ]);
        session()->flash('message', "Newsletter envoyée à {$subs->count()} abonnés !");
    }

    public function duplicate(int $id): void
    {
        $n = Newsletter::with('categories')->findOrFail($id);
        $new = Newsletter::create([
            'title'      => $n->title.' (Copie)',
            'subject'    => $n->subject,
            'content'    => $n->content,
            'status'     => 'draft',
            'created_by' => Auth::id(),
        ]);
        $new->categories()->sync($n->categories->pluck('id'));
        session()->flash('message', 'Newsletter dupliquée avec succès !');
    }

    /* ---------------------------------------------------------------------
    | Hooks pour les filtres
    |---------------------------------------------------------------------*/
    public function updatedSearch(): void { $this->resetPage(); }
    public function updatedStatusFilter(): void { $this->resetPage(); }
}
?>

{{-- ---------------------------------------------------------------------------
| Template Blade/HTML
--------------------------------------------------------------------------- --}}
<div class="p-6">
    {{-- Header --}}
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Gestion des Newsletters</h1>
        <button wire:click="openModal" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">
            Nouvelle Newsletter
        </button>
    </div>

    {{-- Statistiques --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <x-stat-card title="Abonnés" :value="$subscribersCount" icon="users" colour="blue" />
        <x-stat-card title="Newsletters envoyées" :value="$newsletters->where('status','sent')->count()" icon="mail" colour="green" />
        <x-stat-card title="Brouillons" :value="$newsletters->where('status','draft')->count()" icon="file" colour="yellow" />
    </div>

    {{-- Filtres --}}
    <div class="bg-white rounded-lg shadow p-4 mb-6">
        <div class="flex flex-col md:flex-row gap-4">
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Rechercher..." class="flex-1 px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500" />
            <select wire:model.live="statusFilter" class="px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500">
                <option value="all">Tous les statuts</option>
                <option value="draft">Brouillons</option>
                <option value="scheduled">Programmées</option>
                <option value="sent">Envoyées</option>
            </select>
        </div>
    </div>

    {{-- Flash messages --}}
    @foreach (['message' => 'green', 'error' => 'red'] as $msg => $color)
        @if (session()->has($msg))
            <div class="bg-{{ $color }}-100 border border-{{ $color }}-400 text-{{ $color }}-700 px-4 py-3 rounded mb-4">
                {{ session($msg) }}
            </div>
        @endif
    @endforeach

    {{-- Table des newsletters --}}
    <div class="bg-white rounded-lg shadow">
        <div class="overflow-x-auto">
            <table class="w-full table-auto">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Newsletter</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Statut</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Créateur</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($newsletters as $n)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $n->title }}</div>
                                <div class="text-sm text-gray-500">{{ $n->subject }}</div>
                                @if ($n->categories->count())
                                    <div class="mt-1 space-x-1">
                                        @foreach ($n->categories as $cat)
                                            <span class="inline-block bg-gray-100 text-gray-800 text-xs px-2 py-1 rounded">{{ $cat->name }}</span>
                                        @endforeach
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php $color = match($n->status) {
                                    'draft'     => 'yellow',
                                    'scheduled' => 'blue',
                                    default     => 'green'
                                }; @endphp
                                <span class="px-2 py-1 text-xs rounded-full bg-{{ $color }}-100 text-{{ $color }}-800">
                                    {{ ucfirst($n->status) }}
                                </span>
                                @if ($n->status === 'sent')
                                    <div class="text-xs text-gray-500 mt-1">{{ $n->sent_count }} envois</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $n->creator->name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $n->created_at->format('d/m/Y H:i') }}
                                @if($n->scheduled_at)
                                    <div class="text-xs text-blue-600">Programmée: {{ $n->scheduled_at->format('d/m/Y H:i') }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                <button wire:click="openModal({{ $n->id }})" class="text-blue-600 hover:text-blue-900">Modifier</button>
                                @if($n->status !== 'sent')
                                    <button wire:click="sendNewsletter({{ $n->id }})" class="text-green-600 hover:text-green-900" wire:confirm="Envoyer cette newsletter ?">Envoyer</button>
                                @endif
                                <button wire:click="duplicate({{ $n->id }})" class="text-purple-600 hover:text-purple-900">Dupliquer</button>
                                @if($n->status !== 'sent')
                                    <button wire:click="delete({{ $n->id }})" class="text-red-600 hover:text-red-900" wire:confirm="Supprimer cette newsletter ?">Supprimer</button>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-4 text-center text-gray-500">Aucune newsletter trouvée.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4">{{ $newsletters->links() }}</div>
    </div>

    {{-- Modal de création / édition --}}
    @if ($showModal)
        <div class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-start justify-center pt-20 z-50">
            <div class="bg-white w-11/12 md:w-3/4 lg:w-1/2 rounded-lg shadow-lg p-6 relative">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-medium">{{ $editingId ? 'Modifier' : 'Nouvelle' }} newsletter</h3>
                    <button wire:click="closeModal" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <form wire:submit.prevent="save" class="space-y-4">
                    <x-input-group label="Titre" model="title" required />
                    <x-input-group label="Sujet" model="subject" required />

                    @if($categories->count())
                        <div>
                            <label class="block text-sm font-medium mb-1">Catégories</label>
                            <div class="grid grid-cols-2 md:grid-cols-3 gap-2">
                                @foreach($categories as $cat)
                                    <label class="flex items-center space-x-2 text-sm">
                                        <input type="checkbox" wire:model="selectedCategories" value="{{ $cat->id }}" class="rounded border-gray-300" />
                                        <span>{{ $cat->name }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <x-textarea-group label="Contenu" model="content" rows="10" required />

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium mb-1">Statut</label>
                            <select wire:model="status" class="w-full px-3 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500">
                                <option value="draft">Brouillon</option>
                                <option value="scheduled">Programmée</option>
                            </select>
                        </div>
                        @if($status === 'scheduled')
                            <x-input-group type="datetime-local" label="Date de programmation" model="scheduled_at" />
                        @endif
                    </div>

                    <div class="flex justify-end space-x-3 pt-4">
                        <button type="button" wire:click="closeModal" class="px-4 py-2 border rounded-lg text-gray-600 hover:bg-gray-50">Annuler</button>
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">{{ $editingId ? 'Mettre à jour' : 'Créer' }}</button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
