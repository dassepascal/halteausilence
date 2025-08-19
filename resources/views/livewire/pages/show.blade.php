<?php

use App\Models\Page;
use Livewire\Volt\Component;

new class extends Component {
    public Page $page;

    public function mount(Page $page): void
    {
        if (!$page->active) {
            abort(404);
        }

        $this->page = $page;
    }
}; ?>

<div>
    @section('title', $page->seo_title ?? $page->title)
    @section('description', $page->meta_description)
    @section('keywords', $page->meta_keywords)

    <div class="flex justify-end gap-4">
        @auth
            @if (Auth::user()->isAdmin())
                <x-popover>
                    <x-slot:trigger>
                        <x-button icon="c-pencil-square" link="#" spinner class="btn-ghost btn-sm" />
                    </x-slot:trigger>
                    <x-slot:content class="pop-small">
                        @lang('Edit this page')
                    </x-slot:content>
                </x-popover>
            @endif
        @endauth
    </div>

    <x-header title="{!! $page->title !!}" />

    <div class="relative items-center w-full px-5 py-5 mx-auto prose md:px-12 max-w-7xl">
        {!! $page->body !!}
    </div>
    {{-- <div class="flex justify-between">
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
        @if ($commentsCount > 0)
            <div class="flex justify-center">
                <x-button label="{{ $commentsCount > 1 ? __('View comments') : __('View comment') }}"
                    wire:click="showComments" class="btn-outline" spinner />
            </div>
        @endif
    </div> --}}
    
</div>
