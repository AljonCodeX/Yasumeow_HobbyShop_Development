<?php

namespace Database\Seeders;

use App\Models\Event;
use App\Models\Game;
use Illuminate\Database\Seeder;

class EventSeeder extends Seeder
{
    public function run(): void
    {
        $events = [
            ['game' => 'beyblade',        'title' => 'Beyblade Tournament',     'days' => 7,  'hour' => 14, 'slots' => 16, 'price' => 100],
            ['game' => 'tamiya-mini-4wd', 'title' => 'Tamiya Mini 4WD Race',    'days' => 14, 'hour' => 10, 'slots' => 20, 'price' => 0],
            ['game' => 'pokemon-tcg',     'title' => 'Pokemon TCG Casual Play', 'days' => 21, 'hour' => 13, 'slots' => 24, 'price' => 0],
            ['game' => 'tekken-8',        'title' => 'PS5 Game Night',          'days' => 28, 'hour' => 17, 'slots' => 12, 'price' => 150],
        ];

        foreach ($events as $e) {
            $game = Game::where('slug', $e['game'])->first();

            if (! $game) {
                continue; // run GameSeeder first
            }

            Event::updateOrCreate(
                ['title' => $e['title']],
                [
                    'game_id'    => $game->id,
                    'description' => 'Join us at Yasumeow Hobby Shop. Beginners are welcome.',
                    'event_date' => now()->addDays($e['days'])->setTime($e['hour'], 0),
                    'max_slots'  => $e['slots'],
                    'price'      => $e['price'],
                    'status'     => 'open',
                ]
            );
        }
    }
}