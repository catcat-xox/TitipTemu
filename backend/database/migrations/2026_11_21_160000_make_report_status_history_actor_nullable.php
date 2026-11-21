<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('report_status_histories', function (Blueprint $table): void {
            $table->unsignedBigInteger('actor_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        $fallbackUserId = DB::table('users')->min('id');
        if ($fallbackUserId !== null) {
            DB::table('report_status_histories')
                ->whereNull('actor_id')
                ->update(['actor_id' => $fallbackUserId]);
        }

        Schema::table('report_status_histories', function (Blueprint $table): void {
            $table->unsignedBigInteger('actor_id')->nullable(false)->change();
        });
    }
};
