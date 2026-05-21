<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Auto-mark unpaid invoices as overdue
\Illuminate\Support\Facades\Schedule::call(function () {
    $count = \App\Models\Invoice::where('status', 'unpaid')
        ->where('due_date', '<', now()->startOfDay())
        ->update(['status' => 'overdue']);
        
    if ($count > 0) {
        \Illuminate\Support\Facades\Log::info("Auto-marked {$count} unpaid invoices as overdue.");
    }
})->daily()->name('check-overdue-invoices');
