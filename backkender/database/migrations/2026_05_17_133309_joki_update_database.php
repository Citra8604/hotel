<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Tambahin kolom buat OTP di tabel users
        if (!Schema::hasColumn('users', 'phone')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('phone')->nullable()->unique()->after('email');
                $table->string('otp')->nullable();
                $table->timestamp('otp_expires_at')->nullable();
            });
        }

        // 2. Tambahin kolom denda di tabel reservations
        if (!Schema::hasColumn('reservations', 'late_fine')) {
            Schema::table('reservations', function (Blueprint $table) {
                $table->integer('late_fine')->default(0)->after('total_price');
            });
        }
    }

    public function down(): void
    {
        // Gak usah diisi gapapa, ini cuma buat jaga-jaga
    }
};