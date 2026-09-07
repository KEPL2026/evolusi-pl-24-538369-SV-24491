<?php

namespace App\Http\Controllers;

use App\Models\Habit;
use App\Models\Note;
use App\Models\PomodoroSession;
use App\Services\HabitStats;
use Illuminate\View\View;

class DashboardController
{
    public function __construct(private readonly HabitStats $stats) {}

    public function __invoke(): View
    {
        $today = now()->toDateString();

        $habits = Habit::query()
            ->with(['completions' => fn ($query) => $query->whereDate('completed_date', $today)])
            ->orderBy('created_at')
            ->get();

        $sessions = PomodoroSession::query()->completedToday()->latest('completed_at')->get();

        return view('dashboard.index', [
            'habits' => $habits,
            'unfinishedHabits' => $habits->reject(fn (Habit $habit) => $habit->completions->isNotEmpty())->take(3),
            'progress' => $this->stats->progressToday(),
            'streak' => $this->stats->currentStreak(),
            'sessionCount' => $sessions->count(),
            'sessionMinutes' => $sessions->sum('duration_minutes'),
            'noteTotal' => Note::count(),
            'notesToday' => Note::query()->whereDate('created_at', $today)->count(),
            'latestNotes' => Note::query()->latest()->limit(3)->get(),
        ]);
    }
}
