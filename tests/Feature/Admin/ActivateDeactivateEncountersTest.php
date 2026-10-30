<?php

use App\Models\Encounter;
use App\Models\EncounterDifficulty;
use App\Models\EncounterType;
use App\Models\User;

function statusEncounter(User $owner, bool $isActive = true): Encounter {
    return Encounter::create([
        'user_id'       => $owner->id,
        'type_id'       => EncounterType::firstOrCreate(['name' => 'Combat'])->id,
        'difficulty_id' => EncounterDifficulty::firstOrCreate(['name' => 'Easy'])->id,
        'title'         => 'Status Encounter',
        'description'   => 'An encounter for status tests.',
        'min_level'     => 1,
        'max_level'     => 3,
        'party_size'    => 4,
        'is_active'     => $isActive,
    ]);
}

test('an administrator can deactivate an encounter', function () {
    $encounter = statusEncounter(User::factory()->create());

    $this->actingAs(User::factory()->admin()->create())
        ->post(route('admin.encounters.toggle-status', $encounter))
        ->assertRedirect(route('admin.index'))
        ->assertSessionHas('success', 'Encounter status updated successfully.');

    expect((bool) $encounter->fresh()->is_active)->toBeFalse();
});

test('an administrator can activate an encounter again', function () {
    $encounter = statusEncounter(User::factory()->create(), false);

    $this->actingAs(User::factory()->admin()->create())
        ->post(route('admin.encounters.toggle-status', $encounter));

    expect((bool) $encounter->fresh()->is_active)->toBeTrue();
});

test('toggling status changes no other encounter fields', function () {
    $encounter = statusEncounter(User::factory()->create());
    $before = $encounter->fresh()->only([
        'user_id',
        'type_id',
        'difficulty_id',
        'title',
        'description',
        'min_level',
        'max_level',
        'party_size',
        'image_path',
    ]);

    $this->actingAs(User::factory()->admin()->create())
        ->post(route('admin.encounters.toggle-status', $encounter));

    expect($encounter->fresh()->only(array_keys($before)))->toBe($before);
});

test('a normal user cannot toggle encounter status', function () {
    $encounter = statusEncounter(User::factory()->create());

    $this->actingAs(User::factory()->create())
        ->post(route('admin.encounters.toggle-status', $encounter))
        ->assertForbidden();

    expect((bool) $encounter->fresh()->is_active)->toBeTrue();
});

test('a guest is redirected when trying to toggle encounter status', function () {
    $encounter = statusEncounter(User::factory()->create());

    $this->post(route('admin.encounters.toggle-status', $encounter))
        ->assertRedirect(route('login'));

    expect((bool) $encounter->fresh()->is_active)->toBeTrue();
});

test('a get request does not toggle encounter status', function () {
    $encounter = statusEncounter(User::factory()->create());

    $this->actingAs(User::factory()->admin()->create())
        ->get(route('admin.encounters.toggle-status', $encounter))
        ->assertMethodNotAllowed();

    expect((bool) $encounter->fresh()->is_active)->toBeTrue();
});

test('inactive encounters disappear from the public overview after deactivation', function () {
    $owner = User::factory()->create();
    $encounter = statusEncounter($owner);

    $this->actingAs(User::factory()->admin()->create())
        ->post(route('admin.encounters.toggle-status', $encounter));

    $this->get(route('encounters.index'))
        ->assertDontSee($encounter->title);
});

test('activated encounters appear in the public overview again', function () {
    $encounter = statusEncounter(User::factory()->create(), false);

    $this->actingAs(User::factory()->admin()->create())
        ->post(route('admin.encounters.toggle-status', $encounter));

    $this->get(route('encounters.index'))
        ->assertSee($encounter->title);
});

test('the admin overview shows current status and the matching action', function () {
    $activeEncounter = statusEncounter(User::factory()->create());
    $inactiveEncounter = statusEncounter(User::factory()->create(), false);

    $response = $this->actingAs(User::factory()->admin()->create())
        ->get(route('admin.index'));

    $response
        ->assertSee('Active')
        ->assertSee('Deactivate')
        ->assertSee('Inactive')
        ->assertSee('Activate')
        ->assertSee(route('admin.encounters.toggle-status', $activeEncounter))
        ->assertSee(route('admin.encounters.toggle-status', $inactiveEncounter));
});

test('an unknown encounter cannot have its status toggled', function () {
    $this->actingAs(User::factory()->admin()->create())
        ->post(route('admin.encounters.toggle-status', 999999))
        ->assertNotFound();
});

test('the status form uses post and includes csrf protection', function () {
    $encounter = statusEncounter(User::factory()->create());

    $this->actingAs(User::factory()->admin()->create())
        ->get(route('admin.index'))
        ->assertSee('method="POST"', false)
        ->assertSee('name="_token"', false)
        ->assertSee(route('admin.encounters.toggle-status', $encounter));
});
