<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Model DamageReport - Tabel damage_reports (PBI-04 / FR-04).
 * Menyimpan tiket laporan kerusakan fisik fasilitas sekolah dari publik/guru.
 * 
 * @property int $id
 * @property string $ticket_num
 * @property int $school_id
 * @property int $category_id
 * @property int|null $reporter_id
 * @property string $title
 * @property string $description
 * @property int $severity_level
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */
class DamageReport extends Model
{
    use HasFactory;

    protected $table = 'damage_reports';

    protected $fillable = [
        'ticket_num',
        'school_id',
        'category_id',
        'reporter_id',
        'title',
        'description',
        'severity_level',
        'status',
    ];

    protected $casts = [
        'severity_level' => 'integer',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELASI ELOQUENT
    |--------------------------------------------------------------------------
    */

    /**
     * Sekolah tempat terjadinya kerusakan fisik fasilitas.
     */
    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class, 'school_id');
    }

    /**
     * Kategori komponen fasilitas yang rusak (RKB, Sanitasi, Lab, dll).
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(FacilityCategory::class, 'category_id');
    }

    /**
     * Pengguna yang melaporkan (Guru / Pelapor Publik).
     */
    public function reporter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reporter_id');
    }

    /*
    |--------------------------------------------------------------------------
    | SCOPES PENCARIAN & FILTER (Untuk Tabel Listing PBI-04)
    |--------------------------------------------------------------------------
    */

    /**
     * Scope laporan yang masih menunggu visitasi lapangan.
     */
    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope laporan yang sudah diverifikasi oleh auditor.
     */
    public function scopeVerified(Builder $query): Builder
    {
        return $query->where('status', 'verified');
    }

    /**
     * Scope laporan dengan kondisi darurat / kritis (Level 5).
     */
    public function scopeUrgent(Builder $query): Builder
    {
        return $query->where('severity_level', 5);
    }

    /**
     * Scope filter pencarian berdasarkan keyword tiket, nama sekolah, NPSN, atau kota.
     */
    public function scopeSearch(Builder $query, ?string $keyword): Builder
    {
        if (empty($keyword)) {
            return $query;
        }

        return $query->where(function (Builder $q) use ($keyword) {
            $q->where('ticket_num', 'ilike', "%{$keyword}%")
              ->orWhere('title', 'ilike', "%{$keyword}%")
              ->orWhereHas('school', function (Builder $sq) use ($keyword) {
                  $sq->where('name', 'ilike', "%{$keyword}%")
                     ->orWhere('npsn', 'like', "%{$keyword}%")
                     ->orWhereHas('region', function (Builder $rq) use ($keyword) {
                         $rq->where('name', 'ilike', "%{$keyword}%");
                     });
              });
        });
    }

    /*
    |--------------------------------------------------------------------------
    | ACCESSORS & MUTATORS (Presentasi Label Tampilan)
    |--------------------------------------------------------------------------
    */

    /**
     * Label teks tingkat keparahan (Level 1 s/d 5).
     */
    public function getSeverityLabelAttribute(): string
    {
        return match ($this->severity_level) {
            1 => 'Level 1 – Ringan',
            2 => 'Level 2 – Ringan',
            3 => 'Level 3 – Sedang',
            4 => 'Level 4 – Rusak Berat',
            5 => 'Level 5 – Kritis',
            default => 'Tidak Diketahui',
        };
    }

    /**
     * Label teks status laporan.
     */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'Pending Visitasi Lapangan',
            'verified' => 'Verified Auditor',
            'rejected' => 'Laporan Ditolak',
            'in_repair' => 'Dalam Renovasi',
            'resolved' => 'Renovasi Selesai',
            default => ucfirst($this->status),
        };
    }
}

