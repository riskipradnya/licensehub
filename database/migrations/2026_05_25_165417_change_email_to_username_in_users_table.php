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
        // Convert emails to usernames first
        \Illuminate\Support\Facades\DB::table('users')->get()->each(function ($user) {
            $username = explode('@', $user->email)[0];
            \Illuminate\Support\Facades\DB::table('users')
                ->where('id', $user->id)
                ->update(['email' => $username]);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->renameColumn('email', 'username');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->renameColumn('username', 'email');
        });

        \Illuminate\Support\Facades\DB::table('users')->get()->each(function ($user) {
            if (!str_contains($user->email, '@')) {
                \Illuminate\Support\Facades\DB::table('users')
                    ->where('id', $user->id)
                    ->update(['email' => $user->email . '@company.com']);
            }
        });
    }
};
