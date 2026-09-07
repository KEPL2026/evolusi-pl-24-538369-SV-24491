<?php

namespace Database\Factories;

use App\Models\Habit;
use App\Models\HabitCompletion;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<HabitCompletion>
 */
class HabitCompletionFactory extends Factory
{
    public function definition(): array
    {
        return [
            'habit_id' => Habit::factory(),
            'completed_date' => now()->toDateString(),
        ];
    }
}
