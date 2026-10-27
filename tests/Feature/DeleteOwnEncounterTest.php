<?php

use App\Models\Encounter;
use App\Models\EncounterDifficulty;
use App\Models\EncounterType;
use App\Models\User;
use Illuminate\Support\Facades\DB;

function deletableEncounter(User $owner): Encounter {
    return Encounter::create([
        'user_id'       => $owner->id,
        'type_id'       => EncounterType::firstOrCreate(['name' => 'Combat'])->id,
        'difficulty_id' => EncounterDifficulty::firstOrCreate(['name' => 'Easy'])->id,
        'title'         => 'Encounter to delete',
        'description'   => 'This encounter will be deleted.',
        'min_level'     => 1,
        'max_level'     => 3,
        'party_size'    => 4,
    ]);
}

test('the owner can delete an encounter and is redirected to the index', function () {
    $owner = User::factory()->create();
    $encounter = deletableEncounter($owner);

    $this->actingAs($owner)
        ->delete(route('encounters.destroy', $encounter))
        ->assertSessionHas('success', 'Encounter deleted successfully.')
        ->assertRedirect(route('encounters.index'));

    $this->assertDatabaseMissing('encounters', ['id' => $encounter->id]);
});

test('a non-owner cannot delete an encounter', function () {
    $owner = User::factory()->create();
    $otherUser = User::factory()->create();
    $encounter = deletableEncounter($owner);

    $this->actingAs($otherUser)
        ->delete(route('encounters.destroy', $encounter))
        ->assertForbidden();

    $this->assertDatabaseHas('encounters', ['id' => $encounter->id]);
});

test('guests cannot delete an encounter', function () {
    $encounter = deletableEncounter(User::factory()->create());

    $this->delete(route('encounters.destroy', $encounter))
        ->assertRedirect(route('login'));

    $this->assertDatabaseHas('encounters', ['id' => $encounter->id]);
});

test('a get request does not delete an encounter', function () {
    $encounter = deletableEncounter(User::factory()->create());

    $this->actingAs($encounter->user)
        ->get(route('encounters.destroy', $encounter))
        ->assertOk();

    $this->assertDatabaseHas('encounters', ['id' => $encounter->id]);
});

test('the delete action is visible only to the owner', function () {
    $owner = User::factory()->create();
    $otherUser = User::factory()->create();
    $encounter = deletableEncounter($owner);

    $this->get(route('encounters.show', $encounter))
        ->assertDontSee('Delete encounter');

    $this->actingAs($otherUser)
        ->get(route('encounters.show', $encounter))
        ->assertDontSee('Delete encounter');

    $this->actingAs($owner)
        ->get(route('encounters.show', $encounter))
        ->assertSee('method="POST"', false)
        ->assertSee('name="_method" value="DELETE"', false)
        ->assertSee(route('encounters.destroy', $encounter));
});

test('deleting an encounter also deletes its favorites', function () {
    $owner = User::factory()->create();
    $otherUser = User::factory()->create();
    $encounter = deletableEncounter($owner);
    $otherEncounter = deletableEncounter($owner);

    DB::table('favorites')->insert([
        ['user_id' => $owner->id, 'encounter_id' => $encounter->id],
        ['user_id' => $otherUser->id, 'encounter_id' => $encounter->id],
        ['user_id' => $owner->id, 'encounter_id' => $otherEncounter->id],
    ]);

    $this->actingAs($owner)->delete(route('encounters.destroy', $encounter));

    $this->assertDatabaseMissing('favorites', ['encounter_id' => $encounter->id]);
    $this->assertDatabaseHas('favorites', ['encounter_id' => $otherEncounter->id]);
});

test('deleting an unknown encounter returns not found', function () {
    $this->actingAs(User::factory()->create())
        ->delete(route('encounters.destroy', 999999))
        ->assertNotFound();
});
