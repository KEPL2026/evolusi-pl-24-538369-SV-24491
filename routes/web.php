<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HabitCompletionController;
use App\Http\Controllers\HabitController;
use Illuminate\Support\Facades\Route;

Route::get('/', DashboardController::class)->name('dashboard');

Route::get('/habits', [HabitController::class, 'index'])->name('habits.index');
Route::post('/habits', [HabitController::class, 'store'])->name('habits.store');
Route::patch('/habits/{habit}', [HabitController::class, 'update'])->name('habits.update');
Route::delete('/habits/{habit}', [HabitController::class, 'destroy'])->name('habits.destroy');
Route::post('/habits/{habit}/toggle', HabitCompletionController::class)->name('habits.toggle');
