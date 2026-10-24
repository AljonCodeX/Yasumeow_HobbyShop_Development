<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ratings', function (Blueprint $table) {
            $table->dropForeign(['reservation_id']);
            $table->dropColumn(['reservation_id', 'comment', 'updated_at']);
        });
    }

    public function down(): void
    {
        Schema::table('ratings', function (Blueprint $table) {
            $table->foreignId('reservation_id')->nullable()->constrained()->cascadeOnDelete();
            $table->text('comment')->nullable();
            $table->timestamp('updated_at')->nullable();
        });
    }
};