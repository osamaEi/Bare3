<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('scorm_tracking', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('scorm_id')->constrained('scorm_packages')->cascadeOnDelete();
            $table->foreignId('lesson_id')->constrained()->cascadeOnDelete();
            $table->enum('status', ['not_started', 'incomplete', 'completed', 'passed', 'failed'])->default('not_started');
            $table->decimal('score', 5, 2)->nullable();
            $table->json('raw_data')->nullable();
            $table->string('session_time')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->unique(['student_id', 'scorm_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('scorm_tracking');
    }
};
