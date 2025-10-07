<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('teacher_subject', function (Blueprint $table) {
            $table->id();
            $table->foreignId('teacher_id')->constrained('teachers')->onDelete('cascade');
            $table->foreignId('subject_id')->constrained('subjects')->onDelete('cascade');
            $table->foreignId('class_id')->constrained('classes')->onDelete('cascade');
            $table->string('academic_year', 20); // e.g., "2024-2025"
            $table->timestamps();
            
            // Ensure unique combination
            $table->unique(['teacher_id', 'subject_id', 'class_id', 'academic_year'], 'teacher_subject_class_unique');
            
            // Indexes
            $table->index('teacher_id');
            $table->index('subject_id');
            $table->index('class_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('teacher_subject');
    }
};