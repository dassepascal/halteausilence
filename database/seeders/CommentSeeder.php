<?php

namespace Database\Seeders;

use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Seeder;

class CommentSeeder extends Seeder
{
    public function run(): void
    {
        $nbrPosts = 9;
        $nbrUsers = 3;

        $posts = Post::take($nbrPosts)->get();
        $users = User::take($nbrUsers)->get();

        foreach ($posts as $post) {
            // Créer des commentaires parents
            $parentComments = Comment::factory()
                ->count(rand(1, 5))
                ->make([
                    'post_id' => $post->id,
                    'user_id' => $users->random()->id,
                ]);

            foreach ($parentComments as $parentComment) {
                $parentComment->save();

                // Créer des commentaires enfants pour chaque commentaire parent
                $childComments = Comment::factory()
                    ->count(rand(0, 3))
                    ->make([
                        'post_id' => $post->id,
                        'user_id' => $users->random()->id,
                        'parent_id' => $parentComment->id,
                    ]);

                foreach ($childComments as $childComment) {
                    $childComment->save();
                }
            }
        }
    }
}
