<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->dropColumn('file_type');
            $table->enum('document_type', ['contract', 'invoice', 'certificate', 'quotation', 'other'])->default('other')->after('file_size');
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->dropSoftDeletes();
            $table->dropColumn('document_type');
            $table->string('file_type')->after('file_size');
        });
    }
};
