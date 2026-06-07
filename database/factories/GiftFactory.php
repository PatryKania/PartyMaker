<?php

namespace Database\Factories;

use App\Models\Event;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Gift>
 */
class GiftFactory extends Factory
{
    public function definition(): array
    {
        return [
            'event_id' => Event::factory(),
            'name' => fake()->words(3, true),
            'reserved_by_id' => null,
        ];
    }

    public function reserved(?User $user = null): static
    {
        return $this->state(fn () => [
            'reserved_by_id' => ($user ?? User::factory()->create())->id,
        ]);
    }
}
