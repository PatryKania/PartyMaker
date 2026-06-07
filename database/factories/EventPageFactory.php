<?php

namespace Database\Factories;

use App\Models\Event;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\EventPage>
 */
class EventPageFactory extends Factory
{
    public function definition(): array
    {
        return [
            'event_id' => Event::factory(),
            'slug' => fake()->unique()->slug(),
            'main_banner' => null,
            'down_img' => null,
            'content' => ['title' => fake()->sentence(3)],
            'down_content' => ['description' => fake()->paragraph()],
        ];
    }
}
