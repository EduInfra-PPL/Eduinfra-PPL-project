<?php

namespace App\Http\Controllers\Admin;

use App\Actions\Schools\CreateSchoolAction;
use App\Actions\Schools\DeactivateSchoolAction;
use App\Actions\Schools\ListSchoolsAction;
use App\Actions\Schools\UpdateSchoolContactAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Schools\StoreSchoolRequest;
use App\Http\Requests\Schools\UpdateSchoolContactRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Contracts\View\View;
use RuntimeException;

/**
 * Halaman admin profil sekolah (PBI-01 / FR-01). Tipis: validasi di FormRequest, logika di Action.
 */
class SchoolAdminController extends Controller
{
    public function index(Request $request, ListSchoolsAction $list): View
    {
        $filters = $request->only(['q', 'level', 'is_3t', 'category', 'status']);
        $selected = $request->filled('id') ? $list->find((int) $request->input('id')) : null;

        return view('admin.schools.index', [
            'schools' => $list->execute($filters),
            'filters' => $filters,
            'stats' => $list->stats(),
            'selected' => $selected,
        ]);
    }

    public function store(StoreSchoolRequest $request, CreateSchoolAction $create): RedirectResponse
    {
        try {
            $id = $create->execute($request->validated());
        } catch (RuntimeException $e) {
            return back()->withInput()->withErrors(['lat' => $e->getMessage()]);
        }

        return redirect()->route('admin.schools.index', ['id' => $id])
            ->with('success', 'Sekolah berhasil didaftarkan.');
    }

    public function update(UpdateSchoolContactRequest $request, int $id, UpdateSchoolContactAction $update): RedirectResponse
    {
        if (! $update->execute($id, $request->validated())) {
            return back()->with('error', 'Tidak ada perubahan atau sekolah tidak ditemukan.');
        }

        return redirect()->route('admin.schools.index', ['id' => $id])
            ->with('success', 'Kontak dan akreditasi diperbarui.');
    }

    public function destroy(int $id, DeactivateSchoolAction $deactivate): RedirectResponse
    {
        if (! $deactivate->execute($id)) {
            return back()->with('error', 'Sekolah tidak ditemukan atau sudah nonaktif.');
        }

        return redirect()->route('admin.schools.index')->with('success', 'Sekolah dinonaktifkan.');
    }
}
