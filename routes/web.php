<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\LicenseController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\VendorController;
use Illuminate\Support\Facades\Route;

// ══════════════════════════════════════════════════════════════
//  GUEST ROUTES (unauthenticated)
// ══════════════════════════════════════════════════════════════

Route::get('/', fn() => redirect('/login'));

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);

    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);

    Route::get('/forgot-password', fn() => view('auth.forgot-password'))->name('password.request');
});

// ══════════════════════════════════════════════════════════════
//  AUTHENTICATED ROUTES
// ══════════════════════════════════════════════════════════════

Route::middleware('auth')->group(function () {

    // Logout
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // === Dashboard ===
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // === Licenses (IT & Finance can view, IT can manage) ===
    Route::resource('licenses', LicenseController::class);

    // === Vendors ===
    Route::resource('vendors', VendorController::class);

    // === Documents ===
    Route::resource('documents', DocumentController::class)->only(['index', 'store', 'show', 'destroy']);

    // === Monitoring ===
    Route::get('/notifications', fn() => view('monitoring.notifications'))->name('notifications.index');
    Route::get('/cost-projection', fn() => view('monitoring.cost-projection'))->name('cost-projection.index');

    Route::middleware('role:super_admin,finance_manager')->group(function () {
        Route::get('/audit-log', fn() => view('monitoring.audit-log'))->name('audit-log.index');
    });

    // === Finance (restricted to finance roles) ===
    Route::middleware('role:finance_manager,finance_staff')->group(function () {
        // Payments
        Route::get('/payments', [PaymentController::class, 'index'])->name('payments.index');
        Route::post('/payments', [PaymentController::class, 'store'])->name('payments.store');
        Route::post('/payments/{payment}/approve', [PaymentController::class, 'approve'])->name('payments.approve');
        Route::post('/payments/{payment}/reject', [PaymentController::class, 'reject'])->name('payments.reject');
        Route::post('/payments/{payment}/mark-paid', [PaymentController::class, 'markPaid'])->name('payments.markPaid');
        Route::get('/payments/history', [PaymentController::class, 'history'])->name('payments.history');

        // Invoices
        Route::get('/invoices', [InvoiceController::class, 'index'])->name('invoices.index');
        Route::post('/invoices', [InvoiceController::class, 'store'])->name('invoices.store');
        Route::post('/invoices/{invoice}/status', [InvoiceController::class, 'updateStatus'])->name('invoices.updateStatus');
        Route::delete('/invoices/{invoice}', [InvoiceController::class, 'destroy'])->name('invoices.destroy');

        Route::get('/reports', fn() => view('finance.reports'))->name('reports.index');
    });

    // === Settings ===
    Route::get('/profile', fn() => view('settings.profile'))->name('profile.index');

    Route::middleware('role:super_admin')->group(function () {
        Route::get('/users', fn() => view('settings.users'))->name('users.index');
    });
});
