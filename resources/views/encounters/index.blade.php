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
            </header>

            <div class="encounters__grid">
                @forelse ($encounters as $encounter)
                    <x-encounter-card
                        :id="$encounter->id"
                        :title="$encounter->title"
                        :type="$encounter->type->name"
                        :difficulty="$encounter->difficulty->name"
                        :creator="$encounter->user->name"
                    />
                @empty
                    <p class="encounters__empty">
                        No encounters found.
                    </p>
                @endforelse
            </div>
        </div>
    </section>
</x-layout>
