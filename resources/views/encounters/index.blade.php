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

                <form method="GET" action="{{ route('encounters.index') }}">
                    <label for="encounter-search">Search encounters</label>
                    <input
                        id="encounter-search"
                        name="q"
                        type="search"
                        value="{{ $search }}"
                    >

                    <label for="encounter-type">Encounter type</label>
                    <select id="encounter-type" name="type_id">
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

                    <label for="encounter-difficulty">Difficulty</label>
                    <select id="encounter-difficulty" name="difficulty_id">
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

                    <button type="submit">Search</button>
                    <a href="{{ route('encounters.index') }}">Reset</a>
                </form>
            </header>

            <div class="encounters__grid">
                @forelse ($encounters as $encounter)
                    <x-encounter-card
                        :id="$encounter->id"
                        :title="$encounter->title"
                        :type="$encounter->type->name"
                        :difficulty="$encounter->difficulty->name"
                        :creator="$encounter->user->name"
                        :image="$encounter->image_path"
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
