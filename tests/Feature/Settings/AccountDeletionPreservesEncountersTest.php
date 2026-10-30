<?php

use App\Livewire\Settings\DeleteUserForm;
use App\Models\Encounter;
use App\Models\EncounterDifficulty;
use App\Models\EncounterType;
use App\Models\Favorite;
use App\Models\User;
use Livewire\Livewire;

function accountDeletionEncounter(User $owner, string $title, bool $isActive = true): Encounter {
    return Encounter::create([
        'user_id'       => $owner->id,
        'type_id'       => EncounterType::firstOrCreate(['name' => 'Deletion Combat'])->id,
        'difficulty_id' => EncounterDifficulty::firstOrCreate(['name' => 'Deletion Easy'])->id,
        'title'         => $title,
        'description'   => 'An encounter used for account deletion tests.',
        'min_level'     => 1,
        'max_level'     => 3,
        'party_size'    => 4,
        'is_active'     => $isActive,
    ]);
}

function deleteAccount(User $user): void {
    auth()->login($user);

    Livewire::test(DeleteUserForm::class)
        ->set('password', 'password')
        ->call('deleteUser')
        ->assertHasNoErrors();
}

test('a user without encounters can delete their account', function () {
    $user = User::factory()->create();

    deleteAccount($user);

    expect($user->fresh())->toBeNull();
});

test('a user with encounters can delete their account without losing encounters', function () {
    $user = User::factory()->create();
    $encounter = accountDeletionEncounter($user, 'Preserved Encounter');

    deleteAccount($user);

    expect($user->fresh())->toBeNull()
        ->and($encounter->fresh()->user_id)->toBeNull();
    $this->assertDatabaseHas('encounters', ['id' => $encounter->id]);
});

test('favorites from other users remain while deleted users favorites are removed', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $encounter = accountDeletionEncounter($user, 'Favorite Preservation Encounter');
    $otherFavorite = $otherUser->favorites()->create(['encounter_id' => $encounter->id]);
    $deletedUserFavorite = $user->favorites()->create(['encounter_id' => $encounter->id]);

    deleteAccount($user);

    $this->assertDatabaseHas('favorites', ['id' => $otherFavorite->id]);
    $this->assertDatabaseMissing('favorites', ['id' => $deletedUserFavorite->id]);
    expect(Favorite::where('encounter_id', $encounter->id)->count())->toBe(1);
});

test('preserved active encounters remain public and show a deleted user', function () {
    $user = User::factory()->create();
    $encounter = accountDeletionEncounter($user, 'Public Preserved Encounter');

    deleteAccount($user);

    $this->get(route('encounters.index'))
        ->assertOk()
        ->assertSee($encounter->title)
        ->assertSee('Deleted user');

    $this->get(route('encounters.show', $encounter))
        ->assertOk()
        ->assertSee('Deleted user');

    $this->get(route('home'))
        ->assertOk();
});

test('preserved inactive encounters are unavailable to guests and normal users', function () {
    $user = User::factory()->create();
    $encounter = accountDeletionEncounter($user, 'Private Preserved Encounter', false);

    deleteAccount($user);

    auth()->logout();

    $this->get(route('encounters.show', $encounter))->assertNotFound();

    $this->actingAs(User::factory()->create())
        ->get(route('encounters.show', $encounter))
        ->assertNotFound();
});

test('administrators can view and toggle preserved inactive encounters', function () {
    $user = User::factory()->create();
    $encounter = accountDeletionEncounter($user, 'Admin Preserved Encounter', false);

    deleteAccount($user);

    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->get(route('encounters.show', $encounter))
        ->assertOk()
        ->assertSee('Deleted user');

    $this->actingAs($admin)
        ->post(route('admin.encounters.toggle-status', $encounter))
        ->assertRedirect(route('admin.index'));

    expect((bool) $encounter->fresh()->is_active)->toBeTrue();
});

test('an ownerless encounter grants no edit or delete authorization', function () {
    $user = User::factory()->create();
    $encounter = accountDeletionEncounter($user, 'Ownerless Encounter');

    deleteAccount($user);

    $otherUser = User::factory()->create();

    $this->actingAs($otherUser)
        ->get(route('encounters.edit', $encounter))
        ->assertForbidden();

    $this->actingAs($otherUser)
        ->delete(route('encounters.destroy', $encounter))
        ->assertForbidden();

    $this->assertDatabaseHas('encounters', ['id' => $encounter->id]);
});

test('example', function () {
    $response = $this->get('/');

    $response->assertStatus(200);
});
