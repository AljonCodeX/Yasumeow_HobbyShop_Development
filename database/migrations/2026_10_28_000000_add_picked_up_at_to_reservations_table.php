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
            $table->timestamp('picked_up_at')->nullable()->after('pickup_by');
        });

        // Old picked-up rows: best guess is when the status last changed
        DB::table('reservations')
            ->where('status', 'picked_up')
            ->update(['picked_up_at' => DB::raw('COALESCE(status_changed_at, reserved_at)')]);
    }

    public function down(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            $table->dropColumn('picked_up_at');
        });
    }
};