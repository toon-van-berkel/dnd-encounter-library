<?php

use App\Models\Encounter;
use App\Models\EncounterDifficulty;
use App\Models\EncounterType;
use App\Models\Favorite;
use App\Models\User;

function favoritePageEncounter(User $owner, string $title, bool $isActive = true): Encounter {
    return Encounter::create([
        'user_id'       => $owner->id,
        'type_id'       => EncounterType::firstOrCreate(['name' => 'Favorite Combat'])->id,
        'difficulty_id' => EncounterDifficulty::firstOrCreate(['name' => 'Favorite Easy'])->id,
        'title'         => $title,
        'description'   => 'An encounter on the favorites page.',
        'min_level'     => 1,
        'max_level'     => 3,
        'party_size'    => 4,
        'is_active'     => $isActive,
    ]);
}

test('a guest cannot open the favorites page', function () {
    $this->get(route('favorites.index'))
        ->assertRedirect(route('login'));
});

test('an authenticated user can open the favorites page', function () {
    $this->actingAs(User::factory()->create())
        ->get(route('favorites.index'))
        ->assertOk()
        ->assertSee('Favorite encounters');
});

test('a user sees only their own favorites', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $ownEncounter = favoritePageEncounter(User::factory()->create(), 'My Favorite');
    $otherEncounter = favoritePageEncounter(User::factory()->create(), 'Other Favorite');
    $user->favorites()->create(['encounter_id' => $ownEncounter->id]);
    $otherUser->favorites()->create(['encounter_id' => $otherEncounter->id]);

    $this->actingAs($user)
        ->get(route('favorites.index'))
        ->assertSee($ownEncounter->title)
        ->assertDontSee($otherEncounter->title);
});

test('favorites show encounter details and detail links', function () {
    $user = User::factory()->create();
    $encounter = favoritePageEncounter(User::factory()->create(), 'Detailed Favorite');
    $user->favorites()->create(['encounter_id' => $encounter->id]);

    $this->actingAs($user)
        ->get(route('favorites.index'))
        ->assertSee($encounter->title)
        ->assertSee('Favorite Combat')
        ->assertSee('Favorite Easy')
        ->assertSee(route('encounters.show', $encounter));
});

test('a user without favorites sees the empty state', function () {
    $this->actingAs(User::factory()->create())
        ->get(route('favorites.index'))
        ->assertSee("You haven't added any favorite encounters yet.", false)
        ->assertSee(route('encounters.index'));
});

test('a favorite can be removed from the favorites page', function () {
    $user = User::factory()->create();
    $encounter = favoritePageEncounter(User::factory()->create(), 'Removable Favorite');
    $favorite = $user->favorites()->create(['encounter_id' => $encounter->id]);

    $this->actingAs($user)
        ->delete(route('encounters.unfavorite', $encounter), ['redirect_to' => 'favorites'])
        ->assertRedirect(route('favorites.index'));

    $this->assertDatabaseMissing('favorites', ['id' => $favorite->id]);
    $this->assertModelExists($encounter);
});

test('other users favorites remain unchanged', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $encounter = favoritePageEncounter(User::factory()->create(), 'Shared Encounter');
    $otherFavorite = $otherUser->favorites()->create(['encounter_id' => $encounter->id]);

    $this->actingAs($user)
        ->delete(route('encounters.unfavorite', $encounter), ['redirect_to' => 'favorites']);

    $this->assertDatabaseHas('favorites', ['id' => $otherFavorite->id]);
});

test('administrators see only their own favorites', function () {
    $admin = User::factory()->admin()->create();
    $otherUser = User::factory()->create();
    $adminEncounter = favoritePageEncounter(User::factory()->create(), 'Admin Favorite');
    $otherEncounter = favoritePageEncounter(User::factory()->create(), 'Other User Favorite');
    $admin->favorites()->create(['encounter_id' => $adminEncounter->id]);
    $otherUser->favorites()->create(['encounter_id' => $otherEncounter->id]);

    $this->actingAs($admin)
        ->get(route('favorites.index'))
        ->assertSee($adminEncounter->title)
        ->assertDontSee($otherEncounter->title);
});

test('inactive favorite encounters remain visible on the favorites page', function () {
    $user = User::factory()->create();
    $encounter = favoritePageEncounter(User::factory()->create(), 'Inactive Favorite', false);
    $user->favorites()->create(['encounter_id' => $encounter->id]);

    $this->actingAs($user)
        ->get(route('favorites.index'))
        ->assertSee('Unavailable encounter')
        ->assertDontSee($encounter->title);
});

test('an inaccessible inactive favorite hides encounter details and remains removable', function () {
    $user = User::factory()->create();
    $encounter = favoritePageEncounter(User::factory()->create(), 'Protected Inactive Favorite', false);
    $favorite = $user->favorites()->create(['encounter_id' => $encounter->id]);

    $this->actingAs($user)
        ->get(route('favorites.index'))
        ->assertSee('Unavailable encounter')
        ->assertDontSee($encounter->title)
        ->assertDontSee('encounter-card__title-link');

    $this->actingAs($user)
        ->delete(route('encounters.unfavorite', $encounter), ['redirect_to' => 'favorites'])
        ->assertRedirect(route('favorites.index'));

    $this->assertDatabaseMissing('favorites', ['id' => $favorite->id]);
    $this->assertModelExists($encounter);
});

test('owners retain access to inactive favorites', function () {
    $owner = User::factory()->create();
    $encounter = favoritePageEncounter($owner, 'Owned Inactive Favorite', false);
    $owner->favorites()->create(['encounter_id' => $encounter->id]);

    $this->actingAs($owner)
        ->get(route('favorites.index'))
        ->assertSee($encounter->title)
        ->assertSee(route('encounters.show', $encounter));
});

test('administrators retain access to inactive favorites', function () {
    $admin = User::factory()->admin()->create();
    $encounter = favoritePageEncounter(User::factory()->create(), 'Admin Inactive Favorite', false);
    $admin->favorites()->create(['encounter_id' => $encounter->id]);

    $this->actingAs($admin)
        ->get(route('favorites.index'))
        ->assertSee($encounter->title)
        ->assertSee(route('encounters.show', $encounter));
});

test('favorites navigation link is visible only to authenticated users', function () {
    $this->get(route('home'))
        ->assertDontSee(route('favorites.index'));

    $this->actingAs(User::factory()->create())
        ->get(route('home'))
        ->assertSee(route('favorites.index'))
        ->assertSee('My favorites');
});

test('favorite removal from the encounter page keeps its existing redirect', function () {
    $user = User::factory()->create();
    $encounter = favoritePageEncounter(User::factory()->create(), 'Detail Favorite');
    $user->favorites()->create(['encounter_id' => $encounter->id]);

    $this->actingAs($user)
        ->delete(route('encounters.unfavorite', $encounter))
        ->assertRedirect(route('encounters.show', $encounter));

    expect(Favorite::where('user_id', $user->id)->where('encounter_id', $encounter->id)->exists())
        ->toBeFalse();
});
