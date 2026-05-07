<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\License;
use App\Models\Payment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Midtrans\Config;
use Midtrans\Snap;

class MidtransController extends Controller
{
    public function __construct()
    {
        Config::$serverKey    = config('midtrans.server_key');
        Config::$clientKey    = config('midtrans.client_key');
        Config::$isProduction = config('midtrans.is_production');
        Config::$isSanitized  = config('midtrans.is_sanitized');
        Config::$is3ds        = config('midtrans.is_3ds');
    }

    /**
     * Generate a Midtrans Snap Token for a license payment.
     */
    public function getSnapToken(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'license_id' => ['required', 'exists:licenses,id'],
            'amount'     => ['required', 'numeric', 'min:1'],
        ]);

        $license = License::with('vendor')->findOrFail($validated['license_id']);
        $user    = auth()->user();
        $orderId = 'LH-' . $license->id . '-' . time();
        $amount  = (int) $validated['amount'];

        // Create pending payment record
        $payment = Payment::create([
            'license_id'   => $license->id,
            'amount'       => $amount,
            'payment_date' => now(),
            'payment_method' => 'midtrans',
            'reference_number' => $orderId,
            'status'       => 'pending',
            'created_by'   => $user->id,
            'notes'        => "Midtrans payment for {$license->name}",
        ]);

        $params = [
            'transaction_details' => [
                'order_id'     => $orderId,
                'gross_amount' => $amount,
            ],
            'customer_details' => [
                'first_name' => $user->name,
                'email'      => $user->email,
            ],
            'item_details' => [
                [
                    'id'       => 'LICENSE-' . $license->id,
                    'price'    => $amount,
                    'quantity' => 1,
                    'name'     => substr("Renewal: {$license->name}", 0, 50),
                ],
            ],
            'callbacks' => [
                'finish' => route('payments.index'),
            ],
        ];

        try {
            $snapToken = Snap::getSnapToken($params);

            AuditLog::log('midtrans_token_generated', 'Payment', $payment->id, null, [
                'order_id' => $orderId,
                'amount'   => $amount,
            ]);

            return response()->json([
                'snap_token' => $snapToken,
                'order_id'   => $orderId,
                'payment_id' => $payment->id,
            ]);
        } catch (\Exception $e) {
            // Clean up the pending payment if token generation fails
            $payment->delete();

            return response()->json([
                'error' => 'Gagal membuat token pembayaran: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Handle Midtrans payment notification (webhook callback).
     */
    public function handleNotification(Request $request): JsonResponse
    {
        try {
            $notification = new \Midtrans\Notification();

            $orderId     = $notification->order_id;
            $statusCode  = $notification->status_code;
            $grossAmount = $notification->gross_amount;
            $txStatus    = $notification->transaction_status;
            $fraudStatus = $notification->fraud_status ?? null;

            // Find the payment by reference_number (order_id)
            $payment = Payment::where('reference_number', $orderId)->first();

            if (!$payment) {
                return response()->json(['message' => 'Payment not found'], 404);
            }

            if ($txStatus === 'capture' || $txStatus === 'settlement') {
                if ($fraudStatus === 'accept' || $fraudStatus === null) {
                    $payment->update([
                        'status'         => 'paid',
                        'payment_method' => 'midtrans',
                    ]);

                    AuditLog::log('midtrans_paid', 'Payment', $payment->id, null, [
                        'order_id'     => $orderId,
                        'tx_status'    => $txStatus,
                        'gross_amount' => $grossAmount,
                    ]);
                }
            } elseif ($txStatus === 'pending') {
                $payment->update(['status' => 'pending']);
            } elseif (in_array($txStatus, ['deny', 'expire', 'cancel'])) {
                $payment->update(['status' => 'rejected']);

                AuditLog::log('midtrans_failed', 'Payment', $payment->id, null, [
                    'order_id'  => $orderId,
                    'tx_status' => $txStatus,
                ]);
            }

            return response()->json(['message' => 'OK']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
