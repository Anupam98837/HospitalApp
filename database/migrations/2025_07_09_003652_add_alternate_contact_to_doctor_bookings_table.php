<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('doctor_bookings', function (Blueprint $table) {
            // Nullable because the data is optional
            $table->string('alternate_phone', 20)
                  ->nullable()
                  ->after('patient_address');   // keeps columns logically grouped

            $table->string('alternate_email', 150)
                  ->nullable()
                  ->after('alternate_phone');
        });
    }

    public function down(): void
    {
        Schema::table('doctor_bookings', function (Blueprint $table) {
            $table->dropColumn(['alternate_phone', 'alternate_email']);
        });
    }
};
