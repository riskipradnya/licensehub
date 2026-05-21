<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

#[Fillable([
    'license_id', 'amount', 'payment_date', 'payment_method',
    'reference_number', 'status', 'approved_by', 'approved_at',
    'notes', 'created_by',
])]
class Payment extends Model
{
    protected function casts(): array
    {
        return [
            'amount'       => 'decimal:2',
            'payment_date' => 'date',
            'approved_at'  => 'datetime',
        ];
    }

    protected static function booted()
    {
        $syncLogic = function (self $payment) {
            // 1. Cari Invoice Unpaid berdasarkan license_id
            $invoice = \App\Models\Invoice::where('license_id', $payment->license_id)
                ->where('status', 'unpaid')->first();
                
            // 2. Jika ada, update status jadi paid dan isi payment_id, lalu catat AuditLog
            if ($invoice) {
                $invoice->update(['status' => 'paid', 'payment_id' => $payment->id]);
                
                \App\Models\AuditLog::log('updated_status', 'Invoice', $invoice->id,
                    ['status' => 'unpaid', 'payment_id' => null],
                    ['status' => 'paid', 'payment_id' => $payment->id, 'reason' => 'Auto-synced from Payment Event']
                );
            }
            
            // 3. Eksekusi perpanjangan lisensi
            if ($payment->license) {
                $payment->license->renewExpiryDate();
            }
        };

        static::created(function (self $payment) use ($syncLogic) {
            if ($payment->status === 'paid') {
                $syncLogic($payment);
            }
        });

        static::updated(function (self $payment) use ($syncLogic) {
            if ($payment->wasChanged('status') && $payment->status === 'paid') {
                $syncLogic($payment);
            }
        });
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

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function invoice(): HasOne
    {
        return $this->hasOne(Invoice::class);
    }

    // ─── Scopes ──────────────────────────────────────────────

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    // ─── Helpers ─────────────────────────────────────────────

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function approve(User $approver): void
    {
        $this->update([
            'status'      => 'approved',
            'approved_by' => $approver->id,
            'approved_at' => now(),
        ]);
    }
}
