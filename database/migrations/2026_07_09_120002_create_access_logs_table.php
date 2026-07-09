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
        Schema::create('access_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('access_card_id')->constrained()->onDelete('cascade');
            $table->foreignId('door_lock_id')->constrained()->onDelete('cascade');
            $table->enum('method', ['nfc', 'ble', 'qr'])->default('nfc');
            $table->enum('status', ['granted', 'denied', 'expired'])->default('denied');
            $table->timestamp('verified_at')->nullable();
            $table->string('ip_address', 45)->nullable();   // From the lock controller calling the API
            $table->timestamps();

            $table->index(['access_card_id', 'created_at']);
            $table->index(['door_lock_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('access_logs');
    }
};
