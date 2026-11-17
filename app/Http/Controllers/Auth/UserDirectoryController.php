<?php

namespace App\Http\Controllers\Auth;

use App\Actions\Auth\ListUsersAction;
use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class UserDirectoryController extends Controller
{
    public function index(Request $request, ListUsersAction $list): View
    {
        $status = in_array($request->query('status'), ['approved', 'pending', 'rejected'], true)
            ? $request->query('status')
            : 'all';
        $search = $request->query('q');
        $tab = in_array($request->query('tab'), ['users', 'rbac', 'fraud', 'sessions'], true)
            ? $request->query('tab')
            : 'users';

        return view('admin.users.index', [
            'status' => $status,
            'search' => $search,
            'tab' => $tab,
            'users' => $list->execute($status, $search),
            'counts' => $list->counts(),
            'roles' => UserRole::cases(),
        ]);
    }

    public function updateRole(Request $request, User $user)
    {
        $allowedRoles = array_column(UserRole::cases(), 'value');

        $validated = $request->validate([
            'role_enum' => ['required', 'string', 'in:'.implode(',', $allowedRoles)],
        ]);

        $user->forceFill([
            'role_enum' => $validated['role_enum'],
        ])->save();

        return redirect()->back()->with('status', "Peran RBAC untuk {$user->name} berhasil diperbarui.");
    }

    public function exportAuditLog(): StreamedResponse
    {
        $users = User::query()
            ->leftJoin('regions', 'regions.id', '=', 'users.region_id')
            ->select('users.*', 'regions.name as region_name')
            ->orderBy('users.id')
            ->get();

        $headers = [
            'Content-Type' => 'text/csv; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="audit-log-keamanan-eduinfra-'.date('Ymd-His').'.csv"',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        return response()->stream(function () use ($users) {
            $handle = fopen('php://output', 'w');
            fputs($handle, "\xEF\xBB\xBF");

            fputcsv($handle, [
                'ID Pengguna',
                'Nama Lengkap',
                'Email',
                'Nomor Identitas (NIP/NIK)',
                'Nomor Seluler',
                'Peran RBAC (role_enum)',
                'Kartu Pendaftaran',
                'Instansi / Satuan Kerja',
                'Wilayah Koridor 3T',
                'Status Verifikasi',
                'Waktu Registrasi',
                'Terakhir Login',
                'Total Frekuensi Login',
            ]);

            foreach ($users as $u) {
                fputcsv($handle, [
                    $u->id,
                    $u->name,
                    $u->email,
                    $u->identity_number ?? '-',
                    $u->phone ?? '-',
                    $u->role_enum ?? 'belum_ditetapkan',
                    $u->registration_role ?? '-',
                    $u->organization ?? '-',
                    $u->region_name ?? 'Nasional',
                    $u->is_verified ? 'Aktif & Terverifikasi' : ($u->rejected_at ? 'Ditolak' : 'Menunggu Verifikasi'),
                    $u->created_at ? $u->created_at->format('Y-m-d H:i:s') : '-',
                    $u->last_login_at ?? 'Belum pernah',
                    $u->login_count ?? 0,
                ]);
            }

            fclose($handle);
        }, 200, $headers);
    }
}
