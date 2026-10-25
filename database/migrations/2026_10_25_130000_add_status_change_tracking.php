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
            $table->timestamp('status_changed_at')->nullable();
        });

        Schema::table('events', function (Blueprint $table) {
            $table->timestamp('status_changed_at')->nullable();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->timestamp('res_menu_seen_at')->nullable();
            $table->timestamp('event_menu_seen_at')->nullable();
        });

        // Old items stay without a change date, so they never show a badge
        DB::table('users')->update([
            'res_menu_seen_at'   => now(),
            'event_menu_seen_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::table('reservations', fn (Blueprint $table) => $table->dropColumn('status_changed_at'));
        Schema::table('events', fn (Blueprint $table) => $table->dropColumn('status_changed_at'));
        Schema::table('users', fn (Blueprint $table) => $table->dropColumn(['res_menu_seen_at', 'event_menu_seen_at']));
    }
};