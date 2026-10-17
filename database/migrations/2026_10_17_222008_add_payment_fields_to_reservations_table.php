<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            $table->decimal('amount_total', 10, 2)->default(0)->after('status');
            $table->decimal('amount_due', 10, 2)->default(0)->after('amount_total');
            $table->decimal('amount_paid', 10, 2)->default(0)->after('amount_due');
            $table->string('paymongo_session_id')->nullable()->index()->after('amount_paid');
        });
    }

    public function down(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            $table->dropColumn(['amount_total', 'amount_due', 'amount_paid', 'paymongo_session_id']);
        });
    }
};