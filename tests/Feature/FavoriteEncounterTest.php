<?php

use App\Models\Encounter;
use App\Models\EncounterDifficulty;
use App\Models\EncounterType;
use App\Models\Favorite;
use App\Models\User;

function favoritableEncounter(User $owner): Encounter {
    return Encounter::create([
        'user_id'       => $owner->id,
        'type_id'       => EncounterType::firstOrCreate(['name' => 'Combat'])->id,
        'difficulty_id' => EncounterDifficulty::firstOrCreate(['name' => 'Easy'])->id,
        'title'         => 'Favorite Encounter',
        'description'   => 'An encounter that can be favorited.',
        'min_level'     => 1,
        'max_level'     => 3,
        'party_size'    => 4,
        'is_active'     => true,
    ]);
}

test('an authenticated user can favorite an encounter', function () {
    $user = User::factory()->create();
    $encounter = favoritableEncounter(User::factory()->create());

    $this->actingAs($user)
        ->post(route('encounters.favorite', $encounter))
        ->assertRedirect(route('encounters.show', $encounter));

    $this->assertDatabaseHas('favorites', [
        'user_id'      => $user->id,
        'encounter_id' => $encounter->id,
    ]);
});

test('a guest cannot add a favorite', function () {
    $encounter = favoritableEncounter(User::factory()->create());

    $this->post(route('encounters.favorite', $encounter))
        ->assertRedirect(route('login'));

    $this->assertDatabaseCount('favorites', 0);
});

test('the authenticated user is used instead of a submitted user id', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $encounter = favoritableEncounter(User::factory()->create());

    $this->actingAs($user)
        ->post(route('encounters.favorite', $encounter), ['user_id' => $otherUser->id]);

    $this->assertDatabaseHas('favorites', [
        'user_id'      => $user->id,
        'encounter_id' => $encounter->id,
    ]);
    $this->assertDatabaseMissing('favorites', [
        'user_id'      => $otherUser->id,
        'encounter_id' => $encounter->id,
    ]);
});

test('favoriting an encounter twice creates only one favorite', function () {
    $user = User::factory()->create();
    $encounter = favoritableEncounter(User::factory()->create());

    $this->actingAs($user)->post(route('encounters.favorite', $encounter));
    $this->actingAs($user)->post(route('encounters.favorite', $encounter));

    expect(Favorite::where('user_id', $user->id)
        ->where('encounter_id', $encounter->id)
        ->count())->toBe(1);
});

test('the encounter page shows the current favorite status', function () {
    $user = User::factory()->create();
    $encounter = favoritableEncounter(User::factory()->create());

    $this->actingAs($user)
        ->get(route('encounters.show', $encounter))
        ->assertSee('Add to favorites')
        ->assertDontSee('Remove from favorites');

    $user->favorites()->create(['encounter_id' => $encounter->id]);

    $this->actingAs($user)
        ->get(route('encounters.show', $encounter))
        ->assertSee('Remove from favorites')
        ->assertDontSee('Add to favorites');

    auth()->logout();

    $this->get(route('encounters.show', $encounter))
        ->assertDontSee('Add to favorites')
        ->assertDontSee('Remove from favorites');
});

test('a user can remove their favorite', function () {
    $user = User::factory()->create();
    $encounter = favoritableEncounter(User::factory()->create());
    $user->favorites()->create(['encounter_id' => $encounter->id]);

    $this->actingAs($user)
        ->delete(route('encounters.unfavorite', $encounter))
        ->assertRedirect(route('encounters.show', $encounter));

    $this->assertDatabaseMissing('favorites', [
        'user_id'      => $user->id,
        'encounter_id' => $encounter->id,
    ]);
    $this->assertModelExists($encounter);
});

test('a user cannot remove another users favorite', function () {
    $owner = User::factory()->create();
    $otherUser = User::factory()->create();
    $encounter = favoritableEncounter($owner);
    $owner->favorites()->create(['encounter_id' => $encounter->id]);

    $this->actingAs($otherUser)
        ->delete(route('encounters.unfavorite', $encounter))
        ->assertRedirect(route('encounters.show', $encounter));

    $this->assertDatabaseHas('favorites', [
        'user_id'      => $owner->id,
        'encounter_id' => $encounter->id,
    ]);
});

test('a user can favorite an encounter owned by another user', function () {
    $user = User::factory()->create();
    $encounter = favoritableEncounter(User::factory()->create());

    $this->actingAs($user)
        ->post(route('encounters.favorite', $encounter))
        ->assertRedirect();

    $this->assertDatabaseHas('favorites', [
        'user_id'      => $user->id,
        'encounter_id' => $encounter->id,
    ]);
});

test('unknown encounters do not cause a server error', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->post(route('encounters.favorite', 999999))
        ->assertNotFound();

    $this->actingAs($user)
        ->delete(route('encounters.unfavorite', 999999))
        ->assertNotFound();
});

test('get requests cannot change favorites', function () {
    $user = User::factory()->create();
    $encounter = favoritableEncounter(User::factory()->create());

    $this->actingAs($user)
        ->get(route('encounters.favorite', $encounter))
        ->assertMethodNotAllowed();

    $this->assertDatabaseCount('favorites', 0);
});

test('favorite forms include csrf protection', function () {
    $user = User::factory()->create();
    $encounter = favoritableEncounter(User::factory()->create());

    $this->actingAs($user)
        ->get(route('encounters.show', $encounter))
        ->assertSee('name="_token"', false)
        ->assertSee(route('encounters.favorite', $encounter));
});
