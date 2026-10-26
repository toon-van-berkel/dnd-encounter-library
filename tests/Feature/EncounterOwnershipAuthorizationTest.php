<?php

use App\Models\Encounter;
use App\Models\EncounterDifficulty;
use App\Models\EncounterType;
use App\Models\User;
use App\Policies\EncounterPolicy;

function ownedEncounter(User $owner): Encounter {
    return Encounter::create([
        'user_id'       => $owner->id,
        'type_id'       => EncounterType::firstOrCreate(['name' => 'Combat'])->id,
        'difficulty_id' => EncounterDifficulty::firstOrCreate(['name' => 'Easy'])->id,
        'title'         => 'Goblin Ambush',
        'description'   => 'A roadside ambush.',
        'min_level'     => 1,
        'max_level'     => 3,
        'party_size'    => 4,
    ]);
}

test('an owner may open the edit page for their encounter', function () {
    $owner = User::factory()->create();
    $encounter = ownedEncounter($owner);

    $this->actingAs($owner)
        ->get(route('encounters.edit', $encounter))
        ->assertOk();
});

test('a non-owner is forbidden from editing an encounter', function () {
    $owner = User::factory()->create();
    $otherUser = User::factory()->create();
    $encounter = ownedEncounter($owner);

    $this->actingAs($otherUser)
        ->get(route('encounters.edit', $encounter))
        ->assertForbidden();
});

test('a non-owner cannot update an encounter through a direct request', function () {
    $owner = User::factory()->create();
    $otherUser = User::factory()->create();
    $encounter = ownedEncounter($owner);

    $this->actingAs($otherUser)
        ->put(route('encounters.update', $encounter), ['title' => 'Changed title'])
        ->assertForbidden();

    $this->actingAs($otherUser)
        ->patch(route('encounters.update', $encounter), ['title' => 'Changed title'])
        ->assertForbidden();

    expect($encounter->fresh()->title)->toBe('Goblin Ambush');
});

test('a non-owner cannot delete an encounter through a direct request', function () {
    $owner = User::factory()->create();
    $otherUser = User::factory()->create();
    $encounter = ownedEncounter($owner);

    $this->actingAs($otherUser)
        ->delete(route('encounters.destroy', $encounter))
        ->assertForbidden();

    $this->assertModelExists($encounter);
});

test('encounter authorization returns not found for an unknown encounter', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('encounters.edit', 999999))
        ->assertNotFound();
});

test('public encounter routes remain accessible', function () {
    $owner = User::factory()->create();
    $encounter = ownedEncounter($owner);

    $this->get(route('encounters.index'))->assertOk();
    $this->get(route('encounters.show', $encounter))->assertOk();
});

test('the encounter policy permits only the owner to update and delete', function () {
    $owner = User::factory()->create();
    $otherUser = User::factory()->create();
    $encounter = ownedEncounter($owner);
    $policy = new EncounterPolicy;

    expect($policy->update($owner, $encounter))->toBeTrue()
        ->and($policy->delete($owner, $encounter))->toBeTrue()
        ->and($policy->update($otherUser, $encounter))->toBeFalse()
        ->and($policy->delete($otherUser, $encounter))->toBeFalse();
});
