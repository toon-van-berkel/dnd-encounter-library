<nav class="nav">
    <x-navlink
        class="nav-brand"
        href="{{ route('home') }}" 
        :active="request()->routeIs('home')"
    >
        {{ config('app.name') }}
    </x-navlink>
    
    <ul class="nav__list">

        <li class="nav__list__item-container">
            <x-navlink 
                href="{{ route('encounters.index') }}" 
                :active="request()->routeIs('encounters.index')"
            >
                Encounters
            </x-navlink>
        </li>

        <li class="nav__list__item-container">
            <x-navlink 
                href="{{ route('encounters.create') }}" 
                :active="request()->routeIs('encounters.create')"
            >
                Create encounter
            </x-navlink>
        </li>
    </ul>
</nav>
