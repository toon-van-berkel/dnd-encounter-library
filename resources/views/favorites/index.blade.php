<x-layout
    title="Favorites"
    description="View your favorite encounters."
    :breadcrumbs="[['label' => 'Favorites', 'url' => null]]"
>
    <section class="encounters">
        <div class="encounters__container">
            <header class="encounters__header">
                <h1 class="encounters__title">Favorite encounters</h1>
                <p class="encounters__description">
                    Keep your favorite encounters close at hand for your next session.
                </p>
            </header>

            <div class="encounters__grid">
                @forelse ($favorites as $favorite)
                    @php
                        $canViewEncounter = $favorite->encounter->is_active
                            || auth()->id() === $favorite->encounter->user_id
                            || auth()->user()->isAdmin();
                    @endphp

                    @if ($canViewEncounter)
                        <x-encounter-card
                            :id="$favorite->encounter->id"
                            :title="$favorite->encounter->title"
                            :type="$favorite->encounter->type->name"
                            :difficulty="$favorite->encounter->difficulty->name"
                            :creator="$favorite->encounter->user?->name"
                            :image="$favorite->encounter->image_path"
                            :favorites-count="$favorite->encounter->favorites_count"
                        >
                            <x-slot:actions>
                                <form
                                    class="encounter-card__delete-form"
                                    method="POST"
                                    action="{{ route('encounters.unfavorite', $favorite->encounter) }}"
                                >
                                    @csrf
                                    @method('DELETE')
                                    <input type="hidden" name="redirect_to" value="favorites">
                                    <button class="encounter-card__delete" type="submit">
                                        Remove from favorites
                                    </button>
                                </form>
                            </x-slot:actions>
                        </x-encounter-card>
                    @else
                        <article class="encounter-card">
                            <h2 class="encounter-card__title">Unavailable encounter</h2>
                            <form
                                class="encounter-card__delete-form"
                                method="POST"
                                action="{{ route('encounters.unfavorite', $favorite->encounter) }}"
                            >
                                @csrf
                                @method('DELETE')
                                <input type="hidden" name="redirect_to" value="favorites">
                                <button class="encounter-card__delete" type="submit">
                                    Remove from favorites
                                </button>
                            </form>
                        </article>
                    @endif
                @empty
                    <p class="encounters__empty">
                        You haven't added any favorite encounters yet.
                        <a href="{{ route('encounters.index') }}">Browse encounters</a>
                    </p>
                @endforelse
            </div>
        </div>
    </section>
</x-layout>
