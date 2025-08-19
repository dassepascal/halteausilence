<?php

use App\Models\{ Comment, Reaction };
use App\Notifications\{CommentAnswerCreated, CommentCreated};
use Illuminate\Support\Collection;
use Livewire\Attributes\Validate;
use Livewire\Volt\Component;

new class() extends Component {
	public ?Comment $comment;
	public ?Collection $children;
	public bool $showAnswerForm = false;
	public bool $showModifyForm = false;
	public int $depth;
	public bool $alert = false;
    public int $children_count = 0;

	#[Validate('required|max:10000')]
	public string $message = '';

	public function mount($comment, $depth): void
	{
		$this->comment = $comment;
		$this->depth   = $depth;
		$this->message = strip_tags($comment->body);
        $this->children_count = $comment->children_count;
	}

	public function showAnswers(): void
	{
		$this->children = Comment::where('parent_id', $this->comment->id)
            ->with([
                'user' => function ($query) {
                    $query->select('id', 'name', 'email', 'role')->withCount('comments');
                },
            ])
			->withCount(['children' => function ($query) {
				$query->whereHas('user', function ($q) {
					$q->where('valid', true);
				});
			}])
			->get();

		$this->children_count = 0;
	}

	public function toggleAnswerForm(bool $state): void
	{
		$this->showAnswerForm = $state;
		$this->message        = '';
	}

	public function toggleModifyForm(bool $state): void
	{
		$this->showModifyForm = $state;
	}

	public function createAnswer(): void
	{
		$data              = $this->validate();
		$data['parent_id'] = $this->comment->id;
		$data['user_id']   = Auth::id();
		$data['post_id']   = $this->comment->post_id;
		$data['body']      = $this->message;

		$item = Comment::create($data);

		$item->save();

        if ($item->post->user_id != Auth::id()) {
            $item->post->user->notify(new CommentCreated($item));
        }

        $author = $this->comment->user;
        if ($author->id != $item->post->user_id && $author->id != Auth::id()) {
            $author->notify(new CommentAnswerCreated($item));
        }

		$this->toggleAnswerForm(false);

		$this->showAnswers();
	}

	public function updateAnswer(): void
	{
		$data = $this->validate();

		$this->comment->body = $data['message'];
		$this->comment->save();

		$this->toggleModifyForm(false);
	}

	public function deleteComment(): void
	{
		$this->comment->delete();
		$this->childs  = null;
		$this->comment = null;
	} 

}; ?>

<div>
    <style>
        @media (max-width: 768px) {
            .ml-0 { margin-left: 0rem; }
            .ml-3 { margin-left: 0.75rem; }
            .ml-6 { margin-left: 1.5rem; }
            .ml-9 { margin-left: 2.25rem; }
        }
        @media (min-width: 769px) {
            .ml-0 { margin-left: 0rem; }
            .ml-3 { margin-left: 3rem; }
            .ml-6 { margin-left: 6rem; }
            .ml-9 { margin-left: 9rem; }
        }
    </style>

    @if ($comment)
        <div class="flex flex-col mt-4 ml-{{ $depth * 3 }} lg:ml-{{ $depth * 3 }} border-2 border-gray-400 rounded-md p-2 selection:transition duration-500 ease-in-out shadow-md shadow-gray-500 hover:shadow-xl hover:shadow-gray-500" >

            <div class="flex flex-col justify-between mb-4 md:flex-row">
                <x-avatar :image="Gravatar::get($comment->user->email)" class="!w-24">
                    <x-slot:title class="pl-2 text-xl">
                        {{ $comment->user->name }}
                    </x-slot:title>
                    <x-slot:subtitle class="flex flex-col gap-1 pl-2 mt-2 text-gray-500">
                        <x-icon name="o-calendar" label="{{ $comment->created_at->diffForHumans() }}" />
                        <x-icon name="o-chat-bubble-left"  label="{{ $comment->user->comments_count == 0 ? '' : ($comment->user->comments_count == 1 ? __('1 comment') : $comment->user->comments_count . ' ' . __('comments')) }}" />
                    </x-slot:subtitle>
                </x-avatar>

                <div class="flex flex-col mt-4 space-y-2 lg:mt-0 lg:flex-row lg:items-center lg:space-y-0 lg:space-x-2">
                    @auth
                        @if (Auth::user()->name == $comment->user->name)
                            <x-button label="{{ __('Modify') }}" wire:click="toggleModifyForm(true)"
                                class="btn-outline btn-warning btn-sm" spinner />
                            <x-button label="{{ __('Delete') }}" wire:click="deleteComment()"
                                wire:confirm="{{ __('Are you sure to delete this comment?') }}"
                                class="mt-2 btn-outline btn-error btn-sm" spinner />
                        @endif
                        @if ($depth < 3)
                            <x-button label="{{ __('Answer') }}" wire:click="toggleAnswerForm(true)"
                                class="mt-2 btn-outline btn-sm" spinner />
                        @endif
                    @endauth
                </div>
            </div>

            @if(!$showModifyForm)
                <div class="mb-4">
                    {!! nl2br($comment->body) !!}
                </div>
            @endif
            @if ($showModifyForm || $showAnswerForm)
                <x-card :title="($showModifyForm ? __('Update your comment') : __('Your answer'))" shadow="hidden" class="!p-0">
                    <x-form :wire:submit="($showModifyForm ? 'updateAnswer' : 'createAnswer')" class="mb-4">
                        <x-textarea wire:model="message" :placeholder="($showAnswerForm ? __('Your answer') . ' ...' : '')" hint="{{ __('Max 10000 chars') }}" rows="5" inline />
                        <x-slot:actions>
                            <x-button label="{{ __('Cancel') }}" :wire:click="($showModifyForm ? 'toggleModifyForm(false)' : 'toggleAnswerForm(false)')"
                                class="btn-ghost" />
                            <x-button label="{{ __('Save') }}" class="btn-primary" type="submit" spinner="save" />
                        </x-slot:actions>
                    </x-form>
                </x-card>
            @endif

            @if ($alert)
                <x-alert title="{!! __('This is your first comment') !!}"
                    description="{{ __('It will be validated by an administrator before it appears here') }}"
                    icon="o-exclamation-triangle" class="alert-warning" />
            @endif

            @if($children_count > 0)
                <x-button label="{{ __('Show the answers') }} ({{ $children_count }})" wire:click="showAnswers" class="mt-2 btn-outline btn-sm" spinner />
            @endif

        </div>
    @endif

    @if($children)
        @foreach ($children as $child)
            <livewire:posts.comment :comment="$child" :depth="$depth + 1" :key="$child->id">
        @endforeach
    @endif

</div>
