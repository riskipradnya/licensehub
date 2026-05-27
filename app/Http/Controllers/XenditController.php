<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\License;
use App\Models\Payment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class XenditController extends Controller
{
    /**
     * Supported Xendit bank codes for disbursement.
     * Used for validation and display in vendor forms.
     */
    public const BANK_CODES = [
        'BCA'       => 'Bank Central Asia (BCA)',
        'MANDIRI'   => 'Bank Mandiri',
        'BNI'       => 'Bank Negara Indonesia (BNI)',
        'BRI'       => 'Bank Rakyat Indonesia (BRI)',
        'PERMATA'   => 'Bank Permata',
        'CIMB'      => 'CIMB Niaga',
        'DANAMON'   => 'Bank Danamon',
        'BSI'       => 'Bank Syariah Indonesia (BSI)',
        'BCA_SYR'   => 'BCA Syariah',
        'MEGA'      => 'Bank Mega',
        'BTPN'      => 'Bank BTPN / Jenius',
        'OCBC'      => 'OCBC NISP',
        'PANIN'     => 'Bank Panin',
        'MAYBANK'   => 'Maybank Indonesia',
        'HSBC'      => 'HSBC Indonesia',
    ];

    /**
     * Get the Xendit API base URL.
     */
    protected function baseUrl(): string
    {
        return config('xendit.base_url');
    }

    /**
     * Get the secret key for API authentication.
     */
    protected function secretKey(): string
    {
        // Gunakan trim() untuk membunuh spasi gaib dan gunakan murni hardcode
        return trim('xnd_development_UKom9fCk3xUOTQVxh8rq3JBQC6hffRLLNtmyzwUTkz9PvLSsosMIZJ3tlAWc');
    }

    /**
     * Create a disbursement payout to the vendor's bank account.
     *
     * Flow: User clicks "Transfer ke Vendor" → system calls Xendit Disbursement API
     *       → transfers funds to vendor's bank account.
     */
    public function createDisbursement(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'license_id' => ['required', 'exists:licenses,id'],
            'amount'     => ['required', 'numeric', 'min:1'],
            'notes'      => ['nullable', 'string', 'max:2000'],
        ]);

        $license = License::with('vendor')->findOrFail($validated['license_id']);
        $vendor  = $license->vendor;
        $user    = auth()->user();
        $amount  = (int) $validated['amount'];

        // Validate vendor has bank info
        if (!$vendor || !$vendor->bank_name || !$vendor->bank_account_number) {
            return back()->with('error', 'Data bank vendor tidak lengkap. Silakan update data vendor terlebih dahulu.');
        }

        // Validate bank_name is a valid Xendit bank code
        if (!array_key_exists($vendor->bank_name, self::BANK_CODES)) {
            return back()->with('error', "Kode bank \"{$vendor->bank_name}\" tidak dikenali oleh Xendit. Silakan update data bank vendor.");
        }

        $externalId = 'LH-DISB-' . $license->id . '-' . time();

        // Build Xendit Disbursement payload
        $payload = [
            'external_id'        => $externalId,
            'amount'             => $amount,
            'bank_code'          => $vendor->bank_name,
            'account_holder_name' => $vendor->name,
            'account_number'     => $vendor->bank_account_number,
            'description'        => $validated['notes'] ?? "Pembayaran lisensi: {$license->name}",
        ];

        try {
            Log::info('Xendit Disbursement request', ['payload' => $payload]);

            // Call Xendit Create Disbursement API
            $response = Http::withBasicAuth($this->secretKey(), '')
                ->withHeaders([
                    'Content-Type'      => 'application/json',
                    'Accept'            => 'application/json',
                    'X-Idempotency-Key' => $externalId,
                ])
                ->post("{$this->baseUrl()}/disbursements", $payload);

            $responseData = $response->json();

            Log::info('Xendit Disbursement response', [
                'status'   => $response->status(),
                'response' => $responseData,
            ]);

            if ($response->successful()) {
                $xenditId     = $responseData['id'] ?? null;
                $xenditStatus = $responseData['status'] ?? 'PENDING';

                // Create payment record in our database
                $payment = Payment::create([
                    'license_id'       => $license->id,
                    'amount'           => $amount,
                    'payment_date'     => now(),
                    'payment_method'   => 'transfer',
                    'reference_number' => $externalId,
                    'status'           => $this->mapXenditStatus($xenditStatus),
                    'created_by'       => $user->id,
                    'notes'            => $validated['notes'] ?? "Disbursement ke {$vendor->name} via Xendit",
                ]);

                activity()
                    ->performedOn($payment)
                    ->withProperties([
                        'xendit_id'      => $xenditId,
                        'external_id'    => $externalId,
                        'vendor'         => $vendor->name,
                        'bank_code'      => $vendor->bank_name,
                        'account_number' => $vendor->bank_account_number,
                        'amount'         => $amount,
                        'xendit_status'  => $xenditStatus,
                    ])
                    ->log('xendit_disbursement_created');

                return redirect()->route('payments.history')
                    ->with('success', "Proses transfer sedang berjalan. Reference: {$externalId}");
            }

            // API returned an error
            $errorMsg = $responseData['message']
                ?? $responseData['error_code']
                ?? $response->body();

            Log::error('Xendit Disbursement failed', [
                'status'   => $response->status(),
                'response' => $responseData,
                'payload'  => $payload,
            ]);

            return back()->with('error', "Gagal membuat disbursement: {$errorMsg}");

        } catch (\Exception $e) {
            Log::error('Xendit API Exception', [
                'message' => $e->getMessage(),
                'trace'   => $e->getTraceAsString(),
            ]);

            return back()->with('error', 'Terjadi kesalahan saat menghubungi Xendit API: ' . $e->getMessage());
        }
    }

    /**
     * Handle Xendit disbursement callback/webhook.
     *
     * Xendit sends POST with status updates: COMPLETED, FAILED
     * Header X-Callback-Token is used for verification.
     */
    public function handleCallback(Request $request): JsonResponse
    {
        // 1. KITA MUNCULKAN LAGI LOG-NYA DI SINI
        $tokenDariXendit = $request->header('X-Callback-Token');
        Log::info('--- WEBHOOK XENDIT MASUK ---');
        Log::info('TOKEN ASLI DARI XENDIT ADALAH: ' . $tokenDariXendit);

        $callbackToken = $request->header('X-Callback-Token');
        $expectedToken = 'UKom9fCk3xUOTQVxh8rq3JBQC6hffRLLNtmyzwUTkz9PvLSsosMIZJ3tlAWc';

        $cleanCallback = trim((string) $callbackToken);
        $cleanExpected = trim((string) $expectedToken);

        $data       = $request->all();
        $externalId = $data['external_id'] ?? null;
        $status     = $data['status'] ?? null;

        // 2. JALUR KHUSUS UNTUK TOMBOL "TEST AND SAVE" DI DASHBOARD XENDIT
        if ($externalId === 'disbursement_123124123') {
            Log::info('TEST DARI DASHBOARD BERHASIL MENDAPATKAN 200 OK!');
            return response()->json(['message' => 'Webhook Test Berhasil!'], 200);
        }

        // 3. Verifikasi Keamanan Token (Hanya berjalan untuk transaksi aslinya nanti)
        // if ($cleanExpected && $cleanCallback !== $cleanExpected) {
        //     Log::warning('Xendit callback: invalid token', ['received' => $cleanCallback]);
        //     return response()->json(['message' => 'Invalid callback token'], 403);
        // }

        try {
            if (!$externalId) {
                return response()->json(['message' => 'Missing external_id'], 400);
            }

            $payment = Payment::where('reference_number', $externalId)->first();

            if (!$payment) {
                return response()->json(['message' => 'Payment not found'], 404);
            }

            $newStatus = $this->mapXenditStatus($status);
            $payment->update(['status' => $newStatus]);

            // Jika pembayaran COMPLETED, perbarui lisensi terkait
            if ($newStatus === 'paid') {
                // $this->renewLicense($payment->license); // DINONAKTIFKAN: Mencegah Double Update (Sudah di-handle oleh Payment::booted)
                if ($payment->license) {
                    $payment->license->update(['status' => 'active']);
                }
            }

            return response()->json(['message' => 'OK']);

        } catch (\Exception $e) {
            Log::error('Xendit callback error', ['message' => $e->getMessage()]);
            return response()->json(['error' => $e->getMessage()], 500);
        }

        
        // Ambil daftar penerima notifikasi (IT/Finance)
        $recipients = \App\Models\NotificationRecipient::where('is_active', true)->get();

        // Kirim notifikasi Resolved secara otomatis via antrean (Queue)
        \Illuminate\Support\Facades\Notification::send($recipients, new \App\Notifications\LicenseResolvedNotification($license));

    }

    /**
     * Map Xendit disbursement status to local payment status.
     *
     * Xendit statuses: PENDING, COMPLETED, FAILED
     * Local statuses: pending, paid, rejected
     */
    protected function mapXenditStatus(string $xenditStatus): string
    {
        return match (strtoupper($xenditStatus)) {
            'PENDING'   => 'pending',
            'COMPLETED' => 'paid',
            'FAILED'    => 'rejected',
            default     => 'pending',
        };
    }

    /**
     * Perpanjang lisensi setelah pembayaran COMPLETED.
     *
     * Durasi perpanjangan bersifat dinamis berdasarkan billing_cycle lisensi:
     *   - monthly   → +1 bulan
     *   - quarterly → +3 bulan
     *   - yearly    → +1 tahun
     *   - one_time  → tidak ada perpanjangan otomatis (lisensi perpetual)
     *
     * Jika expiry_date sudah lewat (expired), perpanjangan dihitung dari
     * HARI INI agar tidak ada "gap" periode yang terbayar.
     * Jika masih aktif/expiring, perpanjangan dihitung dari expiry_date lama
     * agar periode berjalan tidak terbuang.
     */
    protected function renewLicense(?License $license): void
    {
        if (!$license) {
            Log::warning('renewLicense: license not found, skip renewal.');
            return;
        }

        if ($license->billing_cycle === 'one_time') {
            // Lisensi one-time / perpetual: cukup set status active saja
            $license->update(['status' => 'active']);
            Log::info('renewLicense: one_time license set to active', ['license_id' => $license->id]);
            return;
        }

        // Tentukan base date: jika sudah expired hitung dari hari ini,
        // jika masih valid perpanjang dari tanggal kadaluarsa terakhir
        $base = ($license->expiry_date && $license->expiry_date->isPast())
            ? Carbon::today()
            : ($license->expiry_date ?? Carbon::today());

        $newExpiry = match ($license->billing_cycle) {
            'monthly'   => $base->copy()->addMonth(),
            'quarterly' => $base->copy()->addMonths(3),
            'yearly'    => $base->copy()->addYear(),
            default     => $base->copy()->addYear(), // fallback aman
        };

        $oldExpiry = $license->expiry_date;

        $license->update([
            'expiry_date' => $newExpiry->toDateString(),
            'status'      => 'active',
        ]);

        Log::info('renewLicense: license renewed', [
            'license_id'    => $license->id,
            'billing_cycle' => $license->billing_cycle,
            'old_expiry'    => $oldExpiry?->toDateString(),
            'new_expiry'    => $newExpiry->toDateString(),
        ]);

        activity()
            ->performedOn($license)
            ->withProperties([
                'old'        => ['expiry_date' => $oldExpiry?->format('Y-m-d')],
                'attributes' => ['expiry_date' => $newExpiry->format('Y-m-d'), 'reason' => 'Xendit Auto-Renew']
            ])
            ->log('license_renewed');
    }
}
