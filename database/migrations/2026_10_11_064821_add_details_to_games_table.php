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
    Schema::table('games', function (Blueprint $table) {
        $table->string('slug')->unique()->after('name');
        $table->string('section')->after('slug');          // Board Games, Physical Toy Games, PS5 Games
        $table->string('type')->nullable()->after('section'); // Card Game, Racing, Fighting...
        $table->string('icon')->nullable()->after('type');    // e.g. bi-dice-5-fill
        $table->text('description')->nullable()->after('icon');
        $table->text('about')->nullable()->after('description');
        $table->json('guide')->nullable()->after('about');    // list of steps
        $table->boolean('is_active')->default(true)->after('guide');
        $table->unsignedInteger('sort_order')->default(0)->after('is_active');
    });
}

public function down(): void
{
    Schema::table('games', function (Blueprint $table) {
        $table->dropColumn([
            'slug', 'section', 'type', 'icon', 'description',
            'about', 'guide', 'is_active', 'sort_order',
        ]);
    });
}
};
