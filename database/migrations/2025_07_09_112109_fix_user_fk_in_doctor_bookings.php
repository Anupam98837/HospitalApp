<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('doctor_bookings', function (Blueprint $table) {
            // 1) drop the FK that points to `users`
            $table->dropForeign(['user_id']);

            // 2) re-create FK to the correct table name `user`
            $table->foreign('user_id')
                  ->references('id')->on('user')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('doctor_bookings', function (Blueprint $table) {
            $table->dropForeign(['user_id']);

            // restore FK to `users`
            $table->foreign('user_id')
                  ->references('id')->on('users')
                  ->onDelete('cascade');
        });
    }
};
