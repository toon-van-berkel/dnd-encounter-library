<?php

use App\Models\Encounter;
use App\Models\EncounterDifficulty;
use App\Models\EncounterType;
use App\Models\User;

function adminEncounter(User $owner, bool $isActive, string $title): Encounter {
    return Encounter::create([
        'user_id'       => $owner->id,
        'type_id'       => EncounterType::firstOrCreate(['name' => 'Combat'])->id,
        'difficulty_id' => EncounterDifficulty::firstOrCreate(['name' => 'Easy'])->id,
        'title'         => $title,
        'description'   => 'An encounter for the admin overview.',
        'min_level'     => 1,
        'max_level'     => 3,
        'party_size'    => 4,
        'is_active'     => $isActive,
    ]);
}

test('an administrator can open the admin area', function () {
    $this->actingAs(User::factory()->admin()->create())
        ->get(route('admin.index'))
        ->assertOk()
        ->assertSee('Admin area');
});

test('a normal user is forbidden from the admin area', function () {
    $this->actingAs(User::factory()->create())
        ->get(route('admin.index'))
        ->assertForbidden();
});

test('a guest is redirected to login from the admin area', function () {
    $this->get(route('admin.index'))
        ->assertRedirect(route('login'));
});

test('the admin area shows encounters from different users', function () {
    $firstOwner = User::factory()->create(['name' => 'First Owner']);
    $secondOwner = User::factory()->create(['name' => 'Second Owner']);
    $firstEncounter = adminEncounter($firstOwner, true, 'First Encounter');
    $secondEncounter = adminEncounter($secondOwner, true, 'Second Encounter');

    $this->actingAs(User::factory()->admin()->create())
        ->get(route('admin.index'))
        ->assertSee($firstEncounter->title)
        ->assertSee($secondEncounter->title)
        ->assertSee($firstOwner->name)
        ->assertSee($secondOwner->name);
});

test('the admin area shows inactive encounters and their status', function () {
    $encounter = adminEncounter(User::factory()->create(), false, 'Inactive Encounter');

    $this->actingAs(User::factory()->admin()->create())
        ->get(route('admin.index'))
        ->assertSee($encounter->title)
        ->assertSee('Inactive');
});

test('the admin area links to encounter details', function () {
    $encounter = adminEncounter(User::factory()->create(), true, 'Linked Encounter');

    $this->actingAs(User::factory()->admin()->create())
        ->get(route('admin.index'))
        ->assertSee(route('encounters.show', $encounter));
});

test('only administrators see the admin navigation link', function () {
    $adminResponse = $this->actingAs(User::factory()->admin()->create())
        ->get(route('home'));

    $adminResponse
        ->assertSee(route('admin.index'))
        ->assertSee('Admin');

    $userResponse = $this->actingAs(User::factory()->create())
        ->get(route('home'));

    $userResponse->assertDontSee(route('admin.index'));
    $this->get(route('home'))->assertDontSee(route('admin.index'));
});

test('new registrations receive the normal user role', function () {
    $this->post(route('register.store'), [
        'name'                  => 'New User',
        'email'                 => 'new-user@example.com',
        'password'              => 'password',
        'password_confirmation' => 'password',
    ])->assertRedirect();

    expect(User::where('email', 'new-user@example.com')->first()->role)->toBe('user');
});

test('registration data cannot assign the administrator role', function () {
    $this->post(route('register.store'), [
        'name'                  => 'Attempted Admin',
        'email'                 => 'attempted-admin@example.com',
        'password'              => 'password',
        'password_confirmation' => 'password',
        'role'                  => 'admin',
    ])->assertRedirect();

    expect(User::where('email', 'attempted-admin@example.com')->first()->role)->toBe('user');
});

test('encounter ownership policy remains separate from administrator access', function () {
    $owner = User::factory()->create();
    $admin = User::factory()->admin()->create();
    $encounter = adminEncounter($owner, true, 'Owner Encounter');

    expect($admin->can('update', $encounter))->toBeFalse()
        ->and($admin->can('delete', $encounter))->toBeFalse();
});
