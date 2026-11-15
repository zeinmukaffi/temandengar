<?php

namespace App\Policies;

use App\Models\Consultation;
use App\Models\User;

class ConsultationPolicy
{
    /**
     * Pencerita pemilik atau Konselor tujuan dapat melihat detail konsultasi.
     */
    public function view(User $user, Consultation $consultation): bool
    {
        if ($user->role === 'pencerita') {
            return (int) $consultation->user_id === (int) $user->id;
        }

        if ($user->role === 'konselor') {
            return (int) $consultation->counselor_id === (int) $user->counselor?->id;
        }

        return false;
    }

    /**
     * Hanya Pencerita pemilik konsultasi yang dapat membatalkan.
     */
    public function cancel(User $user, Consultation $consultation): bool
    {
        return $user->role === 'pencerita'
            && (int) $consultation->user_id === (int) $user->id;
    }

    /**
     * Hanya Konselor tujuan yang dapat mengelola status & jadwal sesi.
     */
    public function manageAsCounselor(User $user, Consultation $consultation): bool
    {
        return $user->role === 'konselor'
            && $user->counselor?->is_active === true
            && (int) $consultation->counselor_id === (int) $user->counselor->id;
    }
}