<?php

namespace Database\Factories;

use App\Enums\ParticipantRole;
use App\Enums\ParticipantStatus;
use App\Enums\ParticipantType;
use App\Models\Event;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Participant>
 */
class ParticipantFactory extends Factory
{
    public function definition(): array
    {
        $firstName = fake()->firstName();
        $lastName = fake()->lastName();

        return [
            'event_id' => Event::factory(),
            'user_id' => null,
            'first_name' => $firstName,
            'last_name' => $lastName,
            'email' => fake()->unique()->safeEmail(),
            'phone' => fake()->numerify('#########'),
            'role' => ParticipantRole::Guest,
            'type' => ParticipantType::Adult,
            'status' => ParticipantStatus::New,
            'related_id' => null,
        ];
    }

    public function forUser(?User $user = null): static
    {
        return $this->state(function () use ($user) {
            $user ??= User::factory()->create();

            return [
                'user_id' => $user->id,
                'email' => $user->email,
            ];
        });
    }

    public function organizer(): static
    {
        return $this->state(fn () => [
            'role' => ParticipantRole::Organizer,
            'status' => ParticipantStatus::Confirmed,
        ]);
    }

    public function pending(): static
    {
        return $this->state(fn () => ['status' => ParticipantStatus::Pending]);
    }

    public function confirmed(): static
    {
        return $this->state(fn () => ['status' => ParticipantStatus::Confirmed]);
    }

    public function rejected(): static
    {
        return $this->state(fn () => ['status' => ParticipantStatus::Rejected]);
    }
}
