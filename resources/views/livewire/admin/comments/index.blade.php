<?php

use App\Models\Comment;
use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\Attributes\{Layout, Title};
use Livewire\Volt\Component;
use Livewire\WithPagination;
use Mary\Traits\Toast;

new #[Title('Comments'), Layout('components.layouts.admin')] class extends Component {
    use Toast, WithPagination;

    public string $search = '';
    public array $sortBy = ['column' => 'created_at', 'direction' => 'desc'];
    public $role = 'all';

    public function deleteComment(Comment $comment): void
    {
        $comment->delete();
        $this->success(__('Comment deleted'));
    }

    public function validComment(Comment $comment): void
    {
        $comment->user->valid = true;
        $comment->user->save();

        $this->success(__('Comment validated'));
    }

    public function headers(): array
    {
        return [['key' => 'user_name', 'label' => __('Author')], ['key' => 'body', 'label' => __('Comment'), 'sortable' => false], ['key' => 'post_title', 'label' => __('Post')], ['key' => 'created_at', 'label' => __('Sent on')]];
    }

    public function comments(): LengthAwarePaginator
    {
        return Comment::query()
            ->when($this->search, fn($q) => $q->where('body', 'like', "%{$this->search}%"))
            ->when('post_title' === $this->sortBy['column'], fn($q) => $q->join('posts', 'comments.post_id', '=', 'posts.id')->orderBy('posts.title', $this->sortBy['direction']), fn($q) => $q->orderBy($this->sortBy['column'], $this->sortBy['direction']))
            ->when(Auth::user()->isRedac(), fn($q) => $q->whereRelation('post', 'user_id', Auth::id()))
            ->with(['user:id,name,email,valid', 'post:id,title,slug,user_id'])
            ->withAggregate('user', 'name')
            ->paginate(10);
    }

    public function with(): array
    {
        return [
            'headers' => $this->headers(),
            'comments' => $this->comments(),
        ];
    }
}; ?>

<div>
    <x-header title="{{ __('Comments') }}" separator progress-indicator>
        <x-slot:actions>
            <x-button icon="s-building-office-2" label="{{ __('Dashboard') }}" class="btn-outline lg:hidden"
                link="{{ route('admin') }}" />
            <x-input placeholder="{{ __('Search') }}" wire:model.live.debounce="search" clearable
                icon="o-magnifying-glass" />
        </x-slot:actions>
    </x-header>
    <x-card>
        <x-table striped :headers="$headers" :rows="$comments" link="/admin/comments/{id}/edit" :sort-by="$sortBy"
            with-pagination>
            @scope('cell_created_at', $comment)
                {{ $comment->created_at->isoFormat('LL') }} {{ __('at') }}
                {{ $comment->created_at->isoFormat('HH:mm') }}
            @endscope
            @scope('cell_body', $comment)
                {!! nl2br($comment->body) !!}
            @endscope
            @scope('cell_user_name', $comment)
                <x-avatar :image="Gravatar::get($comment->user->email)">
                    <x-slot:title>
                        {{ $comment->user->name }}
                    </x-slot:title>
                </x-avatar>
            @endscope
            @scope('cell_post_title', $comment)
                {{ $comment->post->title }}
            @endscope
            @scope('actions', $comment)
                <div class="flex">
                    @if (!$comment->user->valid)
                        <x-popover>
                            <x-slot:trigger>
                                <x-button icon="c-eye" wire:click="validComment({{ $comment->id }})"
                                    wire:confirm="{{ __('Are you sure to validate this user for comment?') }}" spinner
                                    class="text-yellow-500 btn-ghost btn-sm" />
                            </x-slot:trigger>
                            <x-slot:content class="pop-small">
                                @lang('Validate the user')
                            </x-slot:content>
                        </x-popover>
                    @endif
                    <x-popover>
                        <x-slot:trigger>
                            <x-button icon="c-eye" link="{{ route('comments.edit', $comment->id) }}" spinner
                                class="btn-ghost btn-sm" />
                        </x-slot:trigger>
                        <x-slot:content class="pop-small">
                            @lang('Edit or answer')
                        </x-slot:content>
                    </x-popover>
                    <x-popover>
                        <x-slot:trigger>
                            <x-button icon="s-document-text" link="{{ route('posts.show', $comment->post->slug) }}" spinner
                                class="btn-ghost btn-sm" />
                        </x-slot:trigger>
                        <x-slot:content class="pop-small">
                            @lang('Show post')
                        </x-slot:content>
                    </x-popover>
                    <x-popover>
                        <x-slot:trigger>
                            <x-button icon="o-trash" wire:click="deleteComment({{ $comment->id }})"
                                wire:confirm="{{ __('Are you sure to delete this comment?') }}" spinner
                                class="text-red-500 btn-ghost btn-sm" />
                        </x-slot:trigger>
                        <x-slot:content class="pop-small">
                            @lang('Delete')
                        </x-slot:content>
                    </x-popover>
                </div>
            @endscope
        </x-table>
    </x-card>
</div>
