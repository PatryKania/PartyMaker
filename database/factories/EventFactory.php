<?php

namespace Database\Factories;

use App\Enums\EventType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Event>
 */
class EventFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->sentence(3),
            'date' => fake()->dateTimeBetween('+1 week', '+1 year')->format('Y-m-d'),
            'type' => fake()->randomElement(EventType::cases()),
            'color' => fake()->hexColor(),
        ];
    }
}
