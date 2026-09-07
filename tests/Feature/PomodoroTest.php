<?php

use App\Models\PomodoroSession;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('renders the pomodoro page', function () {
    $this->get(route('pomodoro.index'))
        ->assertOk()
        ->assertSee('Pomodoro Timer')
        ->assertSee('25:00');
});

it('stores a completed focus session', function () {
    $this->postJson(route('pomodoro.sessions.store'), [
        'type' => 'focus',
        'duration_minutes' => 25,
    ])->assertOk()
        ->assertJsonPath('today.count', 1)
        ->assertJsonPath('today.total_minutes', 25)
        ->assertJsonPath('session.duration_minutes', 25);

    $this->assertDatabaseCount('pomodoro_sessions', 1);
});

it('aggregates today sessions across multiple stores', function () {
    PomodoroSession::factory()->create(['completed_at' => now()->subDay()]);
    PomodoroSession::factory()->create(['duration_minutes' => 25]);

    $this->postJson(route('pomodoro.sessions.store'), [
        'type' => 'focus',
        'duration_minutes' => 50,
    ])->assertOk()
        ->assertJsonPath('today.count', 2)
        ->assertJsonPath('today.total_minutes', 75);
});

it('rejects sessions that are not focus type', function () {
    $this->postJson(route('pomodoro.sessions.store'), [
        'type' => 'short_break',
        'duration_minutes' => 5,
    ])->assertUnprocessable()
        ->assertJsonValidationErrors('type');

    $this->assertDatabaseCount('pomodoro_sessions', 0);
});

it('validates the duration range', function () {
    $this->postJson(route('pomodoro.sessions.store'), [
        'type' => 'focus',
        'duration_minutes' => 0,
    ])->assertUnprocessable()
        ->assertJsonValidationErrors('duration_minutes');

    $this->postJson(route('pomodoro.sessions.store'), [
        'type' => 'focus',
        'duration_minutes' => 200,
    ])->assertUnprocessable()
        ->assertJsonValidationErrors('duration_minutes');
});
