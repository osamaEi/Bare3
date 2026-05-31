<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone')->nullable()->after('email');
            $table->enum('role', ['admin', 'student', 'parent', 'teacher'])->default('student')->after('phone');
            $table->string('avatar')->nullable()->after('role');
            $table->enum('gender', ['male', 'female'])->nullable()->after('avatar');
            $table->date('date_of_birth')->nullable()->after('gender');
            $table->boolean('is_active')->default(true)->after('date_of_birth');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['phone', 'role', 'avatar', 'gender', 'date_of_birth', 'is_active']);
        });
    }
};
