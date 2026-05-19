<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable([
    'name', 'vendor_id', 'category_id', 'type', 'serial_key',
    'start_date', 'expiry_date', 'seats', 'cost', 'billing_cycle',
    'status', 'notes', 'created_by',
])]
class License extends Model
{
    use SoftDeletes;

    protected function casts(): array
    {
        return [
            'start_date'  => 'date',
            'expiry_date' => 'date',
            'cost'        => 'decimal:2',
            'seats'       => 'integer',
        ];
    }

    // ─── Relationships ───────────────────────────────────────

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function documents(): HasMany
    {
        return $this->hasMany(Document::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    public function costProjections(): HasMany
    {
        return $this->hasMany(CostProjection::class);
    }

    // ─── Scopes ──────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeExpiring($query)
    {
        return $query->where('status', 'expiring');
    }

    public function scopeExpired($query)
    {
        return $query->where('status', 'expired');
    }

    public function scopeExpiringSoon($query, int $days = 30)
    {
        return $query->where('expiry_date', '<=', now()->addDays($days))
                     ->where('expiry_date', '>=', now())
                     ->where('status', '!=', 'cancelled');
    }

    public function scopeFilterCategory($query, $categoryId)
    {
        if ($categoryId) {
            return $query->where('category_id', $categoryId);
        }

        return $query;
    }

    public function scopeFilterStatus($query, $status)
    {
        if ($status) {
            return $query->where('status', $status);
        }

        return $query;
    }

    public function scopeSearch($query, $search)
    {
        if ($search) {
            return $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('serial_key', 'like', "%{$search}%")
                  ->orWhereHas('vendor', function ($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  });
            });
        }

        return $query;
    }

    // ─── Accessors ───────────────────────────────────────────

    public function getDaysUntilExpiryAttribute(): ?int
    {
        if (!$this->expiry_date) {
            return null;
        }

        return (int) now()->diffInDays($this->expiry_date, false);
    }

    public function getIsExpiredAttribute(): bool
    {
        return $this->expiry_date && $this->expiry_date->isPast();
    }

    /**
     * Format cost as Indonesian Rupiah string.
     */
    public function getFormattedCostAttribute(): string
    {
        return 'Rp ' . number_format((float) $this->cost, 0, ',', '.');
    }

    /**
     * Get the CSS status color mapping for category badges.
     */
    public function getCategoryColorAttribute(): string
    {
        return match ($this->category?->slug) {
            'os'        => 'active',
            'software'  => 'info',
            'antivirus' => 'warning',
            'security'  => 'danger',
            'database'  => 'info',
            'cloud'     => 'active',
            default     => 'info',
        };
    }
    /**
     * STATUS MANAGEMENT — ARSITEKTUR PENTING:
     *
     * Kolom `status` di tabel licenses adalah SINGLE SOURCE OF TRUTH.
     * Gunakan SELALU $license->status untuk membaca status.
     *
     * Status ditulis oleh dua mekanisme:
     *   1. LicenseController::computeStatus() — saat create/update via form
     *   2. XenditController::renewLicense()   — saat webhook Xendit COMPLETED
     *
     * JANGAN gunakan accessor yang menghitung ulang status dari expiry_date
     * secara real-time, karena akan mengabaikan status 'active' yang
     * sudah ditulis oleh proses pembayaran/renewal.
     *
     * Untuk menampilkan sisa hari: gunakan $license->days_until_expiry.
     */
}
