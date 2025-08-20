<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->lastName(),  // Ajout des parenthèses
            'firstname' => fake()->firstName(),  // Ajout des parenthèses
            'email' => fake()->unique()->safeEmail(),  // Ajout des parenthèses
            'password' => static::$password ??= Hash::make('password'),
            'newsletter' => fake()->boolean(),
            'valid' => fake()->boolean(),
            'role' => 'user',  // Ajout du champ role manquant
            'created_at' => fake()->dateTimeBetween('-4 years', '-6 months'),
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn(array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    /**
     * Create a valid user (for login tests).
     */
    public function valid(): static
    {
        return $this->state(fn(array $attributes) => [
            'valid' => true,
        ]);
    }

    /**
     * Create an invalid user.
     */
    public function invalid(): static
    {
        return $this->state(fn(array $attributes) => [
            'valid' => false,
        ]);
    }

    /**
     * Create an admin user.
     */
    public function admin(): static
    {
        return $this->state(fn(array $attributes) => [
            'role' => 'admin',
            'valid' => true,  // Les admins sont généralement valides
        ]);
    }

    /**
     * Create a redac user.
     */
    public function redac(): static
    {
        return $this->state(fn(array $attributes) => [
            'role' => 'redac',
            'valid' => true,  // Les rédacteurs sont généralement valides
        ]);
    }

    /**
     * Create a user with newsletter subscription.
     */
    public function subscribedToNewsletter(): static
    {
        return $this->state(fn(array $attributes) => [
            'newsletter' => true,
        ]);
    }
}
