<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class MenusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    
    public function run()
    {
        $menus = [
                ['label' => 'Catégorie 1', 'link' => null, 'order' => 3],
                ['label' => 'Catégorie 2', 'link' => '/category/category-2', 'order' => 2],
                ['label' => 'Catégorie 3', 'link' => '/category/category-3', 'order' => 1],
            ];

        DB::table('menus')->insert($menus);

        $submenus = [
            ['label' => 'Post 1', 'order' => 1, 'link' => '/posts/post-1', 'menu_id' => 1],
            ['label' => 'Tout', 'order' => 3, 'link' => '/category/category-1', 'menu_id' => 1],
        ];

        DB::table('submenus')->insert($submenus);
    }
    }

