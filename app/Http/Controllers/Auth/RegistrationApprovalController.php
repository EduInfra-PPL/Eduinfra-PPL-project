<?php

namespace App\Http\Controllers\Auth;

use App\Actions\Auth\ApproveRegistrationAction;
use App\Actions\Auth\ListRegistrationApplicationsAction;
use App\Actions\Auth\RejectRegistrationAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RejectRegistrationRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Peninjauan permohonan registrasi oleh Admin Dinas. Strict Thin Controller.
 */
class RegistrationApprovalController extends Controller
{
    public function index(Request $request, ListRegistrationApplicationsAction $list): View
    {
        $status = $request->query('status') === 'rejected' ? 'rejected' : 'pending';

        return view('admin.registrations.index', [
            'status' => $status,
            'applicants' => $list->execute($status),
            'counts' => $list->counts(),
        ]);
    }

    public function approve(Request $request, User $user, ApproveRegistrationAction $action): RedirectResponse
    {
        abort_if($user->is_verified || $user->registration_role === null, 404);

        $action->execute($user, $request->user());

        return back()->with('status', "Akun {$user->name} disetujui dan sudah dapat login.");
    }

    public function reject(RejectRegistrationRequest $request, User $user, RejectRegistrationAction $action): RedirectResponse
    {
        abort_if($user->is_verified || $user->registration_role === null, 404);

        $action->execute($user, $request->user(), $request->validated('rejection_reason'));

        return back()->with('status', "Permohonan {$user->name} ditolak.");
    }
}