<?php

namespace App\Http\Controllers\Admin;

use App\Actions\FacilityCategories\CreateFacilityCategoryAction;
use App\Actions\FacilityCategories\DeactivateFacilityCategoryAction;
use App\Actions\FacilityCategories\DuplicateFacilityCategoryException;
use App\Actions\FacilityCategories\ListFacilityCategoriesAction;
use App\Actions\FacilityCategories\UpdateFacilityCategoryAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\FacilityCategories\StoreFacilityCategoryRequest;
use App\Http\Requests\FacilityCategories\UpdateFacilityCategoryRequest;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/**
 * Halaman admin kategori fasilitas dan bobot urgensi (PBI-03 / FR-03).
 * Tipis: validasi di FormRequest, logika di Action.
 */
class FacilityCategoryAdminController extends Controller
{
    public function index(Request $request, ListFacilityCategoriesAction $list): View
    {
        $filters = $request->only(['q', 'name', 'weight', 'status']);
        $selected = $request->filled('id') ? $list->find((int) $request->input('id')) : null;

        return view('admin.categories.index', [
            'categories' => $list->execute($filters),
            'filters' => $filters,
            'groupNames' => $list->groupNames(),
            'stats' => $list->stats(),
            'selected' => $selected,
        ]);
    }

    public function store(StoreFacilityCategoryRequest $request, CreateFacilityCategoryAction $create): RedirectResponse
    {
        try {
            $category = $create->execute($request->validated());
        } catch (DuplicateFacilityCategoryException $e) {
            return back()->withInput()->withErrors(['sub_category' => $e->getMessage()]);
        }

        return redirect()->route('admin.categories.index', ['id' => $category->id])
            ->with('success', "Kategori {$category->full_name} berhasil ditambahkan.");
    }

    public function update(UpdateFacilityCategoryRequest $request, int $id, UpdateFacilityCategoryAction $update): RedirectResponse
    {
        $data = $request->validated();
        if ($data === []) {
            return back()->withInput()->withErrors(['urgency_weight' => 'Pilih bobot urgensi.']);
        }
        if (! $update->execute($id, $data)) {
            return back()->with('error', 'Kategori tidak ditemukan.');
        }

        return redirect()->route('admin.categories.index', ['id' => $id])
            ->with('success', 'Bobot urgensi dan satuan diperbarui.');
    }

    public function activate(int $id, UpdateFacilityCategoryAction $update): RedirectResponse
    {
        if (! $update->execute($id, ['is_enabled' => true])) {
            return back()->with('error', 'Kategori tidak ditemukan.');
        }

        return redirect()->route('admin.categories.index', ['id' => $id])
            ->with('success', 'Kategori diaktifkan kembali.');
    }

    public function destroy(int $id, DeactivateFacilityCategoryAction $deactivate): RedirectResponse
    {
        if (! $deactivate->execute($id)) {
            return back()->with('error', 'Kategori tidak ditemukan.');
        }

        return redirect()->route('admin.categories.index', ['id' => $id, 'status' => 'nonaktif'])
            ->with('success', 'Kategori dinonaktifkan dan tidak lagi muncul di formulir laporan.');
    }
}
