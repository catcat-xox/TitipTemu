<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('report_status_histories', function (Blueprint $table): void {
            $table->foreignId('admin_id')->nullable()->after('actor_id')->constrained('admins');
        });
    }

    public function down(): void
    {
        Schema::table('report_status_histories', function (Blueprint $table): void {
            $table->dropColumn('admin_id');
        });
    }
};
