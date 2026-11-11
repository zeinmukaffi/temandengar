<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Journal extends Model
{
    protected $fillable = [
        'user_id',
        'mood_checkin_id',
        'title',
        'content',
        'entry_type',
        'privacy',
        'journaled_at',
    ];

    protected function casts(): array
    {
        return [
            'journaled_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function moodCheckin(): BelongsTo
    {
        return $this->belongsTo(MoodCheckin::class);
    }

    public function aiReflections(): HasMany
    {
        return $this->hasMany(AiReflection::class);
    }

    public function consultations(): HasMany
    {
        return $this->hasMany(Consultation::class);
    }
}