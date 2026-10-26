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

        @guest
            <li class="nav__list__item-container">
                <x-navlink href="{{ route('login') }}" :active="request()->routeIs('login')">
                    Log in
                </x-navlink>
            </li>

            <li class="nav__list__item-container">
                <x-navlink href="{{ route('register') }}" :active="request()->routeIs('register')">
                    Register
                </x-navlink>
            </li>
        @endguest

        @auth
            <li class="nav__list__item-container">
                <span>{{ auth()->user()->name }}</span>
            </li>

            <li class="nav__list__item-container">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit">Log out</button>
                </form>
            </li>
        @endauth
    </ul>
</nav>
