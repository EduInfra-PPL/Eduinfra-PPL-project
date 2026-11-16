<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectMilestone extends Model
{
    use HasFactory;

    protected $table = 'project_milestones';

    protected $fillable = [
        'project_id',
        'milestone_code',
        'title',
        'name',
        'description',
        'weight_pct',
        'progress_pct',
        'status',
        'due_date',
        'target_date',
        'completed_at',
        'completed_date',
        'proof_media_url',
        'is_approved',
        'surveyor_name',
        'notes',
        'evidence_photos',
    ];

    protected $casts = [
        'weight_pct' => 'float',
        'progress_pct' => 'float',
        'due_date' => 'date',
        'completed_at' => 'datetime',
        'is_approved' => 'boolean',
        'evidence_photos' => 'array',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(RepairProject::class, 'project_id');
    }

    public function getNameAttribute(): string
    {
        return (string) ($this->attributes['title'] ?? '');
    }

    public function setNameAttribute(?string $value): void
    {
        $this->attributes['title'] = $value;
    }

    public function getTargetDateAttribute(): mixed
    {
        return $this->due_date;
    }

    public function setTargetDateAttribute(mixed $value): void
    {
        $this->attributes['due_date'] = $value;
    }

    public function getCompletedDateAttribute(): mixed
    {
        return $this->completed_at;
    }

    public function setCompletedDateAttribute(mixed $value): void
    {
        $this->attributes['completed_at'] = $value;
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'completed' => 'Selesai Terverifikasi',
            'in_progress' => 'Sedang Berjalan',
            'delayed' => 'Perlu Akselerasi',
            default => 'Menunggu Jadwal',
        };
    }
}
