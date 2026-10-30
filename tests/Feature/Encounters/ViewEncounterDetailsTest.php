<?php

use App\Models\Encounter;
use App\Models\EncounterDifficulty;
use App\Models\EncounterType;
use App\Models\User;

function detailEncounter(User $user): Encounter {
    return Encounter::create([
        'user_id'       => $user->id,
        'type_id'       => EncounterType::create(['name' => 'Exploration'])->id,
        'difficulty_id' => EncounterDifficulty::create(['name' => 'Medium'])->id,
        'title'         => 'Ruined Watchtower',
        'description'   => 'A watchtower occupied by a mysterious creature.',
        'min_level'     => 4,
        'max_level'     => 6,
        'party_size'    => 5,
    ]);
}

test('guests can view encounter details', function () {
    $encounter = detailEncounter(User::factory()->create(['name' => 'Alex Dungeonmaster']));

    $this->get(route('encounters.show', $encounter))
        ->assertOk()
        ->assertSee($encounter->title)
        ->assertSee($encounter->description)
        ->assertSee('Exploration')
        ->assertSee('Medium')
        ->assertSee('4 – 6')
        ->assertSee('5')
        ->assertSee('Alex Dungeonmaster');
});

test('guests can view active encounter details', function () {
    $encounter = detailEncounter(User::factory()->create());

    $this->get(route('encounters.show', $encounter))
        ->assertOk();
});

test('guests cannot view inactive encounter details', function () {
    $encounter = detailEncounter(User::factory()->create());
    $encounter->update(['is_active' => false]);

    $this->get(route('encounters.show', $encounter))
        ->assertNotFound();
});

test('normal users cannot view another users inactive encounter details', function () {
    $encounter = detailEncounter(User::factory()->create());
    $encounter->update(['is_active' => false]);

    $this->actingAs(User::factory()->create())
        ->get(route('encounters.show', $encounter))
        ->assertNotFound();
});

test('owners can view their own inactive encounter details', function () {
    $owner = User::factory()->create();
    $encounter = detailEncounter($owner);
    $encounter->update(['is_active' => false]);

    $this->actingAs($owner)
        ->get(route('encounters.show', $encounter))
        ->assertOk()
        ->assertSee($encounter->title);
});

test('administrators can view inactive encounter details without ownership actions', function () {
    $encounter = detailEncounter(User::factory()->create());
    $encounter->update(['is_active' => false]);

    $this->actingAs(User::factory()->admin()->create())
        ->get(route('encounters.show', $encounter))
        ->assertOk()
        ->assertSee($encounter->title)
        ->assertDontSee(route('encounters.edit', $encounter));
});

test('encounter details show an image when an image URL is available', function () {
    $encounter = detailEncounter(User::factory()->create());
    $encounter->update(['image_path' => 'https://example.com/watchtower.jpg']);

    $this->get(route('encounters.show', $encounter))
        ->assertSee('https://example.com/watchtower.jpg')
        ->assertSee('Illustration for '.$encounter->title);
});

test('an unknown encounter returns not found', function () {
    $this->get(route('encounters.show', 999999))->assertNotFound();
});

test('guests and non-owners do not see encounter management actions', function () {
    $owner = User::factory()->create();
    $encounter = detailEncounter($owner);
    $otherUser = User::factory()->create();

    $this->get(route('encounters.show', $encounter))
        ->assertOk()
        ->assertDontSee('Edit encounter');

    $this->actingAs($otherUser)
        ->get(route('encounters.show', $encounter))
        ->assertOk()
        ->assertDontSee('Edit encounter');
});

test('owners see only management actions allowed by the encounter policy', function () {
    $owner = User::factory()->create();
    $encounter = detailEncounter($owner);

    $this->actingAs($owner)
        ->get(route('encounters.show', $encounter))
        ->assertOk()
        ->assertSee(route('encounters.edit', $encounter));
});
