<?php

namespace App\Http\Controllers;

use App\Models\Encounter;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HomeController extends Controller {
    private const REQUIRED_FAVORITES = 5;

    public function index(Request $request): View {
        $popularEncounters = Encounter::query()
            ->where('is_active', true)
            ->with(['type', 'difficulty', 'user'])
            ->withCount('favorites')
            ->orderByDesc('favorites_count')
            ->orderBy('id')
            ->limit(3)
            ->get();

        $favoriteCount = $request->user()?->favorites()->count();

        return view('welcome', [
            'popularEncounters' => $popularEncounters,
            'favoriteCount'     => $favoriteCount,
            'requiredFavorites' => self::REQUIRED_FAVORITES,
        ]);
    }
}
