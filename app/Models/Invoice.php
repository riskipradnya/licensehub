<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'payment_id', 'invoice_number', 'vendor_id', 'license_id',
    'amount', 'tax_amount', 'total_amount', 'invoice_date',
    'due_date', 'status', 'file_path', 'notes', 'created_by',
])]
class Invoice extends Model
{
    protected function casts(): array
    {
        return [
            'amount'       => 'decimal:2',
            'tax_amount'   => 'decimal:2',
            'total_amount' => 'decimal:2',
            'invoice_date' => 'date',
            'due_date'     => 'date',
        ];
    }

    // ─── Relationships ───────────────────────────────────────

    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class);
    }

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }

    public function license(): BelongsTo
    {
        return $this->belongsTo(License::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // ─── Scopes ──────────────────────────────────────────────

    public function scopeOverdue($query)
    {
        return $query->where('status', '!=', 'paid')
                     ->where('status', '!=', 'cancelled')
                     ->where('due_date', '<', now());
    }

    public function scopeUnpaid($query)
    {
        return $query->whereNotIn('status', ['paid', 'cancelled']);
    }

    // ─── Helpers ─────────────────────────────────────────────

    public function isOverdue(): bool
    {
        return $this->due_date
            && $this->due_date->isPast()
            && !in_array($this->status, ['paid', 'cancelled']);
    }

    /**
     * Generate the next invoice number (INV-YYYYMM-XXXX).
     */
    public static function generateInvoiceNumber(): string
    {
        $prefix = 'INV-' . now()->format('Ym') . '-';
        $latest = static::where('invoice_number', 'like', $prefix . '%')
            ->orderByDesc('invoice_number')
            ->value('invoice_number');

        $nextNumber = $latest
            ? ((int) substr($latest, -4)) + 1
            : 1;

        return $prefix . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
    }
}
