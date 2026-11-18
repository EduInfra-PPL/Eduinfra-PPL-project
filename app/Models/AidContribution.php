<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Model AidContribution - Tabel aid_contributions (Domain 4: Entitas 12 PRD - PBI-11).
 * Mencatat komitmen bantuan dana/barang CSR dari mitra korporasi atau donatur.
 *
 * @property int $id
 * @property string $transaction_code
 * @property int $campaign_id
 * @property int|null $donor_id
 * @property string $donor_name
 * @property string|null $donor_unit
 * @property int $amount_or_goods
 * @property string $contribution_type
 * @property string|null $payment_proof
 * @property string $term_stage
 * @property string|null $verification_note
 * @property string|null $notes
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */
class AidContribution extends Model
{
    use HasFactory;

    protected $table = 'aid_contributions';

    protected $fillable = [
        'transaction_code',
        'campaign_id',
        'donor_id',
        'donor_name',
        'donor_unit',
        'amount_or_goods',
        'contribution_type',
        'payment_proof',
        'term_stage',
        'verification_note',
        'notes',
        'status',
    ];

    protected $casts = [
        'campaign_id' => 'integer',
        'donor_id' => 'integer',
        'amount_or_goods' => 'integer',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELASI ELOQUENT
    |--------------------------------------------------------------------------
    */

    /**
     * Relasi ke kampanye bantuan induk (Entitas 11).
     */
    public function campaign(): BelongsTo
    {
        return $this->belongsTo(AidCampaign::class, 'campaign_id');
    }

    /**
     * Relasi ke akun donatur / mitra CSR (Domain 5).
     */
    public function donor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'donor_id');
    }

    /*
    |--------------------------------------------------------------------------
    | LOCAL SCOPES
    |--------------------------------------------------------------------------
    */

    /**
     * Scope kontribusi yang sudah tervalidasi.
     */
    public function scopeVerified(Builder $query): Builder
    {
        return $query->where('status', 'verified');
    }

    /*
    |--------------------------------------------------------------------------
    | ACCESSORS
    |--------------------------------------------------------------------------
    */

    /**
     * Format nominal rupiah (mis. Rp 150.000.000).
     */
    public function getFormattedAmountAttribute(): string
    {
        return 'Rp ' . number_format($this->amount_or_goods, 0, ',', '.');
    }
}
