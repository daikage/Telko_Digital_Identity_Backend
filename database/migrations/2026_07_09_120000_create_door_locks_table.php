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
        Schema::create('door_locks', function (Blueprint $table) {
            $table->id();
            $table->string('name');                         // e.g. "Main Entrance"
            $table->string('location')->nullable();         // e.g. "Building A, Floor 2"
            $table->enum('lock_type', ['nfc', 'ble', 'both'])->default('both');
            $table->string('ble_service_uuid')->nullable(); // BLE GATT service UUID
            $table->string('ble_characteristic_uuid')->nullable(); // BLE GATT characteristic UUID
            $table->string('nfc_aid')->nullable();          // NFC Application Identifier
            $table->string('secret_key', 64);               // HMAC secret shared with the lock hardware
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('door_locks');
    }
};
