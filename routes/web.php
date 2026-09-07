<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HabitCompletionController;
use App\Http\Controllers\HabitController;
use App\Http\Controllers\HabitReportController;
use App\Http\Controllers\NoteController;
use App\Http\Controllers\PomodoroController;
use App\Http\Controllers\StorePomodoroSession;
use Illuminate\Support\Facades\Route;

Route::get('/', DashboardController::class)->name('dashboard');

Route::get('/habits', [HabitController::class, 'index'])->name('habits.index');
Route::get('/habits/export', HabitReportController::class)->name('habits.export');
Route::post('/habits', [HabitController::class, 'store'])->name('habits.store');
Route::patch('/habits/{habit}', [HabitController::class, 'update'])->name('habits.update');
Route::delete('/habits/{habit}', [HabitController::class, 'destroy'])->name('habits.destroy');
Route::post('/habits/{habit}/toggle', HabitCompletionController::class)->name('habits.toggle');

Route::get('/pomodoro', [PomodoroController::class, 'index'])->name('pomodoro.index');
Route::post('/pomodoro/sessions', StorePomodoroSession::class)->name('pomodoro.sessions.store');

Route::get('/notes', [NoteController::class, 'index'])->name('notes.index');
Route::post('/notes', [NoteController::class, 'store'])->name('notes.store');
Route::patch('/notes/{note}', [NoteController::class, 'update'])->name('notes.update');
Route::delete('/notes/{note}', [NoteController::class, 'destroy'])->name('notes.destroy');
