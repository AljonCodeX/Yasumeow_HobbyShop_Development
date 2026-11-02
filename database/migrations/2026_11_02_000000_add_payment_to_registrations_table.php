<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('registrations', function (Blueprint $table) {
            $table->string('payment_method')->nullable();
            // paid | unpaid (pay in store) | awaiting_payment (QR Ph, 30 min). Old rows count as paid.
            $table->string('payment_status')->default('paid');
            $table->string('paymongo_session_id')->nullable()->index();
        });
    }

    public function down(): void
    {
        Schema::table('registrations', function (Blueprint $table) {
            $table->dropColumn(['payment_method', 'payment_status', 'paymongo_session_id']);
        });
    }
};