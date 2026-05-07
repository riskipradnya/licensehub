<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // MySQL: Modify ENUM to include 'midtrans'
        DB::statement("ALTER TABLE payments MODIFY COLUMN payment_method ENUM('transfer', 'credit_card', 'e_wallet', 'cash', 'midtrans') NULL DEFAULT NULL");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE payments MODIFY COLUMN payment_method ENUM('transfer', 'credit_card', 'e_wallet', 'cash') NULL DEFAULT NULL");
    }
};
