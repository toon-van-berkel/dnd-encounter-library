<x-layout
    :title="$encounter->title"
    :description="$encounter->description"
>
    <h1>{{ $encounter->title }}</h1>

    <p>{{ $encounter->description }}</p>

    <p>Type: {{ $encounter->type_id }}</p>
    <p>Difficulty: {{ $encounter->difficulty_id }}</p>
    <p>Level: {{ $encounter->min_level }} - {{ $encounter->max_level }}</p>
    <p>Party size: {{ $encounter->party_size }}</p>
</x-layout>