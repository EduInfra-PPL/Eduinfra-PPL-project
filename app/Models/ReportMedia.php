<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

/**
 * Model ReportMedia (Domain 2: PBI-05).
 * Merepresentasikan berkas media bukti kerusakan fisik dan metadata integritas EXIF GPS.
 *
 * @property int $id
 * @property int $report_id
 * @property string $media_path
 * @property string $file_name
 * @property int $file_size
 * @property string $mime_type
 * @property float|null $exif_lat
 * @property float|null $exif_lng
 * @property \Illuminate\Support\Carbon|null $captured_at
 * @property int|null $distance_meters
 * @property bool $is_verified_location
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 */
class ReportMedia extends Model
{
    protected $table = 'report_media';

    protected $fillable = [
        'report_id',
        'media_path',
        'file_name',
        'file_size',
        'mime_type',
        'exif_lat',
        'exif_lng',
        'captured_at',
        'distance_meters',
        'is_verified_location',
    ];

    protected $casts = [
        'exif_lat' => 'decimal:7',
        'exif_lng' => 'decimal:7',
        'captured_at' => 'datetime',
        'distance_meters' => 'integer',
        'file_size' => 'integer',
        'is_verified_location' => 'boolean',
    ];

    /**
     * Relasi ke Laporan Kerusakan Induk (PBI-04).
     */
    public function damageReport(): BelongsTo
    {
        return $this->belongsTo(DamageReport::class, 'report_id');
    }

    /**
     * Scope foto yang berhasil lolos validasi geofence 250m.
     */
    public function scopeVerifiedLocation(Builder $query): Builder
    {
        return $query->where('is_verified_location', true);
    }

    /**
     * Scope foto yang ditandai peringatan (deviasi > 250m atau tanpa GPS).
     */
    public function scopeFlagged(Builder $query): Builder
    {
        return $query->where('is_verified_location', false);
    }

    /**
     * Accessor URL lengkap berkas media untuk ditampilkan di antarmuka web.
     */
    public function getUrlAttribute(): string
    {
        return Storage::disk('public')->url($this->media_path);
    }

    /**
     * Accessor status teks validasi geolokasi EXIF.
     */
    public function getLocationStatusLabelAttribute(): string
    {
        if ($this->exif_lat === null || $this->exif_lng === null) {
            return 'Tanpa Metadata GPS';
        }

        if ($this->is_verified_location) {
            return "Valid Geofence ({$this->distance_meters}m)";
        }

        return "Flagged: Deviasi {$this->distance_meters}m (>250m)";
    }
}

