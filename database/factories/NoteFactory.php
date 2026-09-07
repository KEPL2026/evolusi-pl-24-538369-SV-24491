<?php

namespace Database\Factories;

use App\Models\Note;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Note>
 */
class NoteFactory extends Factory
{
    public function definition(): array
    {
        return [
            'body' => $this->faker->sentence(8),
            'color' => $this->faker->randomElement(Note::COLORS),
        ];
    }
}
