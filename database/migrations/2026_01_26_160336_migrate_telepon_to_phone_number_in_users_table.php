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
        // Migrasi data dari telepon ke phone_number
        if (Schema::hasColumn('users', 'telepon')) {
            DB::statement('UPDATE users SET phone_number = telepon WHERE phone_number IS NULL');
            
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('telepon');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('telepon', 20)->nullable();
        });

        // Kembalikan data
        DB::statement('UPDATE users SET telepon = phone_number');
    }
};
