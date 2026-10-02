<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            // Menghapus kuncian unik agar 1 nomor WA bisa dipakai banyak akun
            $table->dropUnique('users_phone_unique');
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->unique('phone', 'users_phone_unique');
        });
    }
};