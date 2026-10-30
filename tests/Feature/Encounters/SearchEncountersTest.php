<?php

use App\Models\Encounter;
use App\Models\EncounterDifficulty;
use App\Models\EncounterType;
use App\Models\User;

function searchableEncounter(User $user, string $title, string $description, bool $isActive = true): Encounter {
    return Encounter::create([
        'user_id'       => $user->id,
        'type_id'       => EncounterType::firstOrCreate(['name' => 'Combat'])->id,
        'difficulty_id' => EncounterDifficulty::firstOrCreate(['name' => 'Easy'])->id,
        'title'         => $title,
        'description'   => $description,
        'min_level'     => 1,
        'max_level'     => 3,
        'party_size'    => 4,
        'is_active'     => $isActive,
    ]);
}

test('guests can search encounters by title', function () {
    $user = User::factory()->create();
    $matchingEncounter = searchableEncounter($user, 'Goblin Ambush', 'A roadside battle.');
    $otherEncounter = searchableEncounter($user, 'Haunted Manor', 'A quiet investigation.');

    $this->get(route('encounters.index', ['q' => 'Goblin']))
        ->assertSee($matchingEncounter->title)
        ->assertDontSee($otherEncounter->title);
});

test('guests can search encounters by description', function () {
    $user = User::factory()->create();
    $matchingEncounter = searchableEncounter($user, 'Roadside Trouble', 'A goblin patrol blocks the road.');
    $otherEncounter = searchableEncounter($user, 'Haunted Manor', 'A quiet investigation.');

    $this->get(route('encounters.index', ['q' => 'goblin patrol']))
        ->assertSee($matchingEncounter->title)
        ->assertDontSee($otherEncounter->title);
});

test('search supports partial matches and only shows matching encounters', function () {
    $user = User::factory()->create();
    $matchingEncounter = searchableEncounter($user, 'The Goblin Caves', 'Explore the caves.');
    $otherEncounter = searchableEncounter($user, 'Dragon Temple', 'A dangerous shrine.');

    $this->get(route('encounters.index', ['q' => 'gob']))
        ->assertSee($matchingEncounter->title)
        ->assertDontSee($otherEncounter->title);
});

test('an empty search shows all active encounters', function () {
    $user = User::factory()->create();
    $firstEncounter = searchableEncounter($user, 'First Encounter', 'First description.');
    $secondEncounter = searchableEncounter($user, 'Second Encounter', 'Second description.');

    $this->get(route('encounters.index', ['q' => '   ']))
        ->assertSee($firstEncounter->title)
        ->assertSee($secondEncounter->title);
});

test('inactive matching encounters remain hidden', function () {
    $user = User::factory()->create();
    $activeEncounter = searchableEncounter($user, 'Active Goblin Encounter', 'A goblin attack.');
    $inactiveEncounter = searchableEncounter($user, 'Inactive Goblin Encounter', 'A goblin attack.', false);

    $this->get(route('encounters.index', ['q' => 'goblin']))
        ->assertSee($activeEncounter->title)
        ->assertDontSee($inactiveEncounter->title);
});

test('a search with no matches shows a specific empty state', function () {
    searchableEncounter(User::factory()->create(), 'Goblin Ambush', 'A roadside battle.');

    $this->get(route('encounters.index', ['q' => 'dragon']))
        ->assertSee('No encounters matched your search.');
});

test('the search form retains the trimmed search term', function () {
    $this->get(route('encounters.index', ['q' => '  goblin  ']))
        ->assertSee('value="goblin"', false)
        ->assertSee('method="GET"', false)
        ->assertSee('name="q"', false);
});
