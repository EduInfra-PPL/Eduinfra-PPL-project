<?php

namespace App\Actions\Auth;

use App\Models\User;

class RejectRegistrationAction
{
    public function execute(User $applicant, User $admin, string $reason): void
    {
        $applicant->forceFill([
            'is_verified' => false,
            'approved_at' => null,
            'rejected_at' => now(),
            'rejection_reason' => trim($reason),
            'reviewed_by' => $admin->id,
        ])->save();
    }
}