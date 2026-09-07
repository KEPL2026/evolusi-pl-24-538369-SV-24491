<?php

use App\Models\Habit;
use App\Models\HabitCompletion;
use App\Models\Note;
use App\Models\PomodoroSession;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('renders the dashboard when there is no data', function () {
    $this->get(route('dashboard'))
        ->assertOk()
        ->assertSee('Ringkasan Hari Ini')
        ->assertSee('Modul Daily Sync');
});

it('summarizes habits, focus sessions, and notes for today', function () {
    $this->travelTo('2026-09-07 10:00:00');

    Habit::factory()->create(['name' => 'Minum air putih']);
    $second = Habit::factory()->create(['name' => 'Olahraga ringan']);
    HabitCompletion::factory()->create([
        'habit_id' => $second->id,
        'completed_date' => '2026-09-07',
    ]);

    PomodoroSession::factory()->create(['duration_minutes' => 25]);
    PomodoroSession::factory()->create(['duration_minutes' => 15]);

    Note::factory()->count(3)->create();
    Note::factory()->create(['created_at' => now()->subDays(2)]);

    $this->get(route('dashboard'))
        ->assertOk()
        ->assertSee('1 dari 2')
        ->assertSee('2 sesi fokus')
        ->assertSee('Minum air putih')
        ->assertSee('3 dibuat hari ini');

    $this->travelBack();
});

it('lists unfinished habits and latest notes as previews', function () {
    $this->travelTo('2026-09-07 10:00:00');

    Habit::factory()->create(['name' => 'Kebiasaan belum dicap']);
    Note::factory()->create(['body' => 'Catatan terbaru dashboard']);

    $this->get(route('dashboard'))
        ->assertOk()
        ->assertSee('Kebiasaan belum dicap')
        ->assertSee('Catatan terbaru dashboard');

    $this->travelBack();
});
