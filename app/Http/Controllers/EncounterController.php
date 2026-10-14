<?php

namespace App\Http\Controllers;

use App\Models\Encounter;
use App\Models\EncounterDifficulty;
use App\Models\EncounterType;
use Illuminate\Http\Request;

class EncounterController extends Controller
{
    // Display a list of all encounters
    public function index()
    {
        $pageTitle = 'Encounters';
        $encounters = Encounter::all();

        return view('encounters.index', compact(
            'pageTitle',
            'encounters'
        ));
    }

    // Display the form for creating a new encounter
    public function create()
    {
        $types        = EncounterType::orderBy('name')->get();
        $difficulties = EncounterDifficulty::orderBy('id')->get();

        return view('encounters.create', compact('types', 'difficulties'));
    }

    // Store a newly created encounter
    public function store(Request $request)
    {
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

        $validated['user_id'] = 1;

        $encounter = Encounter::create($validated);

        return redirect()
            ->route('encounters.show', $encounter)
            ->with('success', 'Encounter created successfully.');
    }

    // Display a specific encounter
    public function show(string $id)
    {
        $encounter = Encounter::findOrFail($id);

        return view('encounters.show', compact('encounter'));
    }

    // Display the form for editing a specific encounter
    public function edit(string $id)
    {
        return view('encounters.edit', compact('id'));
    }

    // Update an existing encounter
    public function update(Request $request, string $id)
    {
        //
    }

    // Display the deletion confirmation page
    public function destroy(string $id)
    {
        // Require confirmation before permanently deleting the encounter
        return view('encounters.destroy', compact('id'));
    }
}
