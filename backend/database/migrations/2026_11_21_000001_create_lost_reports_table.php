<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lost_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('item_name', 100);
            $table->string('category', 100);
            $table->text('description');
            $table->date('lost_at');
            $table->string('location', 150);
            $table->string('photo_path');
            $table->string('status', 30)->default('pending')->index();
            $table->timestamp('terms_accepted_at');
            $table->timestamp('returned_at')->nullable()->index();
            $table->timestamp('archived_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lost_reports');
    }
};
