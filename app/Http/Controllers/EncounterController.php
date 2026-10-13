<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Encounter;

class EncounterController extends Controller {
    // Display a list of all encounters
    public function index() {
        $pageTitle = 'Encounters';

        $encounters = Encounter::all();

        return view('encounters.index', compact(
            'pageTitle',
            'encounters'
        ));
    }

    // Display the form for creating a new encounter
    public function create() {
        return view('encounters.create');
    }

    // Store a newly created encounter
    public function store(Request $request) {
        //
    }

    // Display a specific encounter
    public function show(string $id) {
        return view('encounters.show', compact('id'));
    }

    // Display the form for editing a specific encounter
    public function edit(string $id) {
        return view('encounters.edit', compact('id'));
    }

    // Update an existing encounter
    public function update(Request $request, string $id) {
        //
    }

    // Display the deletion confirmation page
    public function destroy(string $id) {
        // Require confirmation before permanently deleting the encounter
        return view('encounters.destroy', compact('id'));
    }
}