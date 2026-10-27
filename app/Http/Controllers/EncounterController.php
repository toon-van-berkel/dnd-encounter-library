<?php

namespace App\Http\Controllers;

use App\Models\Encounter;
use App\Models\EncounterDifficulty;
use App\Models\EncounterType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class EncounterController extends Controller {
    // Display a list of all encounters
    public function index() {
        $pageTitle = 'Encounters';
        $encounters = Encounter::query()
            ->where('is_active', true)
            ->with(['type', 'difficulty', 'user'])
            ->get();

        return view('encounters.index', compact(
            'pageTitle',
            'encounters'
        ));
    }

    // Display the form for creating a new encounter
    public function create() {
        $types = EncounterType::orderBy('name')->get();
        $difficulties = EncounterDifficulty::orderBy('id')->get();

        return view('encounters.create', compact('types', 'difficulties'));
    }

    // Store a newly created encounter
    public function store(Request $request) {
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

        return view('encounters.show', compact('encounter'));
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
