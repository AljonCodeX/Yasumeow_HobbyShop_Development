<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            $table->string('contact_name')->nullable()->after('user_id');
            $table->string('contact_phone', 20)->nullable()->after('contact_name');
            $table->string('note', 255)->nullable()->after('contact_phone');
        });
    }

    public function down(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            $table->dropColumn(['contact_name', 'contact_phone', 'note']);
        });
    }
};
