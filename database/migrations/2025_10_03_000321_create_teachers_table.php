<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('teachers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained('users')->onDelete('cascade');
            $table->string('employee_number', 50)->unique(); // e.g., "EMP2024001"
            $table->string('qualification'); // e.g., "B.Ed", "M.Sc"
            $table->string('specialization')->nullable(); // e.g., "Mathematics", "Physics"
            $table->decimal('salary', 10, 2)->nullable();
            $table->date('joining_date');
            $table->date('date_of_birth');
            $table->enum('gender', ['male', 'female', 'other']);
            $table->enum('blood_group', ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'])->nullable();
            $table->string('nationality', 50)->default('Nigerian');
            
            // Employment Details
            $table->enum('employment_type', ['full-time', 'part-time', 'contract'])->default('full-time');
            $table->text('experience')->nullable(); // Previous experience
            $table->text('certifications')->nullable(); // Additional certifications
            
            // Emergency Contact
            $table->string('emergency_contact_name')->nullable();
            $table->string('emergency_contact_phone', 20)->nullable();
            $table->string('emergency_contact_relation', 50)->nullable();
            
            // Additional Information
            $table->text('remarks')->nullable();
            $table->boolean('is_active')->default(true);
            
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index('employee_number');
            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('teachers');
    }
};