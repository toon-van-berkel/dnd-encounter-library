@props([
    'id',
    'title',
    'type',
    'difficulty',
    'creator',
    'image' => null,
    'status' => null,
])

<article class="encounter-card">
    @if ($image)
        <img
            class="encounter-card__image"
            src="{{ $image }}"
            alt="Illustration for {{ $title }}"
            loading="lazy"
        >
    @endif

    <div class="encounter-card__badges">
        <span class="encounter-card__badge">
            {{ $type }}
        </span>

        <span class="encounter-card__badge encounter-card__badge--difficulty">
            {{ $difficulty }}
        </span>
        
    </div>

    <h2 class="encounter-card__title">
        <a
            class="encounter-card__title-link"
            href="{{ route('encounters.show', $id) }}"
        >
            {{ $title }}
        </a>
    </h2>

    <p class="encounter-card__creator">
        Created by
        <span class="encounter-card__creator-name">
            {{ $creator }}
        </span>
    </p>

    <div class="encounter-card__actions">
        <a
            class="encounter-card__link"
            href="{{ route('encounters.show', $id) }}"
        >
            View encounter <span aria-hidden="true">&rarr;</span>
        </a>

        {{ $actions ?? '' }}
    </div>
</article>