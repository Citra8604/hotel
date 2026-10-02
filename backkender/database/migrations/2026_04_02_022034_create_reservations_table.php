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
        Schema::create('reservations', function (Blueprint $table) {
        $table->id();
        $table->string('reservation_number')->unique();
        $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
        $table->foreignId('room_type_id')->constrained('room_types')->onDelete('cascade');
        $table->date('check_in');
        $table->date('check_out');
        $table->integer('quantity')->default(1);
        $table->integer('total_price');
        $table->enum('payment_method', ['bank_transfer', 'cash'])->default('bank_transfer');
        $table->string('payment_proof')->nullable();
        $table->enum('status', ['pending', 'verified', 'cancelled', 'checked_in', 'checked_out'])->default('pending');
        $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reservations');
    }
};
