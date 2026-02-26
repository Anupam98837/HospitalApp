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
        Schema::create('prescriptions', function (Blueprint $table) {
            // Primary key
            $table->bigIncrements('id');

            // Relations
            $table->foreignId('booking_id')
                  ->constrained('doctor_bookings')
                  ->cascadeOnDelete();

            $table->foreignId('doctor_id')
                  ->constrained('doctors')
                  ->cascadeOnDelete();

            // ⚠️ Important: patient table is `user` (singular) in your schema
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')
                  ->references('id')->on('user')
                  ->cascadeOnDelete();

            // Prescription details
            $table->timestampTz('prescription_date')->useCurrent();
            $table->text('notes')->nullable();
            $table->date('follow_up_date')->nullable();
            $table->jsonb('medicines')->nullable(); // medicine list (JSON array)

            // Timestamps
            $table->timestampsTz();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prescriptions');
    }
};
