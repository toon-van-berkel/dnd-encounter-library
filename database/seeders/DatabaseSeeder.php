<?php

namespace Database\Seeders;

use App\Models\EncounterDifficulty;
use App\Models\EncounterType;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        EncounterType::upsert([
            ['name' => 'Combat'],
            ['name' => 'Social'],
            ['name' => 'Exploration'],
            ['name' => 'Puzzle'],
        ], ['name']);

        EncounterDifficulty::upsert([
            ['name' => 'Easy'],
            ['name' => 'Medium'],
            ['name' => 'Hard'],
            ['name' => 'Deadly'],
        ], ['name']);

        User::factory()->create([
            'name'  => 'Test User',
            'email' => 'test@example.com',
        ]);
    }
}
