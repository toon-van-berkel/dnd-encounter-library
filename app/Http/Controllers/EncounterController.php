<?php

namespace App\Http\Controllers;

use App\Models\Encounter;
use App\Models\EncounterDifficulty;
use App\Models\EncounterType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class EncounterController extends Controller {
    private const REQUIRED_FAVORITES = 5;

    // Display a list of all encounters
    public function index(Request $request) {
        $pageTitle = 'Encounters';
        $search = trim((string) $request->query('q', ''));
        $typeId = $request->query('type_id');
        $difficultyId = $request->query('difficulty_id');

        $encounters = Encounter::query()
            ->where('is_active', true)
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query
                        ->where('title', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%");
                });
            })
            ->when($typeId !== null && $typeId !== '', function ($query) use ($typeId) {
                $query->where('type_id', $typeId);
            })
            ->when($difficultyId !== null && $difficultyId !== '', function ($query) use ($difficultyId) {
                $query->where('difficulty_id', $difficultyId);
            })
            ->with(['type', 'difficulty', 'user'])
            ->get();

        $types = EncounterType::orderBy('name')->get();
        $difficulties = EncounterDifficulty::orderBy('id')->get();

        return view('encounters.index', compact(
            'pageTitle',
            'encounters',
            'search',
            'typeId',
            'difficultyId',
            'types',
            'difficulties'
        ));
    }

    // Display the form for creating a new encounter
    public function create(Request $request) {
        $favoriteCount = $request->user()->favorites()->count();
        $types = EncounterType::orderBy('name')->get();
        $difficulties = EncounterDifficulty::orderBy('id')->get();

        return view('encounters.create', [
            'types'             => $types,
            'difficulties'      => $difficulties,
            'favoriteCount'     => $favoriteCount,
            'requiredFavorites' => self::REQUIRED_FAVORITES,
        ]);
    }

    // Store a newly created encounter
    public function store(Request $request) {
        $favoriteCount = $request->user()->favorites()->count();

        if ($favoriteCount < self::REQUIRED_FAVORITES) {
            return redirect()
                ->route('encounters.create')
                ->withErrors([
                    'favorites' => 'You need at least '.self::REQUIRED_FAVORITES
                        .' favorites to create an encounter. You currently have '
                        .$favoriteCount.'.',
                ])
                ->with('favoriteCount', $favoriteCount);
        }

        $validated = $request->validate([
            'type_id'       => ['required', 'integer', 'exists:encounter_types,id'],
            'difficulty_id' => ['required', 'integer', 'exists:encounter_difficulties,id'],
            'title'         => ['required', 'string', 'max:200'],
            'description'   => ['required', 'string'],
            'min_level'     => ['required', 'integer', 'min:1', 'max:20'],
            'max_level'     => ['required', 'integer', 'min:1', 'max:20', 'gte:min_level'],
            'party_size'    => ['required', 'integer', 'min:1', 'max:20'],
            'image_path'    => ['nullable', 'url', 'max:255'],
        ]);

        $validated['user_id'] = $request->user()->id;

        $encounter = Encounter::create($validated);

        return redirect()
            ->route('encounters.show', $encounter)
            ->with('success', 'Encounter created successfully.');
    }

    // Display a specific encounter
    public function show(string $id) {
        $encounter = Encounter::query()
            ->with(['type', 'difficulty', 'user'])
            ->findOrFail($id);

        $isFavorited = auth()->check()
            && $encounter->favorites()->where('user_id', auth()->id())->exists();

        return view('encounters.show', compact('encounter', 'isFavorited'));
    }

    // Display the form for editing a specific encounter
    public function edit(Encounter $encounter) {
        Gate::authorize('update', $encounter);
        $types = EncounterType::orderBy('name')->get();
        $difficulties = EncounterDifficulty::orderBy('id')->get();

        return view('encounters.edit', compact(
            'encounter',
            'types',
            'difficulties'
        ));
    }

    // Update an existing encounter
    public function update(Request $request, Encounter $encounter) {
        Gate::authorize('update', $encounter);

        $validated = $request->validate([
            'type_id'       => ['required', 'integer', 'exists:encounter_types,id'],
            'difficulty_id' => ['required', 'integer', 'exists:encounter_difficulties,id'],
            'title'         => ['required', 'string', 'max:200'],
            'description'   => ['required', 'string'],
            'min_level'     => ['required', 'integer', 'min:1', 'max:20'],
            'max_level'     => ['required', 'integer', 'min:1', 'max:20', 'gte:min_level'],
            'party_size'    => ['required', 'integer', 'min:1', 'max:20'],
            'image_path'    => ['nullable', 'url', 'max:255'],
        ]);

        $encounter->update($validated);

        return redirect()
            ->route('encounters.show', $encounter)
            ->with('success', 'Encounter updated successfully.');
    }

    // Delete an encounter
    public function destroy(Encounter $encounter) {
        Gate::authorize('delete', $encounter);

        $encounter->delete();

        return redirect()
            ->route('encounters.index')
            ->with('success', 'Encounter deleted successfully.');
    }
}
