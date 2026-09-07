<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Note extends Model
{
    use HasFactory;

    public const COLORS = ['yellow', 'sky', 'coral'];

    protected $fillable = [
        'body',
        'color',
    ];
}
