<?php

namespace App\Http\Controllers;

class PageController extends Controller
{
    // All games and activities, grouped by section
    private function gameSections(): array
    {
        $rental = [
            'icon'  => 'bi-clock-history',
            'title' => 'PS5 Rental',
            'text'  => '₱50 per hour for up to 2 players. Pick a game below and play on the shop PS5.',
        ];

        return [
            [
                'title' => 'Board Games',
                'icon'  => 'bi-dice-5-fill',
                'games' => [
                    [
                        'slug'        => 'pokemon-tcg',
                        'name'        => 'Pokemon TCG',
                        'icon'        => 'bi-suit-spade-fill',
                        'type'        => 'Card Game',
                        'description' => 'Casual play and friendly matches for all levels, from new trainers to veterans.',
                        'about'       => 'Pokemon TCG is a collectible card game where two trainers battle using decks of 60 cards. Players build a strategy around Pokemon, Trainer and Energy cards. Join our casual play sessions to learn, trade and meet other players.',
                        'guide'       => [
                            'Get a ready-to-play theme deck or starter set (60 cards).',
                            'Learn the three card types: Pokemon, Trainer and Energy.',
                            'Setup: shuffle your deck, draw 7 cards, place a Basic Pokemon as your Active Pokemon and set aside your Prize cards.',
                            'On your turn: draw a card, attach one Energy, play Trainer cards, then attack.',
                            'Win by taking all your Prize cards or by knocking out all of your opponent\'s Pokemon.',
                            'Ask the shop staff or other players if you are unsure about a rule. Beginners are welcome.',
                        ],
                    ],
                    [
                        'slug'        => 'unmatched',
                        'name'        => 'Unmatched',
                        'icon'        => 'bi-shield-fill',
                        'type'        => 'Board Game',
                        'description' => 'Fast one-on-one battles between legendary heroes on a tactical board.',
                        'about'       => 'Unmatched is a tactical board game where each player controls a hero with their own deck of cards. Heroes move across a map, play cards to attack, defend and use special abilities. Matches are quick and easy to learn.',
                        'guide'       => [
                            'Choose a set, a map and a hero. Each hero has its own deck.',
                            'Setup: place the heroes on the board, shuffle your deck and draw 5 cards.',
                            'On your turn you take two actions: maneuver (move), scheme (play a special card) or attack.',
                            'When attacked, the defender can play a defense card. Compare the results to see how much damage is dealt.',
                            'At the end of your turn, draw back up to 5 cards.',
                            'Win by reducing your opponent\'s hero to zero health.',
                        ],
                    ],
                ],
            ],
            [
                'title' => 'Physical Toy Games',
                'icon'  => 'bi-puzzle-fill',
                'games' => [
                    [
                        'slug'        => 'beyblade',
                        'name'        => 'Beyblade',
                        'icon'        => 'bi-tornado',
                        'type'        => 'Tournament',
                        'description' => 'Battle it out in weekly bey tournaments. Bring your own deck or use the shop arena.',
                        'about'       => 'Beyblade is a battle game where spinning tops are launched into a stadium to outlast and knock out each other. Join our tournaments or just battle with friends in the shop arena.',
                        'guide'       => [
                            'Get a starter set. It comes with a bey and a launcher.',
                            'Assemble your bey by snapping its parts together firmly.',
                            'Attach the launcher, place your bey on it and get ready.',
                            'Both players count "3, 2, 1, Let it rip!" and launch at the same time into the stadium.',
                            'You win by knocking your opponent out of the stadium, bursting their bey, or outspinning them.',
                            'Ask the staff about the tournament rules before joining your first event.',
                        ],
                    ],
                    [
                        'slug'        => 'tamiya-mini-4wd',
                        'name'        => 'Tamiya Mini 4WD',
                        'icon'        => 'bi-car-front-fill',
                        'type'        => 'Racing',
                        'description' => 'Build, tune and race your Mini 4WD on our track. Beginners are welcome.',
                        'about'       => 'Tamiya Mini 4WD is a model racing hobby. You build a small motorized car from a kit, tune it for speed and race it on a track. It is as much about building and tuning as it is about racing.',
                        'guide'       => [
                            'Pick a kit and read the instruction manual before you start.',
                            'Build the car carefully. Most kits only need a few basic tools.',
                            'Insert fresh batteries and test your car on a bench before racing.',
                            'Tune it: check that the wheels and rollers spin freely and fix any rubbing parts.',
                            'Race it on the shop track and watch how it behaves in the corners.',
                            'Ask the staff or experienced racers for tuning tips and the race rules.',
                        ],
                    ],
                ],
            ],
            [
                'title' => 'PS5 Games',
                'icon'  => 'bi-controller',
                'note'  => $rental,
                'games' => [
                    [
                        'slug'        => 'marvels-spider-man-2',
                        'name'        => 'Marvel\'s Spider-Man 2',
                        'icon'        => 'bi-controller',
                        'type'        => 'Action Adventure',
                        'description' => 'Swing through the city in this single-player story game.',
                        'about'       => 'A single-player action adventure game. Swing across the city, fight villains and follow the story. With 2 people sharing the rental, you can take turns playing.',
                        'guide'       => [
                            'Rent a PS5 slot at the counter (₱50 per hour for up to 2 players).',
                            'Tell the staff the game you want to play.',
                            'Take a controller and follow the on-screen tutorial at the start.',
                            'Take turns with your friend, since this is a single-player game.',
                        ],
                    ],
                    [
                        'slug'        => 'tekken-8',
                        'name'        => 'Tekken 8',
                        'icon'        => 'bi-controller',
                        'type'        => 'Fighting',
                        'description' => 'Face a friend in a one-on-one fighting game.',
                        'about'       => 'A one-on-one fighting game, so it is perfect for 2 players sharing the rental. Pick a fighter and learn their moves.',
                        'guide'       => [
                            'Rent a PS5 slot at the counter (₱50 per hour for up to 2 players).',
                            'Pick a fighter and learn their basic moves.',
                            'Play a match against your friend.',
                        ],
                    ],
                ],
            ],
        ];
    }

    public function games()
    {
        return view('games', ['sections' => $this->gameSections()]);
    }
}