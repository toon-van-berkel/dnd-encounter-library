<?php

use App\Models\Encounter;
use App\Models\EncounterDifficulty;
use App\Models\EncounterType;
use App\Models\User;

function homepageEncounter(string $title, bool $isActive = true): Encounter {
    return Encounter::create([
        'user_id'       => User::factory()->create()->id,
        'type_id'       => EncounterType::firstOrCreate(['name' => 'Homepage Combat'])->id,
        'difficulty_id' => EncounterDifficulty::firstOrCreate(['name' => 'Homepage Easy'])->id,
        'title'         => $title,
        'description'   => 'A homepage encounter.',
        'min_level'     => 1,
        'max_level'     => 3,
        'party_size'    => 4,
        'is_active'     => $isActive,
    ]);
}

function addHomepageFavorites(Encounter $encounter, int $count): void {
    for ($index = 0; $index < $count; $index++) {
        User::factory()->create()->favorites()->create(['encounter_id' => $encounter->id]);
    }
}

test('guests can access the homepage', function () {
    $this->get(route('home'))
        ->assertOk()
        ->assertSee('Popular Encounters')
        ->assertSee('Create Your Own Adventure');
});

test('homepage only shows active encounters', function () {
    homepageEncounter('Active Homepage Encounter');
    homepageEncounter('Inactive Homepage Encounter', false);

    $this->get(route('home'))
        ->assertSee('Active Homepage Encounter')
        ->assertDontSee('Inactive Homepage Encounter');
});

test('homepage sorts encounters by popularity', function () {
    $popular = homepageEncounter('Popular Homepage Encounter');
    $lessPopular = homepageEncounter('Less Popular Homepage Encounter');
    addHomepageFavorites($popular, 2);
    addHomepageFavorites($lessPopular, 1);

    $this->get(route('home'))
        ->assertSeeInOrder(['Popular Homepage Encounter', 'Less Popular Homepage Encounter']);
});

test('homepage shows at most three popular encounters', function () {
    homepageEncounter('Homepage Encounter One');
    homepageEncounter('Homepage Encounter Two');
    homepageEncounter('Homepage Encounter Three');
    homepageEncounter('Homepage Encounter Four');

    $this->get(route('home'))
        ->assertSee('Homepage Encounter One')
        ->assertSee('Homepage Encounter Three')
        ->assertDontSee('Homepage Encounter Four');
});

test('homepage uses encounter id as a stable tie breaker', function () {
    $first = homepageEncounter('First Tied Homepage Encounter');
    $second = homepageEncounter('Second Tied Homepage Encounter');

    $this->get(route('home'))
        ->assertSeeInOrder([$first->title, $second->title]);
});

test('homepage shows an empty state without active encounters', function () {
    homepageEncounter('Inactive Homepage Encounter', false);

    $this->get(route('home'))
        ->assertSee('No active encounters are available yet.')
        ->assertDontSee('Inactive Homepage Encounter');
});

test('guests see the registration call to action without personal progress', function () {
    $this->get(route('home'))
        ->assertSee('Get Started')
        ->assertSee(route('register'))
        ->assertDontSee('0 / 5 favorites');
});

test('a user with zero favorites sees zero progress', function () {
    $this->actingAs(User::factory()->create())
        ->get(route('home'))
        ->assertSee('0 / 5 favorites')
        ->assertSee('Find Encounters');
});

test('a user with three favorites sees three progress', function () {
    $user = User::factory()->create();
    addHomepageFavorites(homepageEncounter('Progress Homepage Encounter'), 0);
    $favorites = collect(range(1, 3))->map(fn () => homepageEncounter('Favorite Progress Encounter'));

    foreach ($favorites as $encounter) {
        $user->favorites()->create(['encounter_id' => $encounter->id]);
    }

    $this->actingAs($user)
        ->get(route('home'))
        ->assertSee('3 / 5 favorites')
        ->assertSee('Find Encounters');
});

test('a user with five favorites sees the create link', function () {
    $user = User::factory()->create();
    foreach (range(1, 5) as $index) {
        $user->favorites()->create(['encounter_id' => homepageEncounter("Create Progress Encounter {$index}")->id]);
    }

    $this->actingAs($user)
        ->get(route('home'))
        ->assertSee('5 / 5 favorites')
        ->assertSee('Create an Encounter')
        ->assertSee(route('encounters.create'));
});

test('a user with more than five favorites sees progress capped at five', function () {
    $user = User::factory()->create();
    foreach (range(1, 6) as $index) {
        $user->favorites()->create(['encounter_id' => homepageEncounter("Capped Progress Encounter {$index}")->id]);
    }

    $this->actingAs($user)
        ->get(route('home'))
        ->assertSee('5 / 5 favorites')
        ->assertDontSee('6 / 5 favorites')
        ->assertSee('Create an Encounter');
});

test('favorites from other users do not affect personal homepage progress', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $encounter = homepageEncounter('Other User Homepage Favorite');
    $otherUser->favorites()->create(['encounter_id' => $encounter->id]);

    $this->actingAs($user)
        ->get(route('home'))
        ->assertSee('0 / 5 favorites')
        ->assertDontSee('1 / 5 favorites');
});

test('example', function () {
    $response = $this->get('/');

    $response->assertStatus(200);
});
