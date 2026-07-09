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
        Schema::create('access_cards', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('door_lock_id')->constrained()->onDelete('cascade');
            $table->string('card_token', 64)->unique();     // Cryptographic token the phone presents
            $table->string('card_name')->nullable();         // User-friendly label, e.g. "Office Main Door"
            $table->boolean('is_active')->default(true);
            $table->timestamp('expires_at')->nullable();     // Nullable = never expires
            $table->json('access_schedule')->nullable();     // e.g. {"days":["mon","tue"],"start":"09:00","end":"18:00"}
            $table->timestamps();

            $table->index(['user_id', 'is_active']);
            $table->index('card_token');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('access_cards');
    }
};
