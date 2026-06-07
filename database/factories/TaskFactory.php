<?php

namespace Database\Factories;

use App\Models\Event;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Task>
 */
class TaskFactory extends Factory
{
    public function definition(): array
    {
        return [
            'event_id' => Event::factory(),
            'title' => fake()->sentence(4),
            'description' => fake()->optional()->paragraph(),
            'due_date' => fake()->dateTimeBetween('+1 day', '+2 months'),
            'is_completed' => false,
        ];
    }

    public function completed(): static
    {
        return $this->state(fn () => ['is_completed' => true]);
    }
}
