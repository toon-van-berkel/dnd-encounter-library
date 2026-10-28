<x-layout
    :title="$encounter->title"
    :description="$encounter->description"
    :breadcrumbs="[['label' => 'Encounters', 'url' => route('encounters.index')], ['label' => $encounter->title, 'url' => null]]"
>
    <article class="encounter">
        <header class="encounter__header">
            @if ($encounter->image_path)
                <img
                    class="encounter__image"
                    src="{{ $encounter->image_path }}"
                    alt="Illustration for {{ $encounter->title }}"
                >
            @endif

            <div class="encounter__badges">
                <span class="encounter__badge">
                    {{ $encounter->type->name }}
                </span>

                <span class="encounter__badge encounter__badge--difficulty">
                    {{ $encounter->difficulty->name }}
                </span>
            </div>

            <h1 class="encounter__title">{{ $encounter->title }}</h1>

            <p class="encounter__creator">
                Created by
                <span class="encounter__creator-name">
                    {{ $encounter->user->name }}
                </span>
            </p>
        </header>

        <dl class="encounter__details">
            <div class="encounter__detail">
                <dt class="encounter__detail-label">Character level</dt>
                <dd class="encounter__detail-value">
                    {{ $encounter->min_level }} – {{ $encounter->max_level }}
                </dd>
            </div>

            <div class="encounter__detail">
                <dt class="encounter__detail-label">Party size</dt>
                <dd class="encounter__detail-value">
                    {{ $encounter->party_size }} players
                </dd>
            </div>
        </dl>

        <section class="encounter__content" aria-labelledby="encounter-description">
            <h2 class="encounter__heading" id="encounter-description">
                Description
            </h2>

            <p class="encounter__description">{{ $encounter->description }}</p>
        </section>

        @canany(['update', 'delete'], $encounter)
            <footer class="encounter__actions">
                @can('update', $encounter)
                    <a
                        class="encounter__button"
                        href="{{ route('encounters.edit', $encounter) }}"
                    >
                        Edit encounter
                    </a>
                @endcan

                @can('delete', $encounter)
                    <form
                        class="encounter__delete-form"
                        action="{{ route('encounters.destroy', $encounter) }}"
                        method="POST"
                    >
                        @csrf
                        @method('DELETE')

                        <button
                            class="encounter__button encounter__button--danger"
                            type="submit"
                        >
                            Delete encounter
                        </button>
                    </form>
                @endcan
            </footer>
        @endcanany
    </article>
</x-layout>
