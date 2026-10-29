<?php

use App\Models\Encounter;
use App\Models\EncounterDifficulty;
use App\Models\EncounterType;
use App\Models\User;

function filterEncounter(
    User $user,
    EncounterType $type,
    EncounterDifficulty $difficulty,
    string $title,
    string $description = 'A searchable encounter.',
    bool $isActive = true
): Encounter {
    return Encounter::create([
        'user_id'       => $user->id,
        'type_id'       => $type->id,
        'difficulty_id' => $difficulty->id,
        'title'         => $title,
        'description'   => $description,
        'min_level'     => 1,
        'max_level'     => 3,
        'party_size'    => 4,
        'is_active'     => $isActive,
    ]);
}

function filterFixtures(): array {
    $user = User::factory()->create();
    $combat = EncounterType::create(['name' => 'Combat Filter']);
    $social = EncounterType::create(['name' => 'Social Filter']);
    $easy = EncounterDifficulty::create(['name' => 'Easy Filter']);
    $hard = EncounterDifficulty::create(['name' => 'Hard Filter']);

    return [
        'combatEasy' => filterEncounter($user, $combat, $easy, 'Goblin Ambush', 'A goblin attack.'),
        'combatHard' => filterEncounter($user, $combat, $hard, 'Dragon Attack', 'A dragon attack.'),
        'socialEasy' => filterEncounter($user, $social, $easy, 'Goblin Negotiation', 'A goblin parley.'),
        'socialHard' => filterEncounter($user, $social, $hard, 'Royal Feast', 'A peaceful celebration.'),
        'combat'     => $combat,
        'social'     => $social,
        'easy'       => $easy,
        'hard'       => $hard,
    ];
}

test('guests can filter encounters by type', function () {
    $fixtures = filterFixtures();

    $this->get(route('encounters.index', ['type_id' => $fixtures['combat']->id]))
        ->assertSee('Goblin Ambush')
        ->assertSee('Dragon Attack')
        ->assertDontSee('Goblin Negotiation')
        ->assertDontSee('Royal Feast');
});

test('guests can filter encounters by difficulty', function () {
    $fixtures = filterFixtures();

    $this->get(route('encounters.index', ['difficulty_id' => $fixtures['hard']->id]))
        ->assertSee('Dragon Attack')
        ->assertSee('Royal Feast')
        ->assertDontSee('Goblin Ambush')
        ->assertDontSee('Goblin Negotiation');
});

test('guests can combine type and difficulty filters', function () {
    $fixtures = filterFixtures();

    $this->get(route('encounters.index', [
        'type_id'       => $fixtures['combat']->id,
        'difficulty_id' => $fixtures['easy']->id,
    ]))
        ->assertSee('Goblin Ambush')
        ->assertDontSee('Dragon Attack')
        ->assertDontSee('Goblin Negotiation')
        ->assertDontSee('Royal Feast');
});

test('guests can combine search with a type filter', function () {
    $fixtures = filterFixtures();

    $this->get(route('encounters.index', [
        'q'       => 'goblin',
        'type_id' => $fixtures['combat']->id,
    ]))
        ->assertSee('Goblin Ambush')
        ->assertDontSee('Goblin Negotiation');
});

test('guests can combine search with a difficulty filter', function () {
    $fixtures = filterFixtures();

    $this->get(route('encounters.index', [
        'q'             => 'goblin',
        'difficulty_id' => $fixtures['easy']->id,
    ]))
        ->assertSee('Goblin Ambush')
        ->assertSee('Goblin Negotiation')
        ->assertDontSee('Dragon Attack');
});

test('guests can combine search, type, and difficulty filters', function () {
    $fixtures = filterFixtures();

    $this->get(route('encounters.index', [
        'q'             => 'goblin',
        'type_id'       => $fixtures['combat']->id,
        'difficulty_id' => $fixtures['easy']->id,
    ]))
        ->assertSee('Goblin Ambush')
        ->assertDontSee('Goblin Negotiation')
        ->assertDontSee('Dragon Attack');
});

test('partial search matches continue to work with filters', function () {
    $fixtures = filterFixtures();

    $this->get(route('encounters.index', [
        'q'       => 'gob',
        'type_id' => $fixtures['social']->id,
    ]))
        ->assertSee('Goblin Negotiation')
        ->assertDontSee('Royal Feast');
});

test('inactive encounters remain hidden with filters', function () {
    $fixtures = filterFixtures();
    filterEncounter(
        User::factory()->create(),
        $fixtures['combat'],
        $fixtures['easy'],
        'Inactive Goblin',
        'An inactive goblin encounter.',
        false
    );

    $this->get(route('encounters.index', [
        'q'             => 'goblin',
        'type_id'       => $fixtures['combat']->id,
        'difficulty_id' => $fixtures['easy']->id,
    ]))
        ->assertSee('Goblin Ambush')
        ->assertDontSee('Inactive Goblin');
});

test('empty filters show all active encounters', function () {
    $fixtures = filterFixtures();

    $this->get(route('encounters.index', ['type_id' => '', 'difficulty_id' => '']))
        ->assertSee('Goblin Ambush')
        ->assertSee('Dragon Attack')
        ->assertSee('Goblin Negotiation')
        ->assertSee('Royal Feast');
});

test('a combination without results shows a clear empty state', function () {
    $fixtures = filterFixtures();

    $this->get(route('encounters.index', [
        'q'             => 'dragon',
        'type_id'       => $fixtures['social']->id,
        'difficulty_id' => $fixtures['easy']->id,
    ]))->assertSee('No encounters matched your search.');
});

test('selected filters and search term are retained', function () {
    $fixtures = filterFixtures();

    $this->get(route('encounters.index', [
        'q'             => 'goblin',
        'type_id'       => $fixtures['combat']->id,
        'difficulty_id' => $fixtures['easy']->id,
    ]))
        ->assertSee('value="goblin"', false)
        ->assertSee('value="'.$fixtures['combat']->id.'"', false)
        ->assertSee('value="'.$fixtures['easy']->id.'"', false)
        ->assertSee('selected', false);
});

test('unknown filter values do not cause a server error', function () {
    $this->get(route('encounters.index', [
        'type_id'       => 999999,
        'difficulty_id' => 'unknown',
    ]))
        ->assertOk()
        ->assertSee('No encounters found.');
});

test('guests can use the encounter filters', function () {
    $fixtures = filterFixtures();

    $this->get(route('encounters.index', ['type_id' => $fixtures['combat']->id]))
        ->assertOk()
        ->assertSee('Combat Filter')
        ->assertSee('Easy Filter');
});
