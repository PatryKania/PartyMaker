<?php

namespace Database\Factories;

use App\Models\Event;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Schedule>
 */
class ScheduleFactory extends Factory
{
    public function definition(): array
    {
        return [
            'event_id' => Event::factory(),
            'title' => fake()->sentence(3),
            'desc' => fake()->optional()->paragraph(),
            'date' => fake()->dateTimeBetween('+1 week', '+1 year')->format('Y-m-d'),
            'start_time' => '12:00:00',
            'end_time' => '13:00:00',
        ];
    }
}
