<?php

use App\Models\Post;
use App\Repositories\PostRepository;
use Livewire\Volt\Component;
use Illuminate\Support\Collection;

new class extends Component {

    public Post $post;
    public int $commentsCount;
    public Collection $comments;
    public bool $listComments = false;

    public function mount($slug): void
    {
        $postRepository = new PostRepository();
        $this->post = $postRepository->getPostBySlug($slug);
        $this->commentsCount = $this->post->valid_comments_count;
    }

    public function showComments(): void
    {
        //dd($this->post->body);
        $this->listComments = true;

        $this->comments = $this->post
            ->validComments()
            ->where('parent_id', null)
            ->withCount([
                'children' => function ($query) {
                    $query->whereHas('user', function ($q) {
                        $q->where('valid', true);
                    });
                },
            ])
            ->with([
                'user' => function ($query) {
                    $query->select('id', 'name', 'email', 'role')->withCount('comments');
                },
            ])
            ->latest()
            ->get();
    }
    public function favoritePost(): void
{
    $user = auth()->user();

    if ($user) {
        $user->favoritePosts()->attach($this->post->id);
        $this->post->is_favorited = true;
    }
}

public function unfavoritePost(): void
{
    $user = auth()->user();

    if ($user) {
        $user->favoritePosts()->detach($this->post->id);
        $this->post->is_favorited = false;
    }
}

}; ?>

<div>

    @section('title', $post->seo_title ?? $post->title)
    @section('description', $post->meta_description)
    @section('keywords', $post->meta_keywords)

    <div id="top" class="flex justify-end gap-4">
         @auth
            <x-popover>
                <x-slot:trigger>
                    @if ($post->is_favorited)
                        <x-button icon="s-star" wire:click="unfavoritePost" spinner
                            class="text-yellow-500 btn-ghost btn-sm" />
                    @else
                        <x-button icon="s-star" wire:click="favoritePost" spinner class="btn-ghost btn-sm" />
                    @endif
                </x-slot:trigger>
                <x-slot:content class="pop-small">
                    @if ($post->is_favorited)
                        @lang('Remove from favorites')
                    @else
                        @lang('Bookmark this post')
                    @endif
                </x-slot:content>
            </x-popover>
        @endauth

        <x-popover>
            <x-slot:trigger>
                <x-button class="btn-sm"><a
                        href="{{ url('/category/' . $post->category->slug) }}">{{ $post->category->title }}</a></x-button>
            </x-slot:trigger>
            <x-slot:content class="pop-small">
                @lang('Show this category')
            </x-slot:content>
        </x-popover>
         <x-popover>
            <x-slot:trigger>
                <a href="#bottom"><x-icon name="c-arrow-long-down" /></a>
            </x-slot:trigger>
            <x-slot:content class="pop-small">
                @lang('To bottom')
            </x-slot:content>
        </x-popover>
    </div>

    <x-header title="{!! $post->title !!}" subtitle="{{ ucfirst($post->created_at->isoFormat('LLLL')) }} "
        size="text-2xl sm:text-3xl md:text-4xl" />

    <div class="relative items-center w-full py-5 mx-auto prose md:px-12 max-w-7xl">
        @if ($post->image)
            <div class="flex flex-col items-center mb-4">
                <img src="{{ asset('storage/photos/' . $post->image) }}" />
            </div>
            <br>
        @endif
        <div class="text-justify ">
             {!! $post->body !!}
        </div>
    </div>
    <br>
    <hr>

    <div class="flex justify-between">
        <p>@lang('By ') {{ $post->user->name }}</p>
        <em>
            @if ($commentsCount > 0)
                @lang('Number of comments: ') {{ $commentsCount }}
            @else
                @lang('No comments')
            @endif
        </em>
    </div>

    <div id="bottom" class="relative items-center w-full py-5 mx-auto md:px-12 max-w-7xl">
        @if ($listComments)
            <x-card title="{{ __('Comments') }}" shadow separator>
                @foreach ($comments as $comment)
                    @if (!$comment->parent_id)
                        <livewire:posts.comment :$comment :depth="0" :key="$comment->id" />
                    @endif
                @endforeach
                @auth
                    <livewire:posts.commentBase :postId="$post->id" />
                @endauth
            </x-card>
        @else
            @if ($commentsCount > 0)
                <div class="flex justify-center">
                    <x-button label="{{ $commentsCount > 1 ? __('View comments') : __('View comment') }}"
                        wire:click="showComments" class="btn-outline" spinner />
                </div>
            @else
                @auth
                    <livewire:posts.commentBase :postId="$post->id" />
                @endauth
            @endif
        @endif
    </div>
    <div id="bottom" class="relative flex justify-end w-full py-5 mx-auto md:px-12 max-w-7xl">
        <x-popover>
            <x-slot:trigger>
                <a href="#top"><x-icon name="c-arrow-long-up" />
            </x-slot:trigger>
            <x-slot:content class="pop-small">
                @lang('To up')
            </x-slot:content>
        </x-popover>
    </div>

</div>
