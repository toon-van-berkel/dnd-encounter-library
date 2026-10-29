<?php

use App\Models\Encounter;
use App\Models\EncounterDifficulty;
use App\Models\EncounterType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
|
| The closure you provide to your test functions is always bound to a specific PHPUnit test
| case class. By default, that class is "PHPUnit\Framework\TestCase". Of course, you may
| need to change it using the "pest()" function to bind different classes or traits.
|
*/

pest()->extend(TestCase::class)
    ->use(RefreshDatabase::class)
    ->in('Feature');

/*
|--------------------------------------------------------------------------
| Expectations
|--------------------------------------------------------------------------
|
| When you're writing tests, you often need to check that values meet certain conditions. The
| "expect()" function gives you access to a set of "expectations" methods that you can use
| to assert different things. Of course, you may extend the Expectation API at any time.
|
*/

expect()->extend('toBeOne', function () {
    return $this->toBe(1);
});

/*
|--------------------------------------------------------------------------
| Functions
|--------------------------------------------------------------------------
|
| While Pest is very powerful out-of-the-box, you may have some testing code specific to your
| project that you don't want to repeat in every file. Here you can also expose helpers as
| global functions to help you to reduce the number of lines of code in your test files.
|
*/

function something() {
    // ..
}

function giveFiveFavorites(User $user): void {
    $type = EncounterType::firstOrCreate(['name' => 'Test Combat']);
    $difficulty = EncounterDifficulty::firstOrCreate(['name' => 'Test Easy']);

    for ($index = 1; $index <= 5; $index++) {
        $encounter = Encounter::create([
            'user_id'       => User::factory()->create()->id,
            'type_id'       => $type->id,
            'difficulty_id' => $difficulty->id,
            'title'         => "Favorite prerequisite {$index}",
            'description'   => 'A prerequisite favorite.',
            'min_level'     => 1,
            'max_level'     => 3,
            'party_size'    => 4,
            'is_active'     => true,
        ]);

        $user->favorites()->create(['encounter_id' => $encounter->id]);
    }
}
