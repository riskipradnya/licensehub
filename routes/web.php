<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\LicenseController;
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
        Route::get('/payments', fn() => view('finance.payments'))->name('payments.index');
        Route::get('/payments/history', fn() => view('finance.payment-history'))->name('payments.history');
        Route::get('/invoices', fn() => view('finance.invoices'))->name('invoices.index');
        Route::get('/reports', fn() => view('finance.reports'))->name('reports.index');
    });

    // === Settings ===
    Route::get('/profile', fn() => view('settings.profile'))->name('profile.index');

    Route::middleware('role:super_admin')->group(function () {
        Route::get('/users', fn() => view('settings.users'))->name('users.index');
    });
});
