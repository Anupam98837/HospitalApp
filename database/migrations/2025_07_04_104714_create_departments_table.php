<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('departments', function (Blueprint $table) {
            // Primary key ─ BIGINT UNSIGNED AUTO_INCREMENT in MySQL
            $table->bigIncrements('id');

            // Main columns
            $table->string('title', 150);
            $table->text('description')->nullable();
            $table->string('image_url', 255)->nullable();
            $table->string('status', 255)->default('inactive');

            // created_at & updated_at (stored UTC, shown Asia/Kolkata)
            $table->timestamps();          // use ->timestampsTz() if you switch to Postgres
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('departments');
    }
};
