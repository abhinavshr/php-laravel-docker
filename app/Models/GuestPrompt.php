<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GuestPrompt extends Model
{
    use HasFactory;

    protected $fillable = [
        'ip',
        'count',
        'last_prompt_at',
    ];

    protected $casts = [
        'last_prompt_at' => 'datetime',
    ];
}
