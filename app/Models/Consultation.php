<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Consultation extends Model
{
    protected $fillable = [
        'user_id',
        'counselor_id',
        'journal_id',
        'ai_reflection_id',
        'topic',
        'context',
        'preferred_at',
        'share_journal',
        'share_ai_reflection',
        'consent_at',
        'consultation_brief',
        'status',
        'scheduled_at',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'preferred_at' => 'datetime',
            'share_journal' => 'boolean',
            'share_ai_reflection' => 'boolean',
            'consent_at' => 'datetime',
            'scheduled_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function counselor(): BelongsTo
    {
        return $this->belongsTo(Counselor::class);
    }

    public function journal(): BelongsTo
    {
        return $this->belongsTo(Journal::class);
    }

    public function aiReflection(): BelongsTo
    {
        return $this->belongsTo(AiReflection::class);
    }
}