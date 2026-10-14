<x-layout
    title="Create encounter"
    description="Create and share a new D&D encounter with the community."
>

    <form class="form" action="{{ route('encounters.store') }}" method="POST">
        @csrf

        <h1 class="form-title">Create an encounter</h1>

        @if ($errors->any())
            <div class="form-errors" role="alert">
                <p>Please fix the following errors:</p>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <label class="form-label" for="encounter-type">Encounter type</label>
        <select class="form-input" name="type_id" id="encounter-type" required>
            <option value="">Please select a type</option>
            @foreach ($types as $type)
                <option 
                value="{{ $type->id }}" 
                @selected(old('type_id') == $type->id)
            >{{ $type->name }}</option>
            @endforeach
        </select>

        <label class="form-label" for="encounter-difficulty">Encounter difficulty</label>
        <select class="form-input" name="difficulty_id" id="encounter-difficulty" required>
            <option value="">Please select a difficulty</option>
            @foreach ($difficulties as $difficulty)
                <option 
                    value="{{ $difficulty->id }}" 
                    @selected(old('difficulty_id') == $difficulty->id)
                >{{ $difficulty->name }}</option>
            @endforeach
        </select>

        <label class="form-label" for="encounter-title">Title</label>
        <input 
            id="encounter-title" 
            name="title" 
            class="form-input" 
            type="text" 
            value="{{ old('title') }}" 
            maxlength="200" 
            required
        >

        <label class="form-label" for="encounter-description">Description</label>
        <textarea 
            class="form-input" 
            name="description" 
            id="encounter-description" 
            rows="5" 
            required
        >{{ old('description') }}</textarea>

        <label class="form-label" for="encounter-min-level">Minimum character level</label>
        <input 
            id="encounter-min-level" 
            name="min_level" 
            class="form-input" 
            type="number" 
            min="1" 
            max="20" 
            value="{{ old('min_level') }}" 
            required
        >

        <label class="form-label" for="encounter-max-level">Maximum character level</label>
        <input 
            id="encounter-max-level" 
            name="max_level" 
            class="form-input" 
            type="number" 
            min="1" 
            max="20" 
            value="{{ old('max_level') }}" 
            required
        >

        <label class="form-label" for="encounter-party-size">Party size</label>
        <input 
            id="encounter-party-size" 
            name="party_size" 
            class="form-input" 
            type="number" 
            min="1" 
            max="20" 
            value="{{ old('party_size') }}" 
            required
        >

        <label class="form-label" for="encounter-image-path">Image URL (optional)</label>
        <input 
            id="encounter-image-path" 
            name="image_path" 
            class="form-input" 
            type="url" 
            value="{{ old('image_path') }}" 
            maxlength="255"
        >

        <button class="form-button" type="submit">Create encounter</button>
    </form>
</x-layout>
