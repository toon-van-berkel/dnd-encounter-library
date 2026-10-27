@if (request()->routeIs('appearance.edit'))
    <x-breadcrumbs :items="[
        ['label' => __('Settings'), 'url' => null],
        ['label' => __('Appearance'), 'url' => null],
]" />
@elseif (request()->routeIs('security.edit'))
    <x-breadcrumbs :items="[
        ['label' => __('Settings'), 'url' => null],
        ['label' => __('Security'), 'url' => null],
]" />
@else
    <x-breadcrumbs :items="[
        ['label' => __('Settings'), 'url' => null],
        ['label' => __('Profile'), 'url' => null],
]" />
@endif

<div class="relative mb-6 w-full">
    <flux:heading size="xl" level="1">{{ __('Settings') }}</flux:heading>
    <flux:subheading size="lg" class="mb-6">{{ __('Manage your profile and account settings') }}</flux:subheading>
    <flux:separator variant="subtle" />
</div>
