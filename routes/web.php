<?php

use App\Http\Controllers\AuthController;
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
    Route::get('/dashboard', fn() => view('dashboard.index'))->name('dashboard');

    // === Licenses (IT & Finance can view, IT can manage) ===
    Route::get('/licenses', fn() => view('licenses.index'))->name('licenses.index');
    Route::get('/licenses/create', fn() => view('licenses.create'))->name('licenses.create');
    Route::get('/licenses/{id}', fn() => view('licenses.show'))->name('licenses.show');
    Route::get('/licenses/{id}/edit', fn() => view('licenses.edit'))->name('licenses.edit');

    // === Vendors ===
    Route::get('/vendors', fn() => view('vendors.index'))->name('vendors.index');
    Route::get('/vendors/create', fn() => view('vendors.create'))->name('vendors.create');
    Route::get('/vendors/{id}', fn() => view('vendors.show'))->name('vendors.show');
    Route::get('/vendors/{id}/edit', fn() => view('vendors.edit'))->name('vendors.edit');

    // === Documents ===
    Route::get('/documents', fn() => view('documents.index'))->name('documents.index');

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
