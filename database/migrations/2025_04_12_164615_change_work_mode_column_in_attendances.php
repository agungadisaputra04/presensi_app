<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeWorkModeColumnInAttendances extends Migration
{
    public function up(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            // Cek apakah kolom 'work_mode' sudah ada, jika belum tambahkan
            if (!Schema::hasColumn('attendances', 'work_mode')) {
                $table->string('work_mode')->nullable();
            } else {
                // Jika sudah ada, pastikan kolom 'work_mode' nullable
                $table->string('work_mode')->nullable()->change();
            }
        });
    }

    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            // Kembalikan kolom 'work_mode' menjadi tidak nullable jika diperlukan
            $table->string('work_mode')->nullable(false)->change();
        });
    }
}
