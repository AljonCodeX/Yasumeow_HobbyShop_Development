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
            $table->string('reference', 20)->nullable()->unique()->after('id');
        });

        // Old reservations: same format, counted per day in the order they were made
        $counters = [];

        DB::table('reservations')->orderBy('reserved_at')->orderBy('id')->get(['id', 'reserved_at'])
            ->each(function ($row) use (&$counters) {
                $day = date('Ymd', strtotime($row->reserved_at));
                $counters[$day] = ($counters[$day] ?? 0) + 1;

                DB::table('reservations')->where('id', $row->id)->update([
                    'reference' => $day . '-' . str_pad($counters[$day], 3, '0', STR_PAD_LEFT),
                ]);
            });
    }

    public function down(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            $table->dropColumn('reference');
        });
    }
};