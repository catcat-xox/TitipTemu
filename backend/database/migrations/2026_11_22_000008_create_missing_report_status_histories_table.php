<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('missing_report_status_histories')) {
            Schema::create('missing_report_status_histories', function (Blueprint $table): void {
                $table->id();
                $table->foreignId('missing_report_id')->constrained('missing_reports');
                $table->foreignId('actor_id')->nullable()->constrained('users');
                $table->foreignId('admin_id')->nullable()->constrained('admins');
                $table->string('action', 30);
                $table->string('from_status', 30);
                $table->string('to_status', 30);
                $table->text('reason')->nullable();
                $table->text('note')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasIndex('missing_report_status_histories', ['missing_report_id', 'created_at'])) {
            Schema::table('missing_report_status_histories', function (Blueprint $table): void {
                $table->index(['missing_report_id', 'created_at'], 'missing_histories_report_created_index');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('missing_report_status_histories');
    }
};
