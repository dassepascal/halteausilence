<?php
// resources/views/livewire/admin/contact-list.blade.php

use App\Models\Contact;
use Livewire\Attributes\On;
use Livewire\Volt\Component;
use Livewire\WithPagination;

new class extends Component
{
    use WithPagination;

    public string $search = '';
    public string $filter = 'all'; // all, read, unread
    public ?Contact $selectedContact = null;
    public bool $showModal = false;

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedFilter()
    {
        $this->resetPage();
    }

    public function with(): array
    {
        $query = Contact::query()
            ->when($this->search, function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('email', 'like', '%' . $this->search . '%')
                  ->orWhere('subject', 'like', '%' . $this->search . '%');
            })
            ->when($this->filter === 'read', fn($q) => $q->read())
            ->when($this->filter === 'unread', fn($q) => $q->unread())
            ->latest();

        return [
            'contacts' => $query->paginate(10),
            'unreadCount' => Contact::unread()->count(),
            'totalCount' => Contact::count(),
        ];
    }

    public function viewContact(Contact $contact)
    {
        $this->selectedContact = $contact;
        $this->showModal = true;

        if (!$contact->is_read) {
            $contact->markAsRead();
        }
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->selectedContact = null;
    }

    public function deleteContact(Contact $contact)
    {
        $contact->delete();
        session()->flash('message', 'Message supprimé avec succès.');
    }

    public function markAsRead(Contact $contact)
    {
        $contact->markAsRead();
    }

    public function markAllAsRead()
    {
        Contact::unread()->update(['is_read' => true]);
        session()->flash('message', 'Tous les messages ont été marqués comme lus.');
    }
}; ?>

<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Messages de Contact</h1>
        <div class="flex space-x-2">
            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                Total: {{ $totalCount }}
            </span>
            @if($unreadCount > 0)
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">
                    Non lus: {{ $unreadCount }}
                </span>
            @endif
        </div>
    </div>

    @if(session()->has('message'))
        <div class="mb-6 p-4 bg-green-100 border border-green-400 text-green-700 rounded-md">
            {{ session('message') }}
        </div>
    @endif

    <!-- Filtres et recherche -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between space-y-4 md:space-y-0">
            <div class="flex space-x-4">
                <select wire:model.live="filter" class="px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="all">Tous les messages</option>
                    <option value="unread">Non lus</option>
                    <option value="read">Lus</option>
                </select>

                @if($unreadCount > 0)
                    <button
                        wire:click="markAllAsRead"
                        class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition duration-200"
                    >
                        Marquer tout comme lu
                    </button>
                @endif
            </div>

            <div class="flex-1 max-w-md">
                <input
                    type="text"
                    wire:model.live.debounce.300ms="search"
                    placeholder="Rechercher par nom, email ou sujet..."
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                >
            </div>
        </div>
    </div>

    <!-- Liste des contacts -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        @if($contacts->count() > 0)
            <div class="divide-y divide-gray-200">
                @foreach($contacts as $contact)
                    <div class="p-6 hover:bg-gray-50 {{ !$contact->is_read ? 'bg-blue-50 border-l-4 border-blue-500' : '' }}">
                        <div class="flex items-center justify-between">
                            <div class="flex-1">
                                <div class="flex items-center space-x-3">
                                    <h3 class="text-lg font-medium text-gray-900">
                                        {{ $contact->name }}
                                        @if(!$contact->is_read)
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800 ml-2">
                                                Nouveau
                                            </span>
                                        @endif
                                    </h3>
                                </div>
                                <p class="text-sm text-gray-600 mt-1">{{ $contact->email }}</p>
                                <p class="text-sm font-medium text-gray-900 mt-1">{{ $contact->subject }}</p>
                                <p class="text-sm text-gray-500 mt-2">{{ Str::limit($contact->message, 100) }}</p>
                                <p class="text-xs text-gray-400 mt-2">{{ $contact->created_at->format('d/m/Y à H:i') }}</p>
                            </div>

                            <div class="flex space-x-2">
                                <button
                                    wire:click="viewContact({{ $contact->id }})"
                                    class="px-3 py-1 bg-blue-600 text-white text-sm rounded hover:bg-blue-700 transition duration-200"
                                >
                                    Voir
                                </button>

                                @if(!$contact->is_read)
                                    <button
                                        wire:click="markAsRead({{ $contact->id }})"
                                        class="px-3 py-1 bg-green-600 text-white text-sm rounded hover:bg-green-700 transition duration-200"
                                    >
                                        Marquer lu
                                    </button>
                                @endif

                                <button
                                    wire:click="deleteContact({{ $contact->id }})"
                                    wire:confirm="Êtes-vous sûr de vouloir supprimer ce message ?"
                                    class="px-3 py-1 bg-red-600 text-white text-sm rounded hover:bg-red-700 transition duration-200"
                                >
                                    Supprimer
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="px-6 py-4 bg-gray-50">
                {{ $contacts->links() }}
            </div>
        @else
            <div class="p-8 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2M4 13h2m13-8V4a1 1 0 00-1-1H7a1 1 0 00-1 1v1"></path>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">Aucun message</h3>
                <p class="mt-1 text-sm text-gray-500">Aucun message de contact trouvé.</p>
            </div>
        @endif
    </div>

    <!-- Modal de détail -->
    @if($showModal && $selectedContact)
        <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
            <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-bold text-gray-900">Détail du message</h3>
                    <button wire:click="closeModal" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Nom</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $selectedContact->name }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Email</label>
                        <p class="mt-1 text-sm text-gray-900">
                            <a href="mailto:{{ $selectedContact->email }}" class="text-blue-600 hover:text-blue-800">
                                {{ $selectedContact->email }}
                            </a>
                        </p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Sujet</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $selectedContact->subject }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Message</label>
                        <div class="mt-1 p-3 bg-gray-50 rounded-md">
                            <p class="text-sm text-gray-900 whitespace-pre-wrap">{{ $selectedContact->message }}</p>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Date</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $selectedContact->created_at->format('d/m/Y à H:i:s') }}</p>
                    </div>
                </div>

                <div class="mt-6 flex justify-end space-x-3">
                    <button wire:click="closeModal" class="px-4 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400 transition duration-200">
                        Fermer
                    </button>
                    <a href="mailto:{{ $selectedContact->email }}?subject=Re: {{ $selectedContact->subject }}"
                       class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition duration-200">
                        Répondre
                    </a>
                </div>
            </div>
        </div>
    @endif
</div>
