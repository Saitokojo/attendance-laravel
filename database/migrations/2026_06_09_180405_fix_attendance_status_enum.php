<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE attendance_records MODIFY status ENUM('on_time', 'late') NOT NULL");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE attendance_records MODIFY status ENUM('present', 'late') NOT NULL");
    }
};
