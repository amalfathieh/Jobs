<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Company>
 */
class CompanyFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => $this->sequence(fn ($sequence) => $sequence->index + 1),
            "company_name" => fake()->streetName(),
            "location" => fake()->address(),
            "about" => fake()->sentence(),
            "contact_info" => fake()->word()
        ];
    }
}
