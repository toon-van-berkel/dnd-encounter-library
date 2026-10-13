<nav>
    <x-navlink href="{{ route('home') }}" :active="request()->routeIs('home')">
        Home
    </x-navlink>

    <x-navlink href="{{ route('encounters.index') }}" :active="request()->routeIs('encounters.index')">
        Encounters
    </x-navlink>

    <x-navlink href="{{ route('encounters.create') }}" :active="request()->routeIs('encounters.create')">
        Create encounter
    </x-navlink>
</nav>