<?php

use App\Models\Encounter;
use App\Models\EncounterDifficulty;
use App\Models\EncounterType;
use App\Models\User;

function editableEncounter(User $owner): Encounter {
    return Encounter::create([
        'user_id'       => $owner->id,
        'type_id'       => EncounterType::create(['name' => 'Combat'])->id,
        'difficulty_id' => EncounterDifficulty::create(['name' => 'Easy'])->id,
        'title'         => 'Original Encounter',
        'description'   => 'Original description.',
        'min_level'     => 1,
        'max_level'     => 3,
        'party_size'    => 4,
        'image_path'    => null,
        'is_active'     => true,
    ]);
}

function editablePayload(Encounter $encounter): array {
    return [
        'type_id'       => $encounter->type_id,
        'difficulty_id' => $encounter->difficulty_id,
        'title'         => 'Updated Encounter',
        'description'   => 'Updated description.',
        'min_level'     => 5,
        'max_level'     => 8,
        'party_size'    => 6,
        'image_path'    => 'https://example.com/encounter.jpg',
    ];
}

test('the owner can open an edit form with current encounter values', function () {
    $owner = User::factory()->create();
    $encounter = editableEncounter($owner);

    $this->actingAs($owner)
        ->get(route('encounters.edit', $encounter))
        ->assertOk()
        ->assertSee('value="Original Encounter"', false)
        ->assertSee('Original description.')
        ->assertSee('value="1"', false)
        ->assertSee('value="3"', false)
        ->assertSee('value="4"', false)
        ->assertSee('selected', false);
});

test('the owner can update an encounter and is redirected to its details', function () {
    $owner = User::factory()->create();
    $encounter = editableEncounter($owner);

    $response = $this->actingAs($owner)->put(
        route('encounters.update', $encounter),
        editablePayload($encounter)
    );

    $response
        ->assertSessionHas('success', 'Encounter updated successfully.')
        ->assertRedirect(route('encounters.show', $encounter));

    $this->assertDatabaseHas('encounters', [
        'id'          => $encounter->id,
        'title'       => 'Updated Encounter',
        'description' => 'Updated description.',
        'min_level'   => 5,
        'max_level'   => 8,
        'party_size'  => 6,
    ]);
});

test('invalid encounter updates are rejected and old input is retained', function () {
    $owner = User::factory()->create();
    $encounter = editableEncounter($owner);
    $payload = array_merge(editablePayload($encounter), [
        'title'      => '',
        'min_level'  => 10,
        'max_level'  => 5,
        'party_size' => 21,
    ]);

    $this->actingAs($owner)
        ->from(route('encounters.edit', $encounter))
        ->put(route('encounters.update', $encounter), $payload)
        ->assertRedirect(route('encounters.edit', $encounter))
        ->assertSessionHasErrors(['title', 'max_level', 'party_size'])
        ->assertSessionHasInput('title', '')
        ->assertSessionHasInput('min_level', 10)
        ->assertSessionHasInput('max_level', 5);

    expect($encounter->fresh()->title)->toBe('Original Encounter');
});

test('guests cannot edit or update encounters', function () {
    $encounter = editableEncounter(User::factory()->create());

    $this->get(route('encounters.edit', $encounter))->assertRedirect(route('login'));
    $this->put(route('encounters.update', $encounter), editablePayload($encounter))
        ->assertRedirect(route('login'));
});

test('non-owners cannot update an encounter', function () {
    $owner = User::factory()->create();
    $otherUser = User::factory()->create();
    $encounter = editableEncounter($owner);

    $this->actingAs($otherUser)
        ->patch(route('encounters.update', $encounter), editablePayload($encounter))
        ->assertForbidden();

    expect($encounter->fresh()->title)->toBe('Original Encounter');
});

test('user id and active status cannot be changed through the edit request', function () {
    $owner = User::factory()->create();
    $otherUser = User::factory()->create();
    $encounter = editableEncounter($owner);

    $this->actingAs($owner)->put(route('encounters.update', $encounter), array_merge(
        editablePayload($encounter),
        [
            'user_id'   => $otherUser->id,
            'is_active' => false,
        ]
    ))->assertRedirect(route('encounters.show', $encounter));

    expect($encounter->fresh()->user_id)->toBe($owner->id)
        ->and($encounter->fresh()->is_active)->toBe(1);
});

test('editing an unknown encounter returns not found', function () {
    $this->actingAs(User::factory()->create())
        ->get(route('encounters.edit', 999999))
        ->assertNotFound();
});
