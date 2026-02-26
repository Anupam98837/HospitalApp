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
        Schema::create('doctor_appointment_schedules', function (Blueprint $table) {
            /* ───── Primary / foreign keys ───── */
            $table->bigIncrements('id');

            $table->foreignId('doctor_id')
                  ->constrained('doctors')
                  ->cascadeOnDelete();

            /* ───── Weekly time window ───── */
            // 0 = Sunday … 6 = Saturday
            $table->tinyInteger('day_of_week');
            $table->time('start_time');
            $table->time('end_time');

            /* ───── Optional meta ───── */
            $table->string('appointment_type', 50)->nullable();
            $table->string('location',         100)->nullable();

            $table->timestamps();

            /* ───── Constraints & indexes ───── */
            // Prevent identical start-times for the same doctor on the same day
            $table->unique(
                ['doctor_id', 'day_of_week', 'start_time'],
                'uniq_doctor_day_start'
            );

            // Speeds up overlap checks
            $table->index(
                ['doctor_id', 'day_of_week', 'start_time', 'end_time'],
                'idx_doctor_day_time'
            );
        });

        /* ───── CHECK constraint (optional – MySQL ≥ 8.0.16) ───── */
        if (DB::getDriverName() === 'mysql') {
            DB::statement("
                ALTER TABLE doctor_appointment_schedules
                ADD CONSTRAINT chk_time_window
                CHECK (end_time > start_time)
            ");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('doctor_appointment_schedules');
    }
};
