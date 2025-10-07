<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendance', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->foreignId('class_id')->constrained('classes')->onDelete('cascade');
            $table->date('date');
            $table->enum('status', ['present', 'absent', 'late', 'excused'])->default('present');
            $table->foreignId('marked_by')->constrained('users')->onDelete('cascade'); // Teacher who marked
            $table->text('remarks')->nullable();
            $table->time('check_in_time')->nullable();
            $table->timestamps();
            
            // Ensure one attendance record per student per day
            $table->unique(['student_id', 'date'], 'student_date_unique');
            
            // Indexes for fast queries
            $table->index('student_id');
            $table->index('class_id');
            $table->index('date');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendance');
    }
};