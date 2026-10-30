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
            <div class="nav__dropdown">
                <x-navlink
                    class="nav__link nav__dropdown-toggle"
                    href="{{ route('encounters.index') }}"
                    :active="request()->routeIs('encounters.index')"
                >
                    Encounters
                </x-navlink>
                <div class="nav__dropdown-menu">
                    <x-navlink
                        class="nav__dropdown-link"
                        href="{{ route('encounters.create') }}"
                        :active="request()->routeIs('encounters.create')"
                    >
                        Create encounter
                    </x-navlink>
                </div>
            </div>
        </li>

        @auth
            <li class="nav__list-item">
                <div class="nav__dropdown nav__user-dropdown">
                    <span class="nav__user nav__dropdown-toggle" tabindex="0">
                        {{ auth()->user()->name }}
                    </span>
                    <div class="nav__dropdown-menu">
                        <x-navlink
                            class="nav__dropdown-link"
                            href="{{ route('dashboard') }}"
                            :active="request()->routeIs('dashboard')"
                        >
                            My encounters
                        </x-navlink>
                        <x-navlink
                            class="nav__dropdown-link"
                            href="{{ route('favorites.index') }}"
                            :active="request()->routeIs('favorites.*')"
                        >
                            My favorites
                        </x-navlink>
                        @can('access-admin')
                            <x-navlink
                                class="nav__dropdown-link"
                                href="{{ route('admin.index') }}"
                                :active="request()->routeIs('admin.*')"
                            >
                                Admin
                            </x-navlink>
                        @endcan
                    </div>
                </div>
            </li>
        @else
            <li class="nav__list-item">
                <x-navlink
                    class="nav__button nav__button--primary"
                    href="{{ route('login') }}"
                    :active="request()->routeIs('login')"
                >
                    Log in
                </x-navlink>
            </li>

            <li class="nav__list-item">
                <x-navlink
                    class="nav__button nav__button--secondary"
                    href="{{ route('register') }}"
                    :active="request()->routeIs('register')"
                >
                    Register
                </x-navlink>
            </li>
        @endauth

        @auth
            <li class="nav__list-item">
                <form class="nav__logout-form" method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="nav__logout nav__button nav__button--secondary" type="submit">
                        Log out
                    </button>
                </form>
            </li>
        @endauth
    </ul>
</nav>
