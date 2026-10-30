<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller {
    public function index(Request $request): View {
        $encounters = $request->user()
            ->encounters()
            ->with(['type', 'difficulty'])
            ->withCount('favorites')
            ->latest()
            ->get();

        return view('dashboard', compact('encounters'));
    }
}
