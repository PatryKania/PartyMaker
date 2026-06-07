<?php

namespace Database\Factories;

use App\Models\Memory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\MemoryMedia>
 */
class MemoryMediaFactory extends Factory
{
    public function definition(): array
    {
        return [
            'memory_id' => Memory::factory(),
            'type' => 'image',
            'path' => 'memories/'.fake()->uuid().'.jpg',
        ];
    }

    public function video(): static
    {
        return $this->state(fn () => [
            'type' => 'video',
            'path' => 'memories/'.fake()->uuid().'.mp4',
        ]);
    }
}
