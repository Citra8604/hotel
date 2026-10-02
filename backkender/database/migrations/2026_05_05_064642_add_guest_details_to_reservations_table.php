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
        Schema::table('reservations', function (Blueprint $table) {
            $table->string('nama_tamu')->nullable()->after('room_type_id');
            $table->string('nik')->nullable()->after('nama_tamu');
            $table->string('no_hp')->nullable()->after('nik');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            $table->dropColumn(['nama_tamu', 'nik', 'no_hp']);
        });
    }
};
