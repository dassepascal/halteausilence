<?php

namespace Database\Seeders;

use App\Models\Page;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class PageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
   
    public function run()
    {
        $items = [
                ['slug' => 'terms', 'title' => 'Terms'],
                ['slug' => 'privacy-policy', 'title' => 'Privacy Policy'],
            ];

        foreach ($items as $item) {
            Page::factory()->create([
                'title'     => $item['title'],
                'seo_title' => 'Page ' . $item['title'],
                'slug'      => $item['slug'],
                'active'    => true,
            ]);
        }
    }
    }

