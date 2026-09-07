<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePomodoroSessionRequest;
use App\Models\PomodoroSession;
use Illuminate\Http\JsonResponse;

class StorePomodoroSession
{
    public function __invoke(StorePomodoroSessionRequest $request): JsonResponse
    {
        $duration = $request->integer('duration_minutes');

        $session = PomodoroSession::query()->create([
            'type' => $request->string('type')->toString(),
            'duration_minutes' => $duration,
            'started_at' => now()->subMinutes($duration),
            'completed_at' => now(),
        ]);

        $todaySessions = PomodoroSession::query()->completedToday()->get();

        return response()->json([
            'session' => [
                'id' => $session->id,
                'duration_minutes' => $session->duration_minutes,
                'completed_at' => $session->completed_at->format('H:i'),
            ],
            'today' => [
                'count' => $todaySessions->count(),
                'total_minutes' => $todaySessions->sum('duration_minutes'),
            ],
        ]);
    }
}
