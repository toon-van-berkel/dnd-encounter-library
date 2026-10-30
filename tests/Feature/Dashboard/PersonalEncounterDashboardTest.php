<?php

use App\Models\Encounter;
use App\Models\EncounterDifficulty;
use App\Models\EncounterType;
use App\Models\User;
use Illuminate\Support\Facades\Route;

function dashboardEncounter(User $owner, bool $isActive = true, string $title = 'Dashboard Encounter'): Encounter {
    return Encounter::create([
        'user_id'       => $owner->id,
        'type_id'       => EncounterType::firstOrCreate(['name' => 'Combat'])->id,
        'difficulty_id' => EncounterDifficulty::firstOrCreate(['name' => 'Easy'])->id,
        'title'         => $title,
        'description'   => 'An encounter for the personal dashboard.',
        'min_level'     => 1,
        'max_level'     => 3,
        'party_size'    => 4,
        'is_active'     => $isActive,
    ]);
}

test('a guest cannot open the personal dashboard', function () {
    $this->get(route('dashboard'))
        ->assertRedirect(route('login'));
});

test('an authenticated user can open the personal dashboard', function () {
    $this->actingAs(User::factory()->create())
        ->get(route('dashboard'))
        ->assertOk()
        ->assertSee('My encounters');
});

test('a user sees only their own active and inactive encounters', function () {
    $owner = User::factory()->create();
    $otherUser = User::factory()->create();
    $activeEncounter = dashboardEncounter($owner, true, 'My Active Encounter');
    $inactiveEncounter = dashboardEncounter($owner, false, 'My Inactive Encounter');
    $otherEncounter = dashboardEncounter($otherUser, true, 'Other User Encounter');

    $this->actingAs($owner)
        ->get(route('dashboard'))
        ->assertSee($activeEncounter->title)
        ->assertSee($inactiveEncounter->title)
        ->assertDontSee($otherEncounter->title)
        ->assertSee('Active')
        ->assertSee('Inactive')
        ->assertSee('Combat')
        ->assertSee('Easy');
});

test('dashboard encounter links point to the detail page', function () {
    $owner = User::factory()->create();
    $encounter = dashboardEncounter($owner);

    $this->actingAs($owner)
        ->get(route('dashboard'))
        ->assertSee(route('encounters.show', $encounter));
});

test('a user can navigate from the dashboard to create an encounter', function () {
    $this->actingAs(User::factory()->create())
        ->get(route('dashboard'))
        ->assertSee(route('encounters.create'));
});

test('edit and delete actions follow the encounter policy', function () {
    $owner = User::factory()->create();
    $otherUser = User::factory()->create();
    $ownedEncounter = dashboardEncounter($owner, true, 'Owned Encounter');
    $otherEncounter = dashboardEncounter($otherUser, true, 'Other Encounter');

    $this->actingAs($owner)
        ->get(route('dashboard'))
        ->assertSee(route('encounters.edit', $ownedEncounter))
        ->assertSee(route('encounters.destroy', $ownedEncounter));

    $this->actingAs($otherUser)
        ->get(route('dashboard'))
        ->assertSee(route('encounters.edit', $otherEncounter))
        ->assertDontSee(route('encounters.edit', $ownedEncounter));
});

test('a user without encounters sees the empty state', function () {
    $this->actingAs(User::factory()->create())
        ->get(route('dashboard'))
        ->assertSee('You have not created any encounters yet.');
});

test('an administrator sees only their own encounters on the personal dashboard', function () {
    $admin = User::factory()->admin()->create();
    $otherUser = User::factory()->create();
    $adminEncounter = dashboardEncounter($admin, true, 'Admin Personal Encounter');
    $otherEncounter = dashboardEncounter($otherUser, true, 'Other Personal Encounter');

    $this->actingAs($admin)
        ->get(route('dashboard'))
        ->assertSee($adminEncounter->title)
        ->assertDontSee($otherEncounter->title);
});

test('dashboard delete action uses the protected destroy endpoint', function () {
    $owner = User::factory()->create();
    $encounter = dashboardEncounter($owner);

    $this->actingAs($owner)
        ->get(route('dashboard'))
        ->assertSee('method="POST"', false)
        ->assertSee('name="_method" value="DELETE"', false)
        ->assertSee(route('encounters.destroy', $encounter));
});

test('existing admin and status routes remain available', function () {
    $this->assertTrue(Route::has('admin.index'));
    $this->assertTrue(Route::has('admin.encounters.toggle-status'));
});
