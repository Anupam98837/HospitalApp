<?php


use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('doctor_bookings', function (Blueprint $table) {
            // one doctor, on one day, can start only one slice at a given minute
            $table->unique(
                ['doctor_id', 'appointment_date', 'slice_start'],
                'uniq_doctor_date_slice'
            );
        });
    }

    public function down(): void
    {
        Schema::table('doctor_bookings', function (Blueprint $table) {
            $table->dropUnique('uniq_doctor_date_slice');
        });
    }
};
