<?php

namespace App\Http\Controllers;

use App\Models\Habit;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class HabitReportController
{
    public function __invoke(Request $request): View
    {
        $start = $this->resolveMonthStart($request->string('month')->toString());
        $end = $start->copy()->endOfMonth();

        $habits = Habit::query()
            ->with(['completions' => fn ($query) => $query
                ->whereBetween('completed_date', [$start->toDateString(), $end->toDateString()])
                ->orderBy('completed_date'),
            ])
            ->orderBy('created_at')
            ->get();

        $rows = $habits->map(fn (Habit $habit): array => [
            'id' => $habit->id,
            'name' => $habit->name,
            'days' => array_fill_keys(
                $habit->completions->map(fn ($completion): int => (int) $completion->completed_date->format('j'))->all(),
                true
            ),
            'total' => $habit->completions->count(),
        ]);

        $productiveDays = $this->countProductiveDays($habits);

        $previous = $start->copy()->subMonthNoOverflow();
        $next = $start->copy()->addMonthNoOverflow();

        return view('habits.report', [
            'rows' => $rows,
            'month' => $start,
            'daysInMonth' => $start->daysInMonth,
            'monthLabel' => $this->monthLabel($start),
            'totalCompletions' => $rows->sum('total'),
            'productiveDays' => $productiveDays,
            'previousMonth' => $previous->format('Y-m'),
            'nextMonth' => $next->format('Y-m'),
            'nextDisabled' => $next->isAfter(now()),
        ]);
    }

    private function resolveMonthStart(string $month): Carbon
    {
        if (preg_match('/^\d{4}-\d{2}$/', $month) === 1) {
            $parsed = Carbon::createFromFormat('Y-m-d', $month.'-01');

            if ($parsed && $parsed->format('Y-m') === $month && ! $parsed->isAfter(now())) {
                return $parsed->startOfMonth();
            }
        }

        return now()->startOfMonth();
    }

    /**
     * @param  Collection<int, Habit>  $habits
     */
    private function countProductiveDays(Collection $habits): int
    {
        return $habits
            ->flatMap->completions
            ->pluck('completed_date')
            ->map(fn (Carbon $date): string => $date->format('Y-m-d'))
            ->unique()
            ->count();
    }

    private function monthLabel(Carbon $date): string
    {
        $months = [
            'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
            'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember',
        ];

        return $months[$date->month - 1].' '.$date->year;
    }
}
