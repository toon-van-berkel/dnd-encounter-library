<?php

use App\Models\Encounter;
use App\Models\EncounterDifficulty;
use App\Models\EncounterType;
use App\Models\User;

function browseEncounterPayload(User $user, EncounterType $type, EncounterDifficulty $difficulty, bool $isActive = true): array {
    return [
        'user_id'       => $user->id,
        'type_id'       => $type->id,
        'difficulty_id' => $difficulty->id,
        'title'         => 'Goblin Ambush',
        'description'   => 'A roadside ambush.',
        'min_level'     => 1,
        'max_level'     => 3,
        'party_size'    => 4,
        'is_active'     => $isActive,
    ];
}

test('guests can browse active encounters with related names and detail links', function () {
    $user = User::factory()->create(['name' => 'Dungeon Master']);
    $type = EncounterType::create(['name' => 'Combat']);
    $difficulty = EncounterDifficulty::create(['name' => 'Easy']);
    $encounter = Encounter::create(browseEncounterPayload($user, $type, $difficulty));

    $this->get(route('encounters.index'))
        ->assertOk()
        ->assertSee($encounter->title)
        ->assertSee($type->name)
        ->assertSee($difficulty->name)
        ->assertSee($user->name)
        ->assertSee(route('encounters.show', $encounter));
});

test('inactive encounters are excluded from the public overview', function () {
    $user = User::factory()->create();
    $type = EncounterType::create(['name' => 'Social']);
    $difficulty = EncounterDifficulty::create(['name' => 'Hard']);
    $activeEncounter = Encounter::create(array_merge(
        browseEncounterPayload($user, $type, $difficulty),
        ['title' => 'Active Social Encounter']
    ));
    $inactiveEncounter = Encounter::create(array_merge(
        browseEncounterPayload($user, $type, $difficulty, false),
        ['title' => 'Inactive Social Encounter']
    ));

    $this->get(route('encounters.index'))
        ->assertOk()
        ->assertSee($activeEncounter->title)
        ->assertDontSee($inactiveEncounter->title);
});
