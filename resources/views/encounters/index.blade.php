<x-layout
    title="Encounters"
    description="Browse D&D encounters created by the community and find inspiration for your next session."
>
    <h1>{{ $pageTitle }}</h1>

    @foreach ($encounters as $encounter)
        <x-encounter-card
            :id="$encounter->id"
            :title="$encounter->title"
            :type="$encounter->type_id"
            :difficulty="$encounter->difficulty_id"
            creator="User {{ $encounter->user_id }}"
        />
    @endforeach
</x-layout>