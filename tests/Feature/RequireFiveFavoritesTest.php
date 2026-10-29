<?php

use App\Models\Encounter;
use App\Models\EncounterDifficulty;
use App\Models\EncounterType;
use App\Models\User;

function giveFavorites(User $user, int $count): void {
    $type = EncounterType::firstOrCreate(['name' => 'US15 Combat']);
    $difficulty = EncounterDifficulty::firstOrCreate(['name' => 'US15 Easy']);

    for ($index = 1; $index <= $count; $index++) {
        $encounter = Encounter::create([
            'user_id'       => User::factory()->create()->id,
            'type_id'       => $type->id,
            'difficulty_id' => $difficulty->id,
            'title'         => "US15 Favorite {$index}",
            'description'   => 'A favorite prerequisite encounter.',
            'min_level'     => 1,
            'max_level'     => 3,
            'party_size'    => 4,
            'is_active'     => true,
        ]);

        $user->favorites()->create(['encounter_id' => $encounter->id]);
    }
}

function validCreationPayload(): array {
    return [
        'type_id'       => EncounterType::firstOrCreate(['name' => 'US15 Creation Combat'])->id,
        'difficulty_id' => EncounterDifficulty::firstOrCreate(['name' => 'US15 Creation Easy'])->id,
        'title'         => 'New US15 Encounter',
        'description'   => 'A newly created encounter.',
        'min_level'     => 1,
        'max_level'     => 3,
        'party_size'    => 4,
    ];
}

test('users with fewer than five favorites cannot open the create form', function () {
    $user = User::factory()->create();
    giveFavorites($user, 4);

    $this->actingAs($user)
        ->get(route('encounters.create'))
        ->assertOk()
        ->assertSee('You currently have 4 favorites')
        ->assertSee('5 favorites')
        ->assertSee(route('encounters.index'))
        ->assertDontSee('name="title"', false);
});

test('users with five favorites can open the create form', function () {
    $user = User::factory()->create();
    giveFavorites($user, 5);

    $this->actingAs($user)
        ->get(route('encounters.create'))
        ->assertOk()
        ->assertSee('name="title"', false);
});

test('users with six favorites can open the create form', function () {
    $user = User::factory()->create();
    giveFavorites($user, 6);

    $this->actingAs($user)
        ->get(route('encounters.create'))
        ->assertOk()
        ->assertSee('name="title"', false);
});

test('users with fewer than five favorites cannot create through a direct post', function () {
    $user = User::factory()->create();
    giveFavorites($user, 4);

    $this->actingAs($user)
        ->post(route('encounters.store'), validCreationPayload())
        ->assertRedirect(route('encounters.create'))
        ->assertSessionHasErrors('favorites');

    $this->assertDatabaseMissing('encounters', ['title' => 'New US15 Encounter']);
});

test('a user with five favorites can create an encounter', function () {
    $user = User::factory()->create();
    giveFavorites($user, 5);

    $response = $this->actingAs($user)
        ->post(route('encounters.store'), validCreationPayload());

    $encounter = Encounter::where('title', 'New US15 Encounter')->first();

    $response->assertRedirect(route('encounters.show', $encounter));
    expect($encounter->user_id)->toBe($user->id);
});

test('favorites of another user do not count toward the requirement', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    giveFavorites($otherUser, 5);

    $this->actingAs($user)
        ->get(route('encounters.create'))
        ->assertSee('You currently have 0 favorites')
        ->assertDontSee('name="title"', false);
});

test('removing a favorite immediately lowers the count used by the rule', function () {
    $user = User::factory()->create();
    giveFavorites($user, 5);
    $favorite = $user->favorites()->first();
    $encounter = $favorite->encounter;

    $this->actingAs($user)
        ->delete(route('encounters.unfavorite', $encounter));

    $this->get(route('encounters.create'))
        ->assertSee('You currently have 4 favorites')
        ->assertDontSee('name="title"', false);
});

test('users can add and remove favorites regardless of the create threshold', function () {
    $user = User::factory()->create();
    $encounter = Encounter::create(array_merge(validCreationPayload(), [
        'user_id' => User::factory()->create()->id,
        'title'   => 'Favorite Before Threshold',
    ]));

    $this->actingAs($user)
        ->post(route('encounters.favorite', $encounter))
        ->assertRedirect();

    $this->actingAs($user)
        ->delete(route('encounters.unfavorite', $encounter))
        ->assertRedirect();
});

test('a spoofed user id cannot bypass the requirement', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    giveFavorites($otherUser, 5);

    $this->actingAs($user)
        ->post(route('encounters.store'), array_merge(validCreationPayload(), [
            'user_id' => $otherUser->id,
        ]))
        ->assertRedirect(route('encounters.create'))
        ->assertSessionHasErrors('favorites');
});

test('administrators do not bypass the five favorite requirement', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->get(route('encounters.create'))
        ->assertSee('You currently have 0 favorites')
        ->assertDontSee('name="title"', false);
});

test('existing encounters remain editable below the favorite threshold', function () {
    $owner = User::factory()->create();
    $encounter = Encounter::create(array_merge(validCreationPayload(), [
        'user_id' => $owner->id,
        'title'   => 'Existing Encounter',
    ]));

    $this->actingAs($owner)
        ->get(route('encounters.edit', $encounter))
        ->assertOk();
});
