<?php

namespace Database\Factories;

use App\Models\Newsletter;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class NewsletterFactory extends Factory
{
    protected $model = Newsletter::class;

    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence,
            'subject' => $this->faker->sentence,
            'content' => $this->faker->paragraph,
            'status' => 'draft',
            'created_by' => User::factory(),
            'scheduled_at' => null,
        ];
    }
}
