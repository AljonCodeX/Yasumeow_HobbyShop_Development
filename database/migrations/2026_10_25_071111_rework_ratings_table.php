<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ratings', function (Blueprint $table) {
            if (Schema::hasColumn('ratings', 'reservation_id')) {
                $table->dropForeign(['reservation_id']);
                $table->dropColumn('reservation_id');
            }
            if (Schema::hasColumn('ratings', 'comment')) {
                $table->dropColumn('comment');
            }
            if (Schema::hasColumn('ratings', 'updated_at')) {
                $table->dropColumn('updated_at');
            }
        });

        Schema::table('ratings', function (Blueprint $table) {
            $table->foreignId('reservation_id')->nullable()->unique()->constrained()->cascadeOnDelete();
            $table->foreignId('event_id')->nullable()->constrained()->cascadeOnDelete();
            $table->unique(['user_id', 'event_id']);
        });
    }

    public function down(): void
    {
        Schema::table('ratings', function (Blueprint $table) {
            $table->dropUnique(['user_id', 'event_id']);
            $table->dropForeign(['event_id']);
            $table->dropColumn('event_id');
            $table->text('comment')->nullable();
            $table->timestamp('updated_at')->nullable();
        });
    }
};