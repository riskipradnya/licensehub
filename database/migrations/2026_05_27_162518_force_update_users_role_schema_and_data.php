<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. ALTER COLUMN TIPE DATA: Mengubah kolom role dari ENUM menjadi VARCHAR(255)
        DB::statement("ALTER TABLE users MODIFY role VARCHAR(255) NOT NULL DEFAULT 'it_team'");

        // 2. MASS-UPDATE DATA LAMA (Data Migration)
        DB::table('users')->where('role', 'it_staff')->update(['role' => 'it_team']);
        DB::table('users')->whereIn('role', ['finance_manager', 'finance_staff'])->update(['role' => 'finance_team']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reverse mass-update secara tentatif
        DB::table('users')->where('role', 'it_team')->update(['role' => 'it_staff']);
        DB::table('users')->where('role', 'finance_team')->update(['role' => 'finance_staff']);

        // Rollback kembali menjadi ENUM lama
        DB::statement("ALTER TABLE users MODIFY role ENUM('super_admin', 'it_staff', 'finance_manager', 'finance_staff') NOT NULL DEFAULT 'it_staff'");
    }
};
