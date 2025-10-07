<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exams', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // e.g., "Mid-term Exam"
            $table->foreignId('subject_id')->constrained('subjects')->onDelete('cascade');
            $table->foreignId('class_id')->constrained('classes')->onDelete('cascade');
            $table->enum('exam_type', ['quiz', 'midterm', 'final', 'assignment', 'practical'])->default('midterm');
            $table->date('exam_date');
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->integer('duration_minutes')->nullable(); // Exam duration
            $table->decimal('total_marks', 5, 2); // e.g., 100.00
            $table->decimal('passing_marks', 5, 2); // e.g., 40.00
            $table->string('academic_year', 20); // e.g., "2024-2025"
            $table->string('semester', 20)->nullable(); // e.g., "Fall", "Spring"
            $table->text('description')->nullable();
            $table->text('instructions')->nullable();
            $table->string('room_number', 20)->nullable();
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade'); // Teacher who created
            $table->boolean('is_published')->default(false); // Whether results are published
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index('subject_id');
            $table->index('class_id');
            $table->index('exam_date');
            $table->index('academic_year');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exams');
    }
};