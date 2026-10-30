<x-layout
    title="D&D Encounter Library"
    description="Discover, create and save D&D encounters for your campaigns."
>
    <header class="banner"
        alt="header showing: dungeons and Dragons, players fighting a group of skeletons.">
        <div class="banner__inner">
            <h1 class="banner__inner-title">Explore the Dungeons and Dragons encounter library.</h1>
            <p class="banner__inner-description">
                Discover, save, and share Dungeons & Dragons encounters. Find your next adventure or create one of your own.
            </p>
            <a class="banner__inner-link" href="{{ route('encounters.index') }}">
                Explore Encounters
            </a>
        </div>
    </header>

    <section class="encounters homepage-section homepage-section--popular">
        <div class="encounters__container homepage-section__inner">
            <header class="encounters__header homepage-section__header">
                <h2 class="encounters__title homepage-section__title">Popular Encounters</h2>
                <p class="encounters__description homepage-section__description">
                    Discover the encounters adventurers love most. Find inspiration for your next session and save your favorites.
                </p>
            </header>

            @if ($popularEncounters->isNotEmpty())
                <div class="encounters__grid homepage-section__grid">
                    @foreach ($popularEncounters as $encounter)
                        <x-encounter-card
                            :id="$encounter->id"
                            :title="$encounter->title"
                            :type="$encounter->type->name"
                            :difficulty="$encounter->difficulty->name"
                            :creator="$encounter->user?->name"
                            :image="$encounter->image_path"
                            :favorites-count="$encounter->favorites_count"
                        />
                    @endforeach
                </div>
            @else
                <p class="encounters__empty homepage-section__empty">
                    No active encounters are available yet. Check back soon for new adventures.
                </p>
            @endif

            <a
                class="encounter-card__link homepage-section__link homepage-section__link--explore-all"
                href="{{ route('encounters.index') }}"
            >
                Explore All Encounters
            </a>
        </div>
    </section>

    <section class="encounters homepage-section homepage-section--create">
        <div class="encounters__container homepage-section__inner">
            <header class="encounters__header homepage-section__header">
                <h2 class="encounters__title homepage-section__title">Create Your Own Adventure</h2>
                <p class="encounters__description homepage-section__description">
                    Every great adventure starts with inspiration. Save five encounters to your favorites and unlock the ability to create and share your own.
                </p>
            </header>

            @guest
                <a class="encounter-card__link homepage-section__link" href="{{ route('register') }}">Get Started</a>
            @else
                @php($displayFavoriteCount = min($favoriteCount, $requiredFavorites))
                <p class="homepage-section__progress">
                    {{ $displayFavoriteCount }} / {{ $requiredFavorites }} favorites
                </p>
                <ol class="homepage-section__indicators" aria-label="Favorite progress">
                    @for ($index = 1; $index <= $requiredFavorites; $index++)
                        <li
                            class="homepage-section__indicator {{ $index <= $displayFavoriteCount ? 'homepage-section__indicator--complete' : '' }}"
                            aria-label="{{ $index <= $displayFavoriteCount ? 'Completed favorite' : 'Favorite not completed' }}"
                        >
                            {{ $index }}
                        </li>
                    @endfor
                </ol>

                @if ($favoriteCount >= $requiredFavorites)
                    <a class="encounter-card__link homepage-section__link" href="{{ route('encounters.create') }}">
                        Create an Encounter
                    </a>
                @else
                    <a class="encounter-card__link homepage-section__link" href="{{ route('encounters.index') }}">
                        Find Encounters
                    </a>
                @endif
            @endguest
        </div>
    </section>
</x-layout>
