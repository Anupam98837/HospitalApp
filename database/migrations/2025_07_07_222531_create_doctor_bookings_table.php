<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('doctor_bookings', function (Blueprint $table) {
            /* ───── Primary key ───── */
            $table->bigIncrements('id');

            /* ───── Foreign keys ───── */
            $table->unsignedBigInteger('doctor_id');
            $table->unsignedBigInteger('slot_id');
            $table->unsignedBigInteger('user_id');

            $table->foreign('doctor_id')
                  ->references('id')->on('doctors')
                  ->onDelete('cascade');

            $table->foreign('slot_id')
                  ->references('id')->on('doctor_appointment_schedules')
                  ->onDelete('cascade');

            $table->foreign('user_id')
                  ->references('id')->on('user')
                  ->onDelete('cascade');

            /* ───── Booking meta ───── */
            $table->uuid('booking_token')->unique();   // generated in controller
            $table->string('patient_name', 150);
            $table->text('patient_address')->nullable();

            // Calendar date of the appointment (derived from slot, but indexed for speed)
            $table->date('booking_date');

            $table->text('additional_note')->nullable();

            /* ───── Timestamps ───── */
            $table->timestamps();

            /* ───── Constraints & indexes ───── */
            // Prevent double-booking the same slot
            $table->unique('slot_id', 'uniq_slot_once');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('doctor_bookings');
    }
};
