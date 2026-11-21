<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->string('nim_nip', 50)->nullable()->index()->after('role');
            $table->string('user_type', 20)->default('student')->index()->after('nim_nip');
            $table->string('faculty')->nullable()->after('user_type');
            $table->string('study_program')->nullable()->after('faculty');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->dropIndex(['nim_nip']);
            $table->dropIndex(['user_type']);
            $table->dropColumn(['nim_nip', 'user_type', 'faculty', 'study_program']);
        });
    }
};
