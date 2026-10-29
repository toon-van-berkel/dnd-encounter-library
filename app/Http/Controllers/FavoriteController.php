<?php

namespace App\Http\Controllers;

use App\Models\Encounter;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FavoriteController extends Controller {
    public function index(Request $request): View {
        $favorites = $request->user()
            ->favorites()
            ->with(['encounter.type', 'encounter.difficulty', 'encounter.user'])
            ->latest()
            ->get();

        return view('favorites.index', compact('favorites'));
    }

    public function store(Request $request, Encounter $encounter): RedirectResponse {
        $request->user()->favorites()->firstOrCreate([
            'encounter_id' => $encounter->id,
        ]);

        return redirect()
            ->route('encounters.show', $encounter)
            ->with('success', 'Encounter added to favorites.');
    }

    public function destroy(Request $request, Encounter $encounter): RedirectResponse {
        $request->user()->favorites()
            ->where('encounter_id', $encounter->id)
            ->delete();

        return $this->redirectAfterFavoriteAction($request, $encounter)
            ->with('success', 'Encounter removed from favorites.');
    }

    private function redirectAfterFavoriteAction(Request $request, Encounter $encounter): RedirectResponse {
        if ($request->input('redirect_to') === 'favorites') {
            return redirect()->route('favorites.index');
        }

        return redirect()->route('encounters.show', $encounter);
    }
}
