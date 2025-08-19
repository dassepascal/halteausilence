<?php

namespace Database\Seeders;

use Carbon\Carbon;
use App\Models\Post;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;

class PostSeeder extends Seeder
{
    public static $nbrPosts;

    public function run()
    {
        $nbrCategories = 3;

        $this->createPost(1, 1);
        $this->createPost(2, rand(1, $nbrCategories));
        $this->createPost(3, 1);
        $this->createPost(4, 1);
        $this->createPost(5, rand(1, $nbrCategories));
        $this->createPost(6, 1);
        $this->createPost(7, 1);
        $this->createPost(8, rand(1, $nbrCategories));
        $this->createPost(9, rand(1, $nbrCategories));
      

        

        

    }

    protected function createPost($id, $category_id)
    {
        $months = ['03', '03', '03', '04', '04', '06', '06', '06', '06'];

        $date =
        Carbon::createFromFormat('Y-m-d', '2022-01-01')->addDays(rand(0, 912))->format('Y-m-d H:i:s');

        $postId = "Post {$id}";

        return Post::factory()->create([
            'title'       => $postId,
            'seo_title'   => $postId,
            'slug'        => Str::of($postId)->slug('-'),
            'user_id'     => rand(1, 2),
            'image'       => '2024/' . $months[$id - 1] . '/img0' . $id . '.jpg',
            'category_id' => $category_id,
            'created_at'  => $date,
            'updated_at'  => $date,
            'pinned'      => 5 == $id,
        ]);
    }
}
// protected function generateRandomDateInRange($start, $end)
// {
//     $startTimestamp = strtotime($start);
//     $endTimestamp = strtotime($end);
//     $randomTimestamp = mt_rand($startTimestamp, $endTimestamp);
//     return date('Y-m-d H:i:s', $randomTimestamp);
// }
