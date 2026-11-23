<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('missing_reports', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('item_name', 100);
            $table->string('category', 100);
            $table->text('description');
            $table->date('lost_at');
            $table->string('location', 150);
            $table->string('contact_phone', 30);
            $table->string('photo_path');
            $table->string('status', 30)->default('pending')->index();
            $table->timestamp('terms_accepted_at');
            $table->timestamp('found_at')->nullable()->index();
            $table->timestamp('archived_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('missing_reports');
    }
};
