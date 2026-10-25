<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->timestamp('fav_avatar_seen_at')->nullable();
            $table->timestamp('fav_menu_seen_at')->nullable();
        });

        // Existing favourites count as already seen
        DB::table('users')->update([
            'fav_avatar_seen_at' => now(),
            'fav_menu_seen_at'   => now(),
        ]);
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['fav_avatar_seen_at', 'fav_menu_seen_at']);
        });
    }
};