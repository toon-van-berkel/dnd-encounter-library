<x-layout
    title="Encounters"
    description="Browse D&D encounters created by the community and find inspiration for your next session."
    :breadcrumbs="[['label' => 'Encounters', 'url' => null]]"
>
    <section class="encounters">
        <div class="encounters__container">
            <header class="encounters__header">
                <h1 class="encounters__title">{{ $pageTitle }}</h1>
                <p class="encounters__description">
                    Discover community encounters and find inspiration for your next session.
                </p>

                <form class="encounter-filters" method="GET" action="{{ route('encounters.index') }}">
                    <div class="encounter-filters__field encounter-filters__field--search">
                        <label class="encounter-filters__label" for="encounter-search">Search encounters</label>
                    <input
                        class="encounter-filters__input"
                        id="encounter-search"
                        name="q"
                        type="search"
                        value="{{ $search }}"
                    >
                    </div>

                    <div class="encounter-filters__field">
                        <label class="encounter-filters__label" for="encounter-type">Encounter type</label>
                    <select class="encounter-filters__select" id="encounter-type" name="type_id" onchange="this.form.submit()">
                        <option value="">All types</option>
                        @foreach ($types as $type)
                            <option
                                value="{{ $type->id }}"
                                @selected((string) $typeId === (string) $type->id)
                            >
                                {{ $type->name }}
                            </option>
                        @endforeach
                    </select>
                    </div>

                    <div class="encounter-filters__field">
                        <label class="encounter-filters__label" for="encounter-difficulty">Difficulty</label>
                    <select class="encounter-filters__select" id="encounter-difficulty" name="difficulty_id" onchange="this.form.submit()">
                        <option value="">All difficulties</option>
                        @foreach ($difficulties as $difficulty)
                            <option
                                value="{{ $difficulty->id }}"
                                @selected((string) $difficultyId === (string) $difficulty->id)
                            >
                                {{ $difficulty->name }}
                            </option>
                        @endforeach
                    </select>
                    </div>

                    <div class="encounter-filters__actions">
                        <button class="encounter-filters__button" type="submit">Search</button>
                        <a class="encounter-filters__reset" href="{{ route('encounters.index') }}">Reset</a>
                    </div>
                </form>
            </header>

            <div class="encounters__grid">
                @forelse ($encounters as $encounter)
                    <x-encounter-card
                        :id="$encounter->id"
                        :title="$encounter->title"
                        :type="$encounter->type->name"
                        :difficulty="$encounter->difficulty->name"
                        :creator="$encounter->user?->name"
                        :image="$encounter->image_path"
                        :favorites-count="$encounter->favorites_count"
                    />
                @empty
                    <p class="encounters__empty">
                        {{ $search !== '' ? 'No encounters matched your search.' : 'No encounters found.' }}
                    </p>
                @endforelse
            </div>
        </div>
    </section>
</x-layout>
