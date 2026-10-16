<?php

namespace Database\Factories;

use App\Models\AdminActivity;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AdminActivity>
 */
class AdminActivityFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'admin_id' => User::factory(),
            'user_id' => User::factory(),
            'action' => fake()->randomElement(array_keys(AdminActivity::ACTION_LABELS)),
            'description' => fake()->sentence(),
            'ip_address' => fake()->ipv4(),
        ];
    }
}
