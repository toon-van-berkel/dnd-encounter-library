<x-layout
    title="Favorites"
    description="View your favorite encounters."
    :breadcrumbs="[['label' => 'Favorites', 'url' => null]]"
>
    <h1>Favorite encounters</h1>

    @forelse ($favorites as $favorite)
        <article>
            <h2>
                <a href="{{ route('encounters.show', $favorite->encounter) }}">
                    {{ $favorite->encounter->title }}
                </a>
            </h2>

            <p>Type: {{ $favorite->encounter->type->name }}</p>
            <p>Difficulty: {{ $favorite->encounter->difficulty->name }}</p>

            <form method="POST" action="{{ route('encounters.unfavorite', $favorite->encounter) }}">
                @csrf
                @method('DELETE')
                <input type="hidden" name="redirect_to" value="favorites">
                <button type="submit">Remove from favorites</button>
            </form>
        </article>
    @empty
        <p>You haven't added any favorite encounters yet.</p>
        <a href="{{ route('encounters.index') }}">Browse encounters</a>
    @endforelse
</x-layout>
