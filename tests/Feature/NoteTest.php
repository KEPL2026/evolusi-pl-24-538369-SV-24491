<?php

use App\Models\Note;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('renders the notes page', function () {
    Note::factory()->create(['body' => 'Beli bahan praktikum']);

    $this->get(route('notes.index'))
        ->assertOk()
        ->assertSee('Daily Quick Notes')
        ->assertSee('Beli bahan praktikum');
});

it('creates a note', function () {
    $response = $this->postJson(route('notes.store'), [
        'body' => 'Revisi laporan sebelum jam 3 sore',
        'color' => 'yellow',
    ]);

    $response
        ->assertCreated()
        ->assertJsonPath('note.body', 'Revisi laporan sebelum jam 3 sore')
        ->assertJsonPath('note.color', 'yellow');

    $this->assertDatabaseHas('notes', ['body' => 'Revisi laporan sebelum jam 3 sore']);
});

it('validates the note body when creating', function () {
    $this->postJson(route('notes.store'), ['color' => 'sky'])
        ->assertUnprocessable()
        ->assertJsonValidationErrors('body');
});

it('rejects an unknown note color', function () {
    $this->postJson(route('notes.store'), [
        'body' => 'Catatan',
        'color' => 'purple',
    ])->assertUnprocessable()
        ->assertJsonValidationErrors('color');
});

it('updates a note', function () {
    $note = Note::factory()->create(['body' => 'Sebelum diubah']);

    $this->patchJson(route('notes.update', $note), [
        'body' => 'Sesudah diubah',
        'color' => 'coral',
    ])->assertOk()
        ->assertJsonPath('note.body', 'Sesudah diubah')
        ->assertJsonPath('note.color', 'coral');

    $this->assertDatabaseHas('notes', [
        'id' => $note->id,
        'body' => 'Sesudah diubah',
        'color' => 'coral',
    ]);
});

it('deletes a note', function () {
    $note = Note::factory()->create();

    $this->deleteJson(route('notes.destroy', $note))
        ->assertOk();

    $this->assertDatabaseMissing('notes', ['id' => $note->id]);
});
