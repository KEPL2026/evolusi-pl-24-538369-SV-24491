<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PomodoroSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'duration_minutes',
        'started_at',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'started_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    /**
     * @param  Builder<PomodoroSession>  $query
     */
    public function scopeCompletedToday(Builder $query): Builder
    {
        return $query->whereDate('completed_at', now()->toDateString());
    }
}
