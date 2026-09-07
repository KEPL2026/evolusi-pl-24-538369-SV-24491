<?php

namespace Database\Factories;

use App\Models\PomodoroSession;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PomodoroSession>
 */
class PomodoroSessionFactory extends Factory
{
    public function definition(): array
    {
        $duration = 25;

        return [
            'type' => 'focus',
            'duration_minutes' => $duration,
            'started_at' => now()->subMinutes($duration),
            'completed_at' => now(),
        ];
    }
}
