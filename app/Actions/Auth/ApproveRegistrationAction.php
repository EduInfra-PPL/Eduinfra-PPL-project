<?php

namespace App\Actions\Auth;

use App\Models\User;

class ApproveRegistrationAction
{
    public function execute(User $applicant, User $admin): void
    {
        $applicant->forceFill([
            'is_verified' => true,
            'approved_at' => now(),
            'reviewed_by' => $admin->id,
            'rejected_at' => null,
            'rejection_reason' => null,
        ])->save();
    }
}