<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user', function (Blueprint $table) {
            // Primary key
            $table->bigIncrements('id');

            // User info
            $table->string('name', 150);
            $table->string('email')->unique();
            $table->char('phone', 10);
            $table->text('address')->nullable();

            // Timestamps
            $table->timestamps();
        });

        // Enforce exactly 10 digits on phone (MySQL 8.0.16+)
        DB::statement("
            ALTER TABLE `user`
            ADD CONSTRAINT chk_user_phone
            CHECK (phone REGEXP '^[0-9]{10}$')
        ");
    }

    public function down(): void
    {
        Schema::dropIfExists('user');
    }
};
