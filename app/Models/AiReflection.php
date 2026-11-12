<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AiReflection extends Model
{
    protected $fillable = [
        'user_id',
        'journal_id',
        'summary',
        'emotional_insight',
        'key_concerns',
        'reflection_questions',
        'suggested_steps',
        'user_feedback',
        'status',
        'ai_model',
        'raw_response',
    ];

    protected function casts(): array
    {
        return [
            'key_concerns' => 'array',
            'reflection_questions' => 'array',
            'suggested_steps' => 'array',
            'raw_response' => 'array',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function journal(): BelongsTo
    {
        return $this->belongsTo(Journal::class);
    }

    public function consultations(): HasMany
    {
        return $this->hasMany(Consultation::class);
    }
}