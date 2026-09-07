<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreHabitRequest;
use App\Http\Requests\UpdateHabitRequest;
use App\Models\Habit;
use App\Services\HabitStats;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class HabitController
{
    public function __construct(private readonly HabitStats $stats) {}

    public function index(): View
    {
        $today = now()->toDateString();

        $habits = Habit::query()
            ->with(['completions' => fn ($query) => $query->whereDate('completed_date', $today)])
            ->orderBy('created_at')
            ->get();

        return view('habits.index', [
            'habits' => $habits,
            'streaks' => $this->stats->streaksPerHabit(),
            'streak' => $this->stats->currentStreak(),
            'progress' => $this->stats->progressToday(),
        ]);
    }

    public function store(StoreHabitRequest $request): JsonResponse
    {
        $habit = Habit::query()->create($request->validated());

        return response()->json([
            'habit' => [
                'id' => $habit->id,
                'name' => $habit->name,
            ],
            'progress' => $this->stats->progressToday(),
        ], 201);
    }

    public function update(UpdateHabitRequest $request, Habit $habit): JsonResponse
    {
        $habit->update($request->validated());

        return response()->json([
            'habit' => [
                'id' => $habit->id,
                'name' => $habit->name,
            ],
        ]);
    }

    public function destroy(Habit $habit): JsonResponse
    {
        $habit->delete();

        return response()->json([
            'progress' => $this->stats->progressToday(),
            'streak' => $this->stats->currentStreak(),
        ]);
    }
}
