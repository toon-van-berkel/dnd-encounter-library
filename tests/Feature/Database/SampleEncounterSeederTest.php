<?php

use App\Models\Encounter;
use App\Models\EncounterDifficulty;
use App\Models\EncounterType;
use App\Models\User;
use Database\Seeders\SampleEncounterSeeder;

function runSampleEncounterSeeder(): void {
    app(SampleEncounterSeeder::class)->run();
}

test('the seeder creates ten sample encounters with eight active and two inactive', function () {
    runSampleEncounterSeeder();

    $demoUser = User::where('email', 'sample-encounters@example.test')->firstOrFail();

    expect($demoUser->encounters()->count())->toBe(10)
        ->and($demoUser->encounters()->where('is_active', true)->count())->toBe(8)
        ->and($demoUser->encounters()->where('is_active', false)->count())->toBe(2);
});

test('sample encounters have valid type difficulty and user relationships', function () {
    runSampleEncounterSeeder();

    $encounters = Encounter::whereHas('user', function ($query) {
        $query->where('email', 'sample-encounters@example.test');
    })->with(['user', 'type', 'difficulty'])->get();

    expect($encounters)->toHaveCount(10);

    foreach ($encounters as $encounter) {
        expect($encounter->user)->not->toBeNull()
            ->and($encounter->type)->not->toBeNull()
            ->and($encounter->difficulty)->not->toBeNull();
    }
});

test('the sample seeder is idempotent', function () {
    runSampleEncounterSeeder();
    runSampleEncounterSeeder();

    $demoUser = User::where('email', 'sample-encounters@example.test')->firstOrFail();

    expect($demoUser->encounters()->count())->toBe(10);
});

test('existing users and encounters are preserved', function () {
    $user = User::factory()->create(['name' => 'Existing User']);
    $existingEncounter = Encounter::create([
        'user_id'       => $user->id,
        'type_id'       => EncounterType::firstOrCreate(['name' => 'Existing Type'])->id,
        'difficulty_id' => EncounterDifficulty::firstOrCreate(['name' => 'Existing Difficulty'])->id,
        'title'         => 'Existing Encounter',
        'description'   => 'Must remain unchanged.',
        'min_level'     => 1,
        'max_level'     => 3,
        'party_size'    => 4,
        'is_active'     => true,
    ]);

    runSampleEncounterSeeder();

    expect(User::find($user->id)->name)->toBe('Existing User')
        ->and(Encounter::find($existingEncounter->id)->description)->toBe('Must remain unchanged.');
});

test('the public overview shows the eight active sample encounters only', function () {
    runSampleEncounterSeeder();

    $response = $this->get(route('encounters.index'));

    $response
        ->assertSee('Goblin Ambush at Dawn')
        ->assertSee('Caverns Beneath the Mill')
        ->assertDontSee('The Silent Watchtower')
        ->assertDontSee('The Merchant Princes Puzzle');
});

test('the sample data provides at least five active encounters for US15', function () {
    runSampleEncounterSeeder();

    $demoUser = User::where('email', 'sample-encounters@example.test')->firstOrFail();

    expect($demoUser->encounters()->where('is_active', true)->count())->toBeGreaterThanOrEqual(5);
});
