<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            $table->timestamp('pickup_by')->nullable()->after('reserved_at');
        });

        // Give existing pay-in-store reservations a deadline too
        DB::table('reservations')
            ->where('status', 'pending')
            ->where('payment_method', 'store')
            ->update(['pickup_by' => now()->addDays(2)]);
    }

    public function down(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            $table->dropColumn('pickup_by');
        });
    }
};