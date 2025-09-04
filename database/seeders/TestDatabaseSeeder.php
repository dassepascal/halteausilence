<?php


namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Database\Seeders\PageSeeder;
use Database\Seeders\PostSeeder;
use Database\Seeders\UserSeeder;
use Database\Seeders\MenusSeeder;
use Database\Seeders\FooterSeeder;
use Database\Seeders\CommentSeeder;
use Database\Seeders\ContactSeeder;
use Database\Seeders\SettingSeeder;
use Database\Seeders\CategorySeeder;

class TestDatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            CategorySeeder::class,
            PostSeeder::class,
            PageSeeder::class,
            FooterSeeder::class,
            MenusSeeder::class,
            CommentSeeder::class,
            SettingSeeder::class,
            ContactSeeder::class,
        ]);
    }
}
