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
        Schema::table('door_locks', function (Blueprint $table) {
            $table->string('tuya_device_id')->nullable()->after('nfc_aid');
            // Modify the enum to include 'tuya'
            DB::statement("ALTER TABLE door_locks MODIFY COLUMN lock_type ENUM('nfc', 'ble', 'both', 'tuya') DEFAULT 'both'");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('door_locks', function (Blueprint $table) {
            $table->dropColumn('tuya_device_id');
            // Revert enum
            DB::statement("ALTER TABLE door_locks MODIFY COLUMN lock_type ENUM('nfc', 'ble', 'both') DEFAULT 'both'");
        });
    }
};
