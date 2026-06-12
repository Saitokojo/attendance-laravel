<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendance_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('office_id')->constrained('offices')->cascadeOnDelete();
            $table->date('date');
            $table->foreignId('check_in_session_id')->constrained('qr_sessions')->cascadeOnDelete();
            $table->timestamp('check_in_at');
            $table->decimal('check_in_lat', 10, 7);
            $table->decimal('check_in_lng', 10, 7);
            $table->foreignId('check_out_session_id')->nullable()->constrained('qr_sessions')->nullOnDelete();
            $table->timestamp('check_out_at')->nullable();
            $table->decimal('check_out_lat', 10, 7)->nullable();
            $table->decimal('check_out_lng', 10, 7)->nullable();
            $table->enum('status', ['on_time', 'late']);
            $table->timestamps();

            $table->unique(['user_id', 'date']);
            $table->index(['office_id', 'date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendance_records');
    }
};
