<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained('users')->onDelete('cascade');
            $table->string('student_number', 50)->unique(); // e.g., "STU2024001"
            $table->foreignId('class_id')->nullable()->constrained('classes')->onDelete('set null');
            $table->string('admission_number', 50)->unique();
            $table->date('admission_date');
            $table->string('academic_year', 20); // e.g., "2024-2025"
            $table->date('date_of_birth');
            $table->enum('gender', ['male', 'female', 'other']);
            $table->enum('blood_group', ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'])->nullable();
            $table->string('nationality', 50)->default('Nigerian');
            $table->string('religion', 50)->nullable();
            
            // Parent/Guardian Information
            $table->string('parent_name');
            $table->string('parent_phone', 20);
            $table->string('parent_email')->nullable();
            $table->string('parent_occupation')->nullable();
            $table->text('parent_address')->nullable();
            
            // Emergency Contact
            $table->string('emergency_contact_name')->nullable();
            $table->string('emergency_contact_phone', 20)->nullable();
            $table->string('emergency_contact_relation', 50)->nullable();
            
            // Medical Information
            $table->text('medical_conditions')->nullable();
            $table->text('allergies')->nullable();
            
            // Additional Information
            $table->text('previous_school')->nullable();
            $table->text('remarks')->nullable();
            $table->boolean('is_active')->default(true);
            
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index('student_number');
            $table->index('admission_number');
            $table->index('class_id');
            $table->index('academic_year');
            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};