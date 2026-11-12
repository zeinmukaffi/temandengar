<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Counselor extends Model
{
    protected $fillable = [
        'name',
        'role',
        'photo_path',
        'bio',
        'specialties',
        'availability_description',
        'is_available',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'specialties' => 'array',
            'is_available' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function consultations(): HasMany
    {
        return $this->hasMany(Consultation::class);
    }
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}