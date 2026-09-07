<?php

use App\Models\Habit;
use App\Models\HabitCompletion;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('renders the report for the current month', function () {
    $this->travelTo('2026-09-07 10:00:00');

    $habit = Habit::factory()->create(['name' => 'Minum air putih']);
    HabitCompletion::factory()->create([
        'habit_id' => $habit->id,
        'completed_date' => '2026-09-05',
    ]);

    $this->get(route('habits.export'))
        ->assertOk()
        ->assertSee('Riwayat Habit')
        ->assertSee('September 2026')
        ->assertSee('Minum air putih');

    $this->travelBack();
});

it('falls back to the current month for an invalid month parameter', function () {
    $this->travelTo('2026-09-07 10:00:00');

    $this->get(route('habits.export', ['month' => '9999-99']))
        ->assertOk()
        ->assertSee('September 2026');

    $this->travelBack();
});

it('renders the report for a requested previous month', function () {
    $this->travelTo('2026-09-07 10:00:00');

    Habit::factory()->create(['name' => 'Olahraga ringan']);

    $this->get(route('habits.export', ['month' => '2026-08']))
        ->assertOk()
        ->assertSee('Agustus 2026')
        ->assertSee('Olahraga ringan');

    $this->travelBack();
});

it('does not allow navigating into the future', function () {
    $this->travelTo('2026-09-07 10:00:00');

    $this->get(route('habits.export', ['month' => '2026-10']))
        ->assertOk()
        ->assertSee('September 2026');

    $this->travelBack();
});
