<?php

namespace App\Http\Controllers;

use App\Models\PomodoroSession;
use Illuminate\View\View;

class PomodoroController
{
    public function index(): View
    {
        $sessions = PomodoroSession::query()
            ->completedToday()
            ->latest('completed_at')
            ->get();

        return view('pomodoro.index', [
            'sessions' => $sessions,
            'sessionCount' => $sessions->count(),
            'totalMinutes' => $sessions->sum('duration_minutes'),
        ]);
    }
}
