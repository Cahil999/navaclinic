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
            if (!Schema::hasColumn('users', 'race')) {
                $table->string('race')->nullable();
            }
            if (!Schema::hasColumn('users', 'surgery_history')) {
                $table->text('surgery_history')->nullable();
            }
            if (!Schema::hasColumn('users', 'accident_history')) {
                $table->text('accident_history')->nullable();
            }
            // Add other missing specific columns if any, checking for existence is safer
            if (!Schema::hasColumn('users', 'id_card_number'))
                $table->string('id_card_number')->nullable();
            if (!Schema::hasColumn('users', 'date_of_birth'))
                $table->date('date_of_birth')->nullable();
            if (!Schema::hasColumn('users', 'gender'))
                $table->string('gender')->nullable();
            if (!Schema::hasColumn('users', 'nationality'))
                $table->string('nationality')->nullable();
            if (!Schema::hasColumn('users', 'religion'))
                $table->string('religion')->nullable();
            if (!Schema::hasColumn('users', 'occupation'))
                $table->string('occupation')->nullable();
            if (!Schema::hasColumn('users', 'address'))
                $table->text('address')->nullable();
            if (!Schema::hasColumn('users', 'emergency_contact_name'))
                $table->string('emergency_contact_name')->nullable();
            if (!Schema::hasColumn('users', 'emergency_contact_phone'))
                $table->string('emergency_contact_phone')->nullable();
            if (!Schema::hasColumn('users', 'underlying_disease'))
                $table->text('underlying_disease')->nullable();
            if (!Schema::hasColumn('users', 'drug_allergy'))
                $table->text('drug_allergy')->nullable();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'id_card_number',
                'date_of_birth',
                'gender',
                'race',
                'nationality',
                'religion',
                'occupation',
                'address',
                'emergency_contact_name',
                'emergency_contact_phone',
                'underlying_disease',
                'surgery_history',
                'drug_allergy',
                'accident_history',
            ]);
        });
    }
};
