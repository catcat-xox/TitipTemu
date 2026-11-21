<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('return_confirmations', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('lost_report_id')->constrained('lost_reports');
            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('admin_id')->nullable()->constrained('users');
            $table->string('status', 30)->default('pending')->index();
            $table->unsignedTinyInteger('attempt');
            $table->timestamp('reported_returned_at');
            $table->json('proof_paths');
            $table->text('note')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->text('admin_note')->nullable();
            $table->boolean('is_admin_resolution')->default(false);
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();
            $table->index(['lost_report_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('return_confirmations');
    }
};
