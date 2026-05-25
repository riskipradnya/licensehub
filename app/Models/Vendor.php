<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

#[Fillable([
    'name', 'contact_person', 'email', 'phone', 'address', 'website',
    'notes', 'sla_response', 'sla_hours', 'bank_name', 'bank_account_number', 'is_active',
    'logo', 'msa_file', 'sla_file',
])]
class Vendor extends Model
{
    use SoftDeletes, LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    // ─── Relationships ───────────────────────────────────────

    public function licenses(): HasMany
    {
        return $this->hasMany(License::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    // ─── Scopes ──────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('name');
    }

    // ─── Accessors ───────────────────────────────────────────

    /**
     * Get the first letter of the vendor name (for avatar).
     */
    public function getInitialAttribute(): string
    {
        return strtoupper(substr($this->name, 0, 1));
    }

    /**
     * Generate a consistent color based on vendor name.
     */
    public function getColorAttribute(): string
    {
        $colors = [
            '#6366f1', '#8b5cf6', '#ec4899', '#ef4444', '#f97316',
            '#f59e0b', '#22c55e', '#14b8a6', '#06b6d4', '#3b82f6',
            '#0078d4', '#ff0000', '#006d5c', '#ff6600', '#696566',
        ];

        // Deterministic color based on name hash
        $index = crc32($this->name) % count($colors);
        return $colors[abs($index)];
    }

    /**
     * Get human-readable SLA response label.
     */
    public function getSlaResponseLabelAttribute(): string
    {
        return match ($this->sla_response) {
            '24h' => '24 Hours',
            '48h' => '48 Hours',
            '72h' => '72 Hours',
            default => $this->sla_response ?? '—',
        };
    }

    /**
     * Get human-readable SLA hours label.
     */
    public function getSlaHoursLabelAttribute(): string
    {
        return match ($this->sla_hours) {
            '24/7' => '24/7',
            'business' => 'Business Hours (9-17)',
            default => $this->sla_hours ?? '—',
        };
    }
}
