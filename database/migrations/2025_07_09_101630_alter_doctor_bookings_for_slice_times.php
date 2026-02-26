<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('doctor_bookings', function (Blueprint $table) {

            /* 1️⃣ Rename booking_date → appointment_date (needs doctrine/dbal) */
            if (Schema::hasColumn('doctor_bookings', 'booking_date')) {
                $table->renameColumn('booking_date', 'appointment_date');
            }

            /* 2️⃣ Add slice_start if it doesn’t exist yet */
            if (!Schema::hasColumn('doctor_bookings', 'slice_start')) {
                $table->time('slice_start')
                      ->after('appointment_date')
                      ->comment('Exact 30-min slice start (HH:MM:SS)');
            }

            /* 3️⃣ Create composite unique index (adds left-prefix for FK) */
            $table->unique(
                ['slot_id', 'appointment_date', 'slice_start'],
                'uniq_slot_date_slice'
            );

            /* 4️⃣ Drop old unique index — only if it’s still there */
            DB::statement("
                ALTER TABLE doctor_bookings
                DROP INDEX IF EXISTS uniq_slot_once
            ");
        });

        /* 5️⃣ Fix the user_id FK only if it points to the wrong table */
        $fkWrong = DB::selectOne("
            SELECT CONSTRAINT_NAME
            FROM information_schema.KEY_COLUMN_USAGE
            WHERE TABLE_SCHEMA = DATABASE()
              AND TABLE_NAME   = 'doctor_bookings'
              AND COLUMN_NAME  = 'user_id'
              AND REFERENCED_TABLE_NAME = 'user'
            LIMIT 1
        ");

        if ($fkWrong) {
            Schema::table('doctor_bookings', function (Blueprint $table) use ($fkWrong) {
                // drop wrong FK
                $table->dropForeign($fkWrong->CONSTRAINT_NAME);

                // create correct FK
                $table->foreign('user_id')
                      ->references('id')->on('users')
                      ->onDelete('cascade');
            });
        }
    }

    public function down(): void
    {
        Schema::table('doctor_bookings', function (Blueprint $table) {

            /* reverse in safe order */
            DB::statement("
                ALTER TABLE doctor_bookings
                DROP INDEX IF EXISTS uniq_slot_date_slice
            ");

            if (!Schema::hasColumn('doctor_bookings', 'booking_date') &&
                 Schema::hasColumn('doctor_bookings', 'appointment_date')) {
                $table->renameColumn('appointment_date', 'booking_date');
            }

            if (Schema::hasColumn('doctor_bookings', 'slice_start')) {
                $table->dropColumn('slice_start');
            }

            /* re-add the old single-column unique index */
            $table->unique('slot_id', 'uniq_slot_once');
        });
    }
};
