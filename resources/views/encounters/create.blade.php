<x-layout
    title="Create encounter"
    description="Create and share a new D&D encounter with the community."
    :breadcrumbs="[['label' => 'Encounters', 'url' => route('encounters.index')], ['label' => 'Create encounter', 'url' => null]]"
>
    <div class="encounter-create">
        <form class="form" action="{{ route('encounters.store') }}" method="POST">
            @csrf

            <header class="form__header">
                <h1 class="form__title">Create an encounter</h1>
                <p class="form__description">
                    Create and share a new D&D encounter with the community.
                </p>
            </header>

            @if ($errors->any())
                <div class="form__errors" role="alert">
                    <p>Please fix the following errors:</p>
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="form__row">
                <div class="form__field">
                    <label class="form__label" for="encounter-type">
                        Encounter type
                    </label>

                    <select
                        class="form__input"
                        name="type_id"
                        id="encounter-type"
                        required
                    >
                        <option value="">Please select a type</option>
                        @foreach ($types as $type)
                            <option
                                value="{{ $type->id }}"
                                @selected(old('type_id') == $type->id)
                            >
                                {{ $type->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form__field">
                    <label class="form__label" for="encounter-difficulty">
                        Encounter difficulty
                    </label>

                    <select
                        class="form__input"
                        name="difficulty_id"
                        id="encounter-difficulty"
                        required
                    >
                        <option value="">Please select a difficulty</option>
                        @foreach ($difficulties as $difficulty)
                            <option
                                value="{{ $difficulty->id }}"
                                @selected(old('difficulty_id') == $difficulty->id)
                            >
                                {{ $difficulty->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="form__field">
                <label class="form__label" for="encounter-title">Title</label>

                <input
                    id="encounter-title"
                    name="title"
                    class="form__input"
                    type="text"
                    value="{{ old('title') }}"
                    placeholder="Give your encounter a title"
                    maxlength="200"
                    required
                >
            </div>

            <div class="form__field">
                <label class="form__label" for="encounter-description">
                    Description
                </label>

                <textarea
                    class="form__input form__input--textarea"
                    name="description"
                    id="encounter-description"
                    placeholder="Describe the setting, challenges and possible outcomes..."
                    rows="6"
                    required
                >{{ old('description') }}</textarea>
            </div>

            <div class="form__row form__row--three">
                <div class="form__field">
                    <label class="form__label" for="encounter-min-level">
                        Minimum level
                    </label>

                    <input
                        id="encounter-min-level"
                        name="min_level"
                        class="form__input"
                        type="number"
                        min="1"
                        max="20"
                        value="{{ old('min_level') }}"
                        required
                    >
                </div>

                <div class="form__field">
                    <label class="form__label" for="encounter-max-level">
                        Maximum level
                    </label>

                    <input
                        id="encounter-max-level"
                        name="max_level"
                        class="form__input"
                        type="number"
                        min="1"
                        max="20"
                        value="{{ old('max_level') }}"
                        required
                    >
                </div>

                <div class="form__field">
                    <label class="form__label" for="encounter-party-size">
                        Party size
                    </label>

                    <input
                        id="encounter-party-size"
                        name="party_size"
                        class="form__input"
                        type="number"
                        min="1"
                        max="20"
                        value="{{ old('party_size') }}"
                        required
                    >
                </div>
            </div>

            <div class="form__field">
                <label class="form__label" for="encounter-image-path">
                    Image URL <span class="form__optional">(optional)</span>
                </label>

                <input
                    id="encounter-image-path"
                    name="image_path"
                    class="form__input"
                    type="url"
                    value="{{ old('image_path') }}"
                    placeholder="https://example.com/encounter.webp"
                    maxlength="255"
                >
            </div>

            <button class="form__button" type="submit">
                Create encounter
            </button>
        </form>
    </div>
</x-layout>
