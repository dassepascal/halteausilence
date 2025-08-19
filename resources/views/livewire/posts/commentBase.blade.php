<?php

use App\Models\{ Comment, Post };
use App\Notifications\CommentCreated;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Livewire\Attributes\Validate;
use Livewire\Volt\Component;

new class() extends Component {
	public int $postId;
	public ?Comment $comment    = null;
	public bool $showCreateForm = true;
	public bool $showModifyForm = false;
	public bool $alert          = false;
	 //public Post $post;
	

	#[Validate('required|max:10000')]
	public string $message = '';

	public function mount( $postId ): void
	{
	
		$this->postId = $postId;
		//dd($this->postId);
		//$this->post = Post::select('id', 'title', 'user_id')->with('user')->findOrFail($this->postId);
		
	}

	public function createComment(): void
	{
		
		$data = $this->validate();

		if (!Auth::user()->valid) {
			$this->alert = true;
		}

		$post = Post::select('id', 'title', 'user_id')->with('user')->findOrFail($this->postId);

		$this->comment = Comment::create([
			'user_id' => Auth::id(),
			'post_id' => $this->postId,
			'body'    => $this->message,
			
		]);

			$post->user->notify(new CommentCreated($this->comment));

		$this->message = $data['message'];
	}

	public function updateComment(): void
	{
		$data = $this->validate();

		$this->comment->body = $data['message'];
		$this->comment->save();

		$this->toggleModifyForm(false);
	}

	public function toggleModifyForm(bool $state): void
	{
		$this->showModifyForm = $state;
	}

	public function deleteComment(): void
	{
		$this->comment->delete();

		$this->comment = null;
		$this->message = '';
	}
}; ?>



<div class="flex flex-col mt-4">
    @if ($this->comment)

        @if ($alert)
            <x-alert title="{!! __('This is your first comment') !!}" description="{!! __('It will be validated by an administrator before it appears here') !!}" icon="o-exclamation-triangle"
                class="alert-warning" />
        @else
            <div class="flex flex-col justify-between mb-4 md:flex-row">
                <x-avatar :image="Gravatar::get(Auth::user()->email)" class="!w-24">
                    <x-slot:title class="pl-2 text-xl">
                        {{ Auth::user()->name }}
                    </x-slot:title>
                    <x-slot:subtitle class="flex flex-col gap-1 pl-2 mt-2 text-gray-500">
                        <x-icon name="o-calendar" label="{{ $comment->created_at->diffForHumans() }}" />
                        <x-icon name="o-chat-bubble-left"
                            label="{{ $comment->user->comments_count }} {{ __(' comments') }}" />
                    </x-slot:subtitle>
                </x-avatar>

                <div class="flex flex-col mt-4 space-y-2 lg:mt-0 lg:flex-row lg:items-center lg:space-y-0 lg:space-x-2">
                    <x-button label="{{ __('Modify') }}" wire:click="toggleModifyForm(true)"
                        class="btn-outline btn-sm" />
                    <x-button label="{{ __('Delete') }}" wire:click="deleteComment()"
                        wire:confirm="{{ __('Are you sure to delete this comment?') }}"
                        class="btn-outline btn-error btn-sm" />
                </div>
            </div>

            @include('livewire.posts.comment-form', [
                'formTitle' => __('Update your comment'),
                'formAction' => 'updateComment',
                'showForm' => $showModifyForm,
                'message' => $comment->body,
            ])
        @endif
    @else
        @include('livewire.posts.comment-form', [
            'formTitle' => __('Leave a comment'),
            'formAction' => 'createComment',
            'showForm' => true,
            'message' => '',
        ])
    @endif

</div>
