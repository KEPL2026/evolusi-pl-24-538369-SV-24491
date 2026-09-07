<?php

namespace App\Services;

use App\Models\Habit;
use App\Models\HabitCompletion;

class HabitStats
{
    /**
     * @return array{completed: int, total: int, percent: int}
     */
    public function progressToday(): array
    {
        $total = Habit::count();
        $completed = HabitCompletion::query()
            ->whereDate('completed_date', now()->toDateString())
            ->count();

        return [
            'completed' => $completed,
            'total' => $total,
            'percent' => $total > 0 ? (int) round($completed / $total * 100) : 0,
        ];
    }

    public function currentStreak(): int
    {
        $dates = HabitCompletion::query()
            ->distinct()
            ->orderByDesc('completed_date')
            ->pluck('completed_date');

        return $this->countConsecutiveDays($dates->all());
    }

    public function streakForHabit(Habit $habit): int
    {
        $dates = HabitCompletion::query()
            ->where('habit_id', $habit->id)
            ->distinct()
            ->orderByDesc('completed_date')
            ->pluck('completed_date');

        return $this->countConsecutiveDays($dates->all());
    }

    /**
     * @return array<int, int>
     */
    public function streaksPerHabit(): array
    {
        $rows = HabitCompletion::query()
            ->select('habit_id', 'completed_date')
            ->distinct()
            ->orderBy('habit_id')
            ->orderByDesc('completed_date')
            ->get();

        $streaks = [];

        foreach ($rows->groupBy('habit_id') as $habitId => $completions) {
            $streaks[(int) $habitId] = $this->countConsecutiveDays($completions->pluck('completed_date')->all());
        }

        return $streaks;
    }

    /**
     * @param  array<int, string>  $dates
     */
    private function countConsecutiveDays(array $dates): int
    {
        $dateSet = collect($dates)
            ->map(fn (string $date): string => substr((string) $date, 0, 10))
            ->flip();

        $cursor = now()->startOfDay();

        if (! $dateSet->has($cursor->format('Y-m-d'))) {
            $cursor->subDay();
        }

        $streak = 0;

        while ($dateSet->has($cursor->format('Y-m-d'))) {
            $streak++;
            $cursor->subDay();
        }

        return $streak;
    }
}
