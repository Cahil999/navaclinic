<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_doctor')->default(false)->after('is_admin');
        });

        // Backfill existing doctors
        $doctorUserIds = \Illuminate\Support\Facades\DB::table('doctors')->whereNotNull('user_id')->pluck('user_id');
        if ($doctorUserIds->isNotEmpty()) {
            \Illuminate\Support\Facades\DB::table('users')->whereIn('id', $doctorUserIds)->update(['is_doctor' => true]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('is_doctor');
        });
    }
};
