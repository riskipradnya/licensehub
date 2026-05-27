<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\LicenseController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\VendorController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CostProjectionController;
use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\XenditController;
use Illuminate\Support\Facades\Route;

// ══════════════════════════════════════════════════════════════
//  GUEST ROUTES (unauthenticated)
// ══════════════════════════════════════════════════════════════

Route::get('/', fn() => redirect('/login'));

// Xendit Disbursement webhook (no auth — called server-to-server by Xendit)
Route::post('/xendit/callback', [XenditController::class, 'handleCallback'])->name('xendit.callback');
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);


});

// ══════════════════════════════════════════════════════════════
//  AUTHENTICATED ROUTES
// ══════════════════════════════════════════════════════════════

Route::middleware('auth')->group(function () {

    // Logout
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // === Dashboard ===
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // ==========================================================
    // === URUTAN MUTLAK: Categories HARUS di atas Licenses! ====
    // ==========================================================
    Route::prefix('licenses')->name('licenses.')->group(function () {
        Route::resource('categories', CategoryController::class)->except(['create', 'show', 'edit']);
    });

    // === Licenses (IT & Finance can view, IT can manage) ===
    Route::resource('licenses', LicenseController::class);

    // === Vendors ===
    Route::resource('vendors', VendorController::class);
    // (Tanda kurung kurawal nyasar di sini sudah dihapus!)

    // === Documents ===
    Route::delete('/documents/vendor/{vendor}/{field}', [DocumentController::class, 'destroyVendorDoc'])->name('documents.destroyVendorDoc');
    Route::resource('documents', DocumentController::class)->only(['index', 'store', 'show', 'destroy']);

    // === Monitoring ===
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.markAllRead');
    Route::get('/cost-projection', [CostProjectionController::class, 'index'])->name('cost-projection.index');
    Route::get('/cost-projection/export', [CostProjectionController::class, 'export'])->name('cost-projection.export');

    Route::middleware('role:super_admin,finance_manager')->group(function () {
        Route::get('/audit-log', [AuditLogController::class, 'index'])->name('audit-log.index');
    });

    // === Process Payment for a specific license (accessible to all authenticated users) ===
    Route::get('/payments/process/{license}', [PaymentController::class, 'renew'])->name('payments.renew');

    // === Xendit Disbursement ===
    Route::post('/xendit/disburse', [XenditController::class, 'createDisbursement'])->name('xendit.disburse');
    
    // === Finance (restricted to finance roles) ===
    Route::middleware('role:finance_manager,finance_staff')->group(function () {
        // Payments
        Route::get('/payments', [PaymentController::class, 'index'])->name('payments.index');
        Route::post('/payments', [PaymentController::class, 'store'])->name('payments.store');
        Route::post('/payments/{payment}/approve', [PaymentController::class, 'approve'])->name('payments.approve');
        Route::post('/payments/{payment}/reject', [PaymentController::class, 'reject'])->name('payments.reject');
        Route::post('/payments/{payment}/mark-paid', [PaymentController::class, 'markPaid'])->name('payments.markPaid');
        Route::get('/payments/history', [PaymentController::class, 'history'])->name('payments.history');
        Route::get('/payments/export', [PaymentController::class, 'export'])->name('payments.export');
        Route::get('/payments/{payment}/receipt', [PaymentController::class, 'downloadReceipt'])->name('payments.receipt');

        // Invoices
        Route::get('/invoices', [InvoiceController::class, 'index'])->name('invoices.index');
        Route::post('/invoices', [InvoiceController::class, 'store'])->name('invoices.store');
        Route::post('/invoices/{invoice}/status', [InvoiceController::class, 'updateStatus'])->name('invoices.updateStatus');
        Route::delete('/invoices/{invoice}', [InvoiceController::class, 'destroy'])->name('invoices.destroy');

        Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    });

    // === Settings ===
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.index');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');

    Route::middleware('role:super_admin')->group(function () {
        Route::resource('users', UserController::class);
    });

    Route::middleware('role:super_admin,it_manager')->group(function () {
        Route::resource('notification-settings', App\Http\Controllers\NotificationSettingController::class)
            ->only(['index', 'store', 'update', 'destroy']);
    });
}); // <--- INI ADALAH PENUTUP MIDDLEWARE AUTH YANG SEBENARNYA!

// ══════════════════════════════════════════════════════════════
//  TESTING & AUTOMATION ROUTES
// ══════════════════════════════════════════════════════════════

Route::get('/test-email-blast', function () {
    try {
        $license = \App\Models\License::whereIn('status', ['expired', 'expiring'])->first();
        
        if (!$license) {
            return 'GAGAL SIMULASI! Tidak ada lisensi expired/expiring di database untuk diperpanjang.';
        }

        $license->renewExpiryDate();
        
        return 'SUKSES! Fungsi renewExpiryDate() dijalankan, tanggal diperpanjang, dan email RESOLVED masuk antrean.';
    } catch (\Exception $e) {
        return 'GAGAL! Error: ' . $e->getMessage();
    }
});


Route::get('/auto-set-dates', function () {
    $licenses = \App\Models\License::where('type', '!=', 'Perpetual')->take(5)->get();

    if ($licenses->count() < 5) {
        return "Anda butuh minimal 5 data lisensi Subscription/Berjangka di database untuk tes ini!";
    }

    $licenses[0]->update(['expiry_date' => '2026-06-26', 'status' => 'active']);
    $licenses[1]->update(['expiry_date' => '2026-06-09', 'status' => 'active']);
    $licenses[2]->update(['expiry_date' => '2026-06-02', 'status' => 'active']);
    $licenses[3]->update(['expiry_date' => '2026-05-25', 'status' => 'active']);
    $licenses[4]->update(['expiry_date' => '2026-05-22', 'status' => 'active']);

    return "✅ SUPER MANTAP! 5 Lisensi NON-PERPETUAL telah otomatis diatur untuk dieksekusi Robot.";
});