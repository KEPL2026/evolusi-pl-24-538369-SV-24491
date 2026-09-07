<?php

use App\Models\Habit;
use App\Models\HabitCompletion;
use App\Services\HabitStats;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('renders the habits index page', function () {
    $this->get(route('habits.index'))
        ->assertOk()
        ->assertSee('Habit Checklist');
});

it('creates a habit', function () {
    $response = $this->postJson(route('habits.store'), ['name' => 'Olahraga pagi']);

    $response
        ->assertCreated()
        ->assertJsonPath('habit.name', 'Olahraga pagi');

    $this->assertDatabaseHas('habits', ['name' => 'Olahraga pagi']);
});

it('validates the habit name when creating', function () {
    $this->postJson(route('habits.store'), [])
        ->assertUnprocessable()
        ->assertJsonValidationErrors('name');
});

it('toggles a habit completion for today', function () {
    $habit = Habit::factory()->create();
    $today = now()->toDateString();

    $this->postJson(route('habits.toggle', $habit))
        ->assertOk()
        ->assertJsonPath('completed', true);

    $this->assertDatabaseHas('habit_completions', [
        'habit_id' => $habit->id,
        'completed_date' => $today,
    ]);

    $this->postJson(route('habits.toggle', $habit))
        ->assertOk()
        ->assertJsonPath('completed', false);

    $this->assertDatabaseMissing('habit_completions', [
        'habit_id' => $habit->id,
        'completed_date' => $today,
    ]);
});

it('reports today progress on toggle responses', function () {
    Habit::factory()->create();
    $second = Habit::factory()->create();

    $this->postJson(route('habits.toggle', $second))
        ->assertOk()
        ->assertJsonPath('progress.total', 2)
        ->assertJsonPath('progress.completed', 1)
        ->assertJsonPath('progress.percent', 50);
});

it('renames a habit', function () {
    $habit = Habit::factory()->create(['name' => 'Kebiasaan lama']);

    $this->patchJson(route('habits.update', $habit), ['name' => 'Kebiasaan baru'])
        ->assertOk()
        ->assertJsonPath('habit.name', 'Kebiasaan baru');

    $this->assertDatabaseHas('habits', ['name' => 'Kebiasaan baru']);
});

it('validates the habit name when updating', function () {
    $habit = Habit::factory()->create();

    $this->patchJson(route('habits.update', $habit), ['name' => ''])
        ->assertUnprocessable()
        ->assertJsonValidationErrors('name');
});

it('deletes a habit and cascades its completions', function () {
    $habit = Habit::factory()
        ->has(HabitCompletion::factory())
        ->create();

    $this->deleteJson(route('habits.destroy', $habit))
        ->assertOk();

    $this->assertDatabaseMissing('habits', ['id' => $habit->id]);
    $this->assertDatabaseCount('habit_completions', 0);
});

it('counts consecutive days as the current streak', function () {
    $this->travelTo('2026-09-07 10:00:00');

    $habit = Habit::factory()->create();

    foreach (['2026-09-05', '2026-09-06', '2026-09-07'] as $date) {
        HabitCompletion::factory()->create([
            'habit_id' => $habit->id,
            'completed_date' => $date,
        ]);
    }

    expect(app(HabitStats::class)->currentStreak())->toBe(3);

    $this->travelBack();
});

it('keeps the streak when today is not completed yet', function () {
    $this->travelTo('2026-09-07 10:00:00');

    $habit = Habit::factory()->create();

    foreach (['2026-09-05', '2026-09-06'] as $date) {
        HabitCompletion::factory()->create([
            'habit_id' => $habit->id,
            'completed_date' => $date,
        ]);
    }

    expect(app(HabitStats::class)->currentStreak())->toBe(2);

    $this->travelBack();
});
