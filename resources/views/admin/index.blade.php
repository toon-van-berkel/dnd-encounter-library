<x-layout
    title="Admin"
    description="Manage encounters as an administrator."
    :breadcrumbs="[['label' => 'Admin', 'url' => null]]"
>
    <section class="admin" aria-labelledby="admin-title">
        <header class="admin__header">
            <h1 class="admin__title" id="admin-title">
                Admin area
            </h1>

            <p class="admin__description">
                All encounters, including inactive encounters.
            </p>
        </header>

        <div
            class="admin__table-container"
            role="region"
            aria-label="Encounter management"
            tabindex="0"
        >
            <table class="admin__table">
                <caption class="admin__caption">
                    All encounters
                </caption>

                <thead>
                    <tr>
                        <th scope="col">Encounter</th>
                        <th scope="col">Created by</th>
                        <th scope="col">Status</th>
                        <th scope="col">Action</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($encounters as $encounter)
                        <tr>
                            <th scope="row">
                                <a
                                    class="admin__link"
                                    href="{{ route('encounters.show', $encounter) }}"
                                >
                                    {{ $encounter->title }}
                                </a>
                            </th>

                            <td>{{ $encounter->user?->name ?? 'Deleted user' }}</td>

                            <td>
                                <span @class([
                                    'admin__status',
                                    'admin__status--active' => $encounter->is_active,
                                    'admin__status--inactive' => ! $encounter->is_active,
                                ])>
                                    {{ $encounter->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>

                            <td>
                                <form
                                    class="admin__action-form"
                                    method="POST"
                                    action="{{ route('admin.encounters.toggle-status', $encounter) }}"
                                >
                                    @csrf

                                    <button
                                        @class([
                                            'admin__button',
                                            'admin__button--danger' => $encounter->is_active,
                                        ])
                                        type="submit"
                                        aria-label="{{ $encounter->is_active ? 'Deactivate' : 'Activate' }} encounter: {{ $encounter->title }}"
                                    >
                                        {{ $encounter->is_active ? 'Deactivate' : 'Activate' }}
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td class="admin__empty" colspan="4">
                                No encounters found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
</x-layout>
