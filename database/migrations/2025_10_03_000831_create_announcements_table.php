<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('announcements', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('content');
            $table->enum('target_audience', ['all', 'students', 'teachers', 'parents', 'admin'])->default('all');
            $table->enum('priority', ['low', 'medium', 'high', 'urgent'])->default('medium');
            $table->boolean('is_active')->default(true);
            $table->date('publish_date')->nullable();
            $table->date('expiry_date')->nullable();
            $table->boolean('send_email')->default(false); // Whether to send email notification
            $table->boolean('email_sent')->default(false); // Whether email was actually sent
            $table->foreignId('posted_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index('target_audience');
            $table->index('is_active');
            $table->index('priority');
            $table->index('publish_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('announcements');
    }
};