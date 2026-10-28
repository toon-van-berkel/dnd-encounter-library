<?php

namespace App\Http\Controllers;

use App\Models\Encounter;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class AdminController extends Controller {
    public function index(): View {
        $encounters = Encounter::query()
            ->with(['type', 'difficulty', 'user'])
            ->latest()
            ->get();

        return view('admin.index', compact('encounters'));
    }

    public function toggleStatus(Encounter $encounter): RedirectResponse {
        $encounter->is_active = ! $encounter->is_active;
        $encounter->save();

        return redirect()
            ->route('admin.index')
            ->with('success', 'Encounter status updated successfully.');
    }
}
