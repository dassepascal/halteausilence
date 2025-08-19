<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Page>
 */
class PageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'body'             => fake()->paragraph(10),
            'meta_description' => fake()->sentence($nbWords = 6, $variableNbWords = true),
            'meta_keywords'    => implode(',', fake()->words($nb = 3, $asText = false)),
        ];
    }
}
