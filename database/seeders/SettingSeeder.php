<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            ['key' => 'pagination', 'value' => 6],
            ['key' => 'excerptSize', 'value' => 45],
            ['key' => 'title', 'value' => 'Mon titre'],
            ['key' => 'subTitle', 'value' => 'Mon sous-titre'],
            ['key' => 'newPost', 'value' => 4],
        ];

        DB::table('settings')->insert($settings);
    }
}
