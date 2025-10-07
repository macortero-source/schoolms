<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('grades', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->foreignId('exam_id')->constrained('exams')->onDelete('cascade');
            $table->decimal('marks_obtained', 5, 2); // e.g., 85.50
            $table->string('grade', 5)->nullable(); // e.g., "A+", "B", "C"
            $table->decimal('grade_point', 3, 2)->nullable(); // e.g., 4.00, 3.50
            $table->enum('status', ['pass', 'fail', 'absent'])->default('pass');
            $table->text('remarks')->nullable();
            $table->foreignId('entered_by')->constrained('users')->onDelete('cascade'); // Teacher who entered grade
            $table->timestamp('entered_at')->nullable();
            $table->timestamps();
            
            // Ensure one grade per student per exam
            $table->unique(['student_id', 'exam_id'], 'student_exam_unique');
            
            // Indexes
            $table->index('student_id');
            $table->index('exam_id');
            $table->index('grade');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('grades');
    }
};