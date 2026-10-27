<?php

use App\Models\Encounter;
use App\Models\EncounterDifficulty;
use App\Models\EncounterType;
use App\Models\User;

function validationPayload(): array {
    return [
        'type_id'       => EncounterType::firstOrCreate(['name' => 'Combat'])->id,
        'difficulty_id' => EncounterDifficulty::firstOrCreate(['name' => 'Easy'])->id,
        'title'         => 'Validation test encounter',
        'description'   => 'Validation test description.',
        'min_level'     => 1,
        'max_level'     => 3,
        'party_size'    => 4,
        'image_path'    => null,
    ];
}

function validationEncounter(User $owner): Encounter {
    return Encounter::create(array_merge(validationPayload(), [
        'user_id' => $owner->id,
    ]));
}

test('create validation rejects invalid ids, levels, ordering, and party size', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->from(route('encounters.create'))->post(
        route('encounters.store'),
        array_merge(validationPayload(), [
            'type_id'       => 999999,
            'difficulty_id' => 999999,
            'min_level'     => 0,
            'max_level'     => 21,
            'party_size'    => 21,
        ])
    );

    $response
        ->assertRedirect(route('encounters.create'))
        ->assertSessionHasErrors([
            'type_id',
            'difficulty_id',
            'min_level',
            'max_level',
            'party_size',
        ]);

    $this->assertDatabaseCount('encounters', 0);
});

test('create validation rejects a maximum level below the minimum level', function () {
    $user = User::factory()->create();
    $payload = array_merge(validationPayload(), [
        'min_level' => 10,
        'max_level' => 5,
    ]);

    $this->actingAs($user)
        ->from(route('encounters.create'))
        ->post(route('encounters.store'), $payload)
        ->assertRedirect(route('encounters.create'))
        ->assertSessionHasErrors(['max_level']);

    $this->assertDatabaseCount('encounters', 0);
});

test('create validation retains submitted values after an error', function () {
    $user = User::factory()->create();
    $payload = array_merge(validationPayload(), [
        'title'      => 'Keep this title',
        'min_level'  => 10,
        'max_level'  => 5,
        'party_size' => 7,
    ]);

    $this->actingAs($user)
        ->from(route('encounters.create'))
        ->post(route('encounters.store'), $payload)
        ->assertRedirect(route('encounters.create'))
        ->assertSessionHasErrors()
        ->assertSessionHasInput('title', 'Keep this title')
        ->assertSessionHasInput('min_level', 10)
        ->assertSessionHasInput('max_level', 5)
        ->assertSessionHasInput('party_size', 7);
});

test('edit validation rejects invalid data without changing the encounter', function () {
    $owner = User::factory()->create();
    $encounter = validationEncounter($owner);

    $response = $this->actingAs($owner)->from(route('encounters.edit', $encounter))->put(
        route('encounters.update', $encounter),
        array_merge(validationPayload(), [
            'type_id'       => 999999,
            'difficulty_id' => 999999,
            'min_level'     => 0,
            'max_level'     => 21,
            'party_size'    => 0,
        ])
    );

    $response
        ->assertRedirect(route('encounters.edit', $encounter))
        ->assertSessionHasErrors([
            'type_id',
            'difficulty_id',
            'min_level',
            'max_level',
            'party_size',
        ]);

    expect($encounter->fresh()->title)->toBe('Validation test encounter');
});

test('edit validation retains submitted values after an error', function () {
    $owner = User::factory()->create();
    $encounter = validationEncounter($owner);
    $payload = array_merge(validationPayload(), [
        'title'      => 'Keep this edited title',
        'min_level'  => 12,
        'max_level'  => 6,
        'party_size' => 8,
    ]);

    $this->actingAs($owner)
        ->from(route('encounters.edit', $encounter))
        ->put(route('encounters.update', $encounter), $payload)
        ->assertRedirect(route('encounters.edit', $encounter))
        ->assertSessionHasErrors(['max_level'])
        ->assertSessionHasInput('title', 'Keep this edited title')
        ->assertSessionHasInput('min_level', 12)
        ->assertSessionHasInput('max_level', 6)
        ->assertSessionHasInput('party_size', 8);
});
