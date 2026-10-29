<nav class="nav">
    <x-navlink
        class="nav__brand"
        href="{{ route('home') }}"
        :active="request()->routeIs('home')"
    >
        {{ config('app.name') }}
    </x-navlink>

    <ul class="nav__list">
        <li class="nav__list-item">
            <x-navlink
                class="nav__link"
                href="{{ route('encounters.index') }}"
                :active="request()->routeIs('encounters.index')"
            >
                Encounters
            </x-navlink>
        </li>

        <li class="nav__list-item">
            <x-navlink
                class="nav__link"
                href="{{ route('encounters.create') }}"
                :active="request()->routeIs('encounters.create')"
            >
                Create encounter
            </x-navlink>
        </li>

        @auth
            <li class="nav__list-item">
                <x-navlink
                    class="nav__link"
                    href="{{ route('dashboard') }}"
                    :active="request()->routeIs('dashboard')"
                >
                    Dashboard
                </x-navlink>
            </li>

            <li class="nav__list-item">
                <x-navlink
                    class="nav__link"
                    href="{{ route('favorites.index') }}"
                    :active="request()->routeIs('favorites.*')"
                >
                    Favorites
                </x-navlink>
            </li>
        @endauth

        @can('access-admin')
            <li class="nav__list-item">
                <x-navlink
                    class="nav__link"
                    href="{{ route('admin.index') }}"
                    :active="request()->routeIs('admin.*')"
                >
                    Admin
                </x-navlink>
            </li>
        @endcan

        @guest
            <li class="nav__list-item">
                <x-navlink
                    class="nav__link"
                    href="{{ route('login') }}"
                    :active="request()->routeIs('login')"
                >
                    Log in
                </x-navlink>
            </li>

            <li class="nav__list-item">
                <x-navlink
                    class="nav__link"
                    href="{{ route('register') }}"
                    :active="request()->routeIs('register')"
                >
                    Register
                </x-navlink>
            </li>
        @endguest

        @auth
            <li class="nav__list-item">
                <span class="nav__user">{{ auth()->user()->name }}</span>
            </li>

            <li class="nav__list-item">
                <form class="nav__logout-form" method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="nav__logout" type="submit">Log out</button>
                </form>
            </li>
        @endauth
    </ul>
</nav>
