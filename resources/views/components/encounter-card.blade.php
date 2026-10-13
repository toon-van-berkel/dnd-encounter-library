@props([
    'id',
    'title',
    'type',
    'difficulty',
    'creator'
])

<article class="encounter-card">
    <h2>{{ $title }}</h2>

    <p>Type: {{ $type }}</p>
    <p>Difficulty: {{ $difficulty }}</p>
    <p>Created by: {{ $creator }}</p>

    <a href="{{ route('encounters.show', $id) }}">
        View encounter
    </a>
</article>