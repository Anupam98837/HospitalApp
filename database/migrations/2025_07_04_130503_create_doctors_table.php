<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('doctors', function (Blueprint $table) {
            /* ───── Primary & foreign keys ───── */
            $table->bigIncrements('id');

            $table->unsignedBigInteger('department_id');
            $table->foreign('department_id')
                  ->references('id')->on('departments')
                  ->onDelete('cascade');              // remove doctors if a department is deleted

            /* ───── Personal information ───── */
            $table->string('first_name', 100);
            $table->string('last_name', 100);
            $table->string('phone', 20)->nullable();
            $table->string('email')->unique()->nullable();
            $table->string('password');   // hashed value

            $table->string('specialty', 150)->nullable();
            $table->string('degree', 150)->nullable();
            $table->enum('sex', ['male', 'female', 'other'])->nullable();

            /* ───── Addresses ───── */
            $table->string('home_town', 150)->nullable();
            $table->text('address')->nullable();          // full residential address
            $table->text('office_address')->nullable();   // hospital/clinic address

            /* ───── Media & state ───── */
            $table->string('image_url', 255)->nullable();
            $table->boolean('is_active')->default(true);

            /* ───── Prices ───── */
            $table->decimal('visiting_charge',     10, 2)->nullable();
            $table->decimal('consultation_charge', 10, 2)->nullable();

            /* ───── Timestamps ───── */
            $table->timestamps();  // created_at, updated_at (store in UTC; Laravel will display in Asia/Kolkata)
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('doctors');
    }
};
