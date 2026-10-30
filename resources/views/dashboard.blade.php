<x-layout
    :title="__('Dashboard')"
    :description="__('Your D&D encounter library dashboard')"
    :breadcrumbs="[['label' => __('Dashboard'), 'url' => null]]"
>
    <section class="dashboard" aria-labelledby="dashboard-title">
        <header class="dashboard__header">
            <div>
                <h1 class="dashboard__title" id="dashboard-title">
                    {{ __('My encounters') }}
                </h1>

                <p class="dashboard__description">
                    Manage your encounters and create new adventures.
                </p>
            </div>

            <a
                class="dashboard__button"
                href="{{ route('encounters.create') }}"
            >
                Create encounter
            </a>
        </header>

        <div class="dashboard__grid">
            @forelse ($encounters as $encounter)
                <x-encounter-card
                    :id="$encounter->id"
                    :title="$encounter->title"
                    :type="$encounter->type->name"
                    :difficulty="$encounter->difficulty->name"
                    :creator="auth()->user()->name"
                    :image="$encounter->image_path"
                    :favorites-count="$encounter->favorites_count"
                    :status="(bool) $encounter->is_active"
                >
                    <x-slot:actions>
                        @can('update', $encounter)
                            <a
                                class="encounter-card__link"
                                href="{{ route('encounters.edit', $encounter) }}"
                            >
                                Edit
                            </a>
                        @endcan

                        @can('delete', $encounter)
                            <form
                                class="encounter-card__delete-form"
                                method="POST"
                                action="{{ route('encounters.destroy', $encounter) }}"
                            >
                                @csrf
                                @method('DELETE')

                                <button
                                    class="encounter-card__delete"
                                    type="submit"
                                    aria-label="Delete encounter: {{ $encounter->title }}"
                                >
                                    Delete
                                </button>
                            </form>
                        @endcan
                    </x-slot:actions>
                </x-encounter-card>
            @empty
                <p class="dashboard__empty">
                    You have not created any encounters yet.
                    Create your first encounter using the button above.
                </p>
            @endforelse
        </div>
    </section>
</x-layout>
