<?php

namespace Database\Factories;

use App\Models\Event;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Invitation>
 */
class InvitationFactory extends Factory
{
    public function definition(): array
    {
        return [
            'event_id' => Event::factory(),
            'theme' => 'classic',
            'content' => [
                'subject' => fake()->sentence(4),
                'body' => fake()->paragraph(),
            ],
        ];
    }
}
