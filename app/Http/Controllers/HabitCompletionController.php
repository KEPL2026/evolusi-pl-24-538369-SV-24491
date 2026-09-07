<?php

namespace App\Http\Controllers;

use App\Models\Habit;
use App\Models\HabitCompletion;
use App\Services\HabitStats;
use Illuminate\Http\JsonResponse;

class HabitCompletionController
{
    public function __construct(private readonly HabitStats $stats) {}

    public function __invoke(Habit $habit): JsonResponse
    {
        $today = now()->toDateString();

        $completion = HabitCompletion::query()
            ->where('habit_id', $habit->id)
            ->whereDate('completed_date', $today)
            ->first();

        if ($completion) {
            $completion->delete();
            $completed = false;
        } else {
            HabitCompletion::query()->create([
                'habit_id' => $habit->id,
                'completed_date' => $today,
            ]);
            $completed = true;
        }

        return response()->json([
            'completed' => $completed,
            'progress' => $this->stats->progressToday(),
            'streak' => $this->stats->currentStreak(),
            'habit_streak' => $this->stats->streakForHabit($habit),
        ]);
    }
}
