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
        DB::table('activity_log')->where('subject_type', 'License')->update(['subject_type' => 'App\Models\License']);
        DB::table('activity_log')->where('subject_type', 'Vendor')->update(['subject_type' => 'App\Models\Vendor']);
        DB::table('activity_log')->where('subject_type', 'Document')->update(['subject_type' => 'App\Models\Document']);
        DB::table('activity_log')->where('subject_type', 'User')->update(['subject_type' => 'App\Models\User']);
        DB::table('activity_log')->where('subject_type', 'Payment')->update(['subject_type' => 'App\Models\Payment']);
        DB::table('activity_log')->where('subject_type', 'Invoice')->update(['subject_type' => 'App\Models\Invoice']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('activity_log')->where('subject_type', 'App\Models\License')->update(['subject_type' => 'License']);
        DB::table('activity_log')->where('subject_type', 'App\Models\Vendor')->update(['subject_type' => 'Vendor']);
        DB::table('activity_log')->where('subject_type', 'App\Models\Document')->update(['subject_type' => 'Document']);
        DB::table('activity_log')->where('subject_type', 'App\Models\User')->update(['subject_type' => 'User']);
        DB::table('activity_log')->where('subject_type', 'App\Models\Payment')->update(['subject_type' => 'Payment']);
        DB::table('activity_log')->where('subject_type', 'App\Models\Invoice')->update(['subject_type' => 'Invoice']);
    }
};
