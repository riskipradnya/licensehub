<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\License;
use App\Models\Vendor;
use App\Models\Document;
use App\Models\Payment;
use Spatie\Activitylog\Models\Activity;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class RefreshDemoData extends Command
{
    protected $signature = 'app:refresh-demo-data';
    protected $description = 'Garbage Collect current data and seed demo data for documentation';

    public function handle()
    {
        $this->info('Starting Database Cleanup...');

        // Disable foreign key checks for SQLite/MySQL depending on driver
        $isSqlite = DB::connection()->getDriverName() === 'sqlite';
        if ($isSqlite) {
            DB::statement('PRAGMA foreign_keys = OFF;');
        } else {
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        }
        
        Document::truncate();
        Payment::truncate();
        \App\Models\Invoice::truncate();
        License::truncate();
        Vendor::truncate();

        if ($isSqlite) {
            DB::statement('PRAGMA foreign_keys = ON;');
        } else {
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        }

        $this->info('Database entities (Licenses, Vendors, Documents, Payments) truncated.');

        Activity::whereIn('subject_type', [License::class, Vendor::class, Payment::class, Document::class])->delete();
        $this->info('Audit Logs for Licenses and Vendors cleared.');

        Storage::disk('public')->deleteDirectory('documents');
        $this->info('Public storage /documents directory deleted.');

        $this->info('Running DemoDataSeeder...');
        $this->call('db:seed', ['--class' => 'Database\Seeders\DemoDataSeeder']);
        
        $this->info('Successfully seeded realistic demo data!');
    }
}
