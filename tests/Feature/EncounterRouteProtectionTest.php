<?php

use App\Models\Encounter;
use App\Models\EncounterDifficulty;
use App\Models\EncounterType;
use App\Models\User;

function encounterPayload(): array {
    return [
        'type_id'       => EncounterType::firstOrCreate(['name' => 'Combat'])->id,
        'difficulty_id' => EncounterDifficulty::firstOrCreate(['name' => 'Easy'])->id,
        'title'         => 'Goblin Ambush',
        'description'   => 'A roadside ambush.',
        'min_level'     => 1,
        'max_level'     => 3,
        'party_size'    => 4,
        'image_path'    => null,
    ];
}

test('guests can view the encounter index', function () {
    $this->get(route('encounters.index'))->assertOk();
});

test('guests can view an encounter', function () {
    $encounter = Encounter::create(array_merge(encounterPayload(), [
        'user_id' => User::factory()->create()->id,
    ]));

    $this->get(route('encounters.show', $encounter))->assertOk();
});

test('guests are redirected from protected encounter routes', function () {
    $encounter = Encounter::create(array_merge(encounterPayload(), [
        'user_id' => User::factory()->create()->id,
    ]));

    $this->get(route('encounters.create'))->assertRedirect(route('login'));
    $this->post(route('encounters.store'), encounterPayload())->assertRedirect(route('login'));
    $this->get(route('encounters.edit', $encounter))->assertRedirect(route('login'));
    $this->put(route('encounters.update', $encounter), [])->assertRedirect(route('login'));
    $this->delete(route('encounters.destroy', $encounter))->assertRedirect(route('login'));
});

test('authenticated users can view the encounter form', function () {
    $this->actingAs(User::factory()->create())
        ->get(route('encounters.create'))
        ->assertOk();
});

test('authenticated users can create an encounter for themselves', function () {
    $user = User::factory()->create();
    $payload = encounterPayload();

    $response = $this->actingAs($user)->post(route('encounters.store'), $payload);

    $encounter = Encounter::query()->latest('id')->first();

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('encounters.show', $encounter));

    expect($encounter->user_id)->toBe($user->id);
    $this->assertDatabaseHas('encounters', [
        'id'      => $encounter->id,
        'user_id' => $user->id,
    ]);
});

test('a submitted user id is ignored when creating an encounter', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $payload = array_merge(encounterPayload(), ['user_id' => $otherUser->id]);

    $this->actingAs($user)->post(route('encounters.store'), $payload);

    $encounter = Encounter::query()->latest('id')->first();

    expect($encounter->user_id)->toBe($user->id);
});
