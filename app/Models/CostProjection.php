<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'license_id', 'projected_date', 'projected_cost',
    'actual_cost', 'notes', 'created_by',
])]
class CostProjection extends Model
{
    protected function casts(): array
    {
        return [
            'projected_date' => 'date',
            'projected_cost' => 'decimal:2',
            'actual_cost'    => 'decimal:2',
        ];
    }

    // ─── Relationships ───────────────────────────────────────

    public function license(): BelongsTo
    {
        return $this->belongsTo(License::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // ─── Accessors ───────────────────────────────────────────

    public function getVarianceAttribute(): ?float
    {
        if ($this->actual_cost === null) {
            return null;
        }

        return (float) $this->actual_cost - (float) $this->projected_cost;
    }

    public function getVariancePercentAttribute(): ?float
    {
        if ($this->actual_cost === null || (float) $this->projected_cost === 0.0) {
            return null;
        }

        return (($this->actual_cost - $this->projected_cost) / $this->projected_cost) * 100;
    }
}
