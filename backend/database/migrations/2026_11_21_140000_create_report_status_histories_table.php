<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('report_status_histories', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('lost_report_id')->constrained('lost_reports');
            $table->foreignId('actor_id')->constrained('users');
            $table->string('action', 30);
            $table->string('from_status', 30);
            $table->string('to_status', 30);
            $table->text('reason')->nullable();
            $table->text('note')->nullable();
            $table->timestamps();
            $table->index(['lost_report_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('report_status_histories');
    }
};
