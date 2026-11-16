<?php

namespace App\Http\Controllers;

use App\Actions\Reports\ProcessReportMediaAction;
use App\Http\Requests\Reports\UploadReportMediaRequest;
use App\Models\DamageReport;
use App\Models\ReportMedia;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

/**
 * Controller untuk pengelolaan berkas media bukti kerusakan fisik (Domain 2: PBI-05).
 * Mematuhi prinsip Strict Thin Controller (< 20 baris per method) sesuai AGENT_RULE.md.
 */
class ReportMediaController extends Controller
{
    /**
     * Unggah dan proses berkas foto bukti fisik kerusakan beserta validasi EXIF GPS.
     */
    public function store(
        UploadReportMediaRequest $request,
        DamageReport $report,
        ProcessReportMediaAction $action
    ): JsonResponse|RedirectResponse {
        $media = $action->execute($report, $request->file('file'));

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Foto bukti kerusakan berhasil diproses dan disimpan.',
                'data' => [
                    'id' => $media->id,
                    'file_name' => $media->file_name,
                    'url' => $media->url,
                    'distance_meters' => $media->distance_meters,
                    'is_verified_location' => $media->is_verified_location,
                    'location_status' => $media->location_status_label,
                ],
            ], 201);
        }

        return back()->with('success', 'Foto bukti kerusakan berhasil diunggah.');
    }

    /**
     * Hapus berkas media fisik dari disk storage dan baris tabel report_media.
     */
    public function destroy(Request $request, ReportMedia $media): JsonResponse|RedirectResponse
    {
        Storage::disk('public')->delete($media->media_path);
        $media->delete();

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Berkas media bukti berhasil dihapus.',
            ]);
        }

        return back()->with('success', 'Berkas media bukti berhasil dihapus.');
    }
}
