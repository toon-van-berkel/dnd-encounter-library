<?php

namespace Database\Seeders;

use App\Models\Encounter;
use App\Models\EncounterDifficulty;
use App\Models\EncounterType;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class SampleEncounterSeeder extends Seeder {
    public function run(): void {
        $demoUser = User::firstOrCreate(
            ['email' => 'sample-encounters@example.test'],
            [
                'name'     => 'Sample Encounter Author',
                'password' => Hash::make(Str::random(64)),
            ]
        );

        $encounterData = [
            [
                'title'         => 'Goblin Ambush at Dawn',
                'type'          => 'Combat',
                'difficulty'    => 'Easy',
                'description'   => 'A goblin patrol attacks travelers on a forest road at first light.',
                'min_level'     => 1,
                'max_level'     => 3,
                'party_size'    => 4,
                'is_active'     => true,
            ],
            [
                'title'         => 'The Haunted Crypt',
                'type'          => 'Exploration',
                'difficulty'    => 'Hard',
                'description'   => 'Ancient bells ring beneath a crypt where restless spirits guard a lost relic.',
                'min_level'     => 5,
                'max_level'     => 8,
                'party_size'    => 4,
                'is_active'     => true,
            ],
            [
                'title'         => 'Tavern Negotiation',
                'type'          => 'Social',
                'difficulty'    => 'Medium',
                'description'   => 'A tense negotiation in a crowded tavern could prevent a guild war.',
                'min_level'     => 2,
                'max_level'     => 5,
                'party_size'    => 5,
                'is_active'     => true,
            ],
            [
                'title'         => 'The Forest Path',
                'type'          => 'Exploration',
                'difficulty'    => 'Easy',
                'description'   => 'A misty forest trail hides unusual tracks and a choice of dangerous routes.',
                'min_level'     => 1,
                'max_level'     => 4,
                'party_size'    => 3,
                'is_active'     => true,
            ],
            [
                'title'         => 'Riddle of the Bronze Door',
                'type'          => 'Puzzle',
                'difficulty'    => 'Medium',
                'description'   => 'A bronze door opens only when the party solves the verses of an ancient riddle.',
                'min_level'     => 3,
                'max_level'     => 6,
                'party_size'    => 4,
                'is_active'     => true,
            ],
            [
                'title'         => 'Dragon Over the Valley',
                'type'          => 'Combat',
                'difficulty'    => 'Deadly',
                'description'   => 'A young dragon claims a mountain pass and demands tribute from nearby villages.',
                'min_level'     => 10,
                'max_level'     => 14,
                'party_size'    => 5,
                'is_active'     => true,
            ],
            [
                'title'         => 'Festival of Masks',
                'type'          => 'Social',
                'difficulty'    => 'Hard',
                'description'   => 'The characters must identify a spy during a masked festival without causing a panic.',
                'min_level'     => 6,
                'max_level'     => 9,
                'party_size'    => 6,
                'is_active'     => true,
            ],
            [
                'title'         => 'Caverns Beneath the Mill',
                'type'          => 'Exploration',
                'difficulty'    => 'Hard',
                'description'   => 'A collapsed mill reveals caverns filled with strange fungi and a forgotten shrine.',
                'min_level'     => 7,
                'max_level'     => 11,
                'party_size'    => 4,
                'is_active'     => true,
            ],
            [
                'title'         => 'The Silent Watchtower',
                'type'          => 'Combat',
                'difficulty'    => 'Deadly',
                'description'   => 'A watchtower has gone silent, and something dangerous now watches the surrounding plains.',
                'min_level'     => 12,
                'max_level'     => 16,
                'party_size'    => 5,
                'is_active'     => false,
            ],
            [
                'title'         => 'The Merchant Princes Puzzle',
                'type'          => 'Puzzle',
                'difficulty'    => 'Hard',
                'description'   => 'A sealed merchant vault contains clues to a vanished caravan and a dangerous secret.',
                'min_level'     => 8,
                'max_level'     => 12,
                'party_size'    => 4,
                'is_active'     => false,
            ],
        ];

        foreach ($encounterData as $data) {
            $type = EncounterType::firstOrCreate(['name' => $data['type']]);
            $difficulty = EncounterDifficulty::firstOrCreate(['name' => $data['difficulty']]);

            Encounter::firstOrCreate(
                [
                    'user_id' => $demoUser->id,
                    'title'   => $data['title'],
                ],
                [
                    'type_id'       => $type->id,
                    'difficulty_id' => $difficulty->id,
                    'description'   => $data['description'],
                    'min_level'     => $data['min_level'],
                    'max_level'     => $data['max_level'],
                    'party_size'    => $data['party_size'],
                    'is_active'     => $data['is_active'],
                ]
            );
        }
    }
}
