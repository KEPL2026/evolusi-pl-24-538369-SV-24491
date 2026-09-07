<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreNoteRequest;
use App\Http\Requests\UpdateNoteRequest;
use App\Models\Note;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class NoteController
{
    public function index(): View
    {
        return view('notes.index', [
            'notes' => Note::query()->latest()->get(),
        ]);
    }

    public function store(StoreNoteRequest $request): JsonResponse
    {
        $note = Note::query()->create($request->validated());

        return response()->json(['note' => $note], 201);
    }

    public function update(UpdateNoteRequest $request, Note $note): JsonResponse
    {
        $note->update($request->validated());

        return response()->json(['note' => $note]);
    }

    public function destroy(Note $note): JsonResponse
    {
        $note->delete();

        return response()->json(['ok' => true]);
    }
}
