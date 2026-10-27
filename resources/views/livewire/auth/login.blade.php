<x-layout :title="__('Log in')" :description="__('Log in to your account')" :breadcrumbs="[['label' => __('Log in'), 'url' => null]]">
    <div class="login">
        <div class="login__container">
            <header class="login__header">
                <h1 class="login__title">{{ __('Log in to your account') }}</h1>
                <p class="login__description">
                    {{ __('Enter your email and password below to log in') }}
                </p>
            </header>

            @if (session('status'))
                <p class="login__status" role="status">
                    {{ session('status') }}
                </p>
            @endif

            <form method="POST" action="{{ route('login.store') }}" class="login__form">
                @csrf

                <div class="login__field">
                    <label for="email" class="login__label">
                        {{ __('Email address') }}
                    </label>

                    <input
                        id="email"
                        class="login__input"
                        name="email"
                        type="email"
                        value="{{ old('email') }}"
                        placeholder="email@example.com"
                        autocomplete="email"
                        required
                        autofocus
                        @error('email')
                            aria-invalid="true"
                            aria-describedby="email-error"
                        @enderror
                    >

                    @error('email')
                        <p id="email-error" class="login__error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="login__field">
                    <label for="password" class="login__label">
                        {{ __('Password') }}
                    </label>

                    <input
                        id="password"
                        class="login__input"
                        name="password"
                        type="password"
                        placeholder="{{ __('Password') }}"
                        autocomplete="current-password"
                        required
                        @error('password')
                            aria-invalid="true"
                            aria-describedby="password-error"
                        @enderror
                    >

                    @error('password')
                        <p id="password-error" class="login__error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="login__options">
                    <label class="login__remember">
                        <input
                            class="login__checkbox"
                            type="checkbox"
                            name="remember"
                            value="1"
                            @checked(old('remember'))
                        >
                        <span>{{ __('Remember me') }}</span>
                    </label>

                    @if (Route::has('password.request'))
                        <a class="login__link" href="{{ route('password.request') }}">
                            {{ __('Forgot your password?') }}
                        </a>
                    @endif
                </div>

                <button type="submit" class="login__submit" data-test="login-button">
                    {{ __('Log in') }}
                </button>
            </form>

            @if (Route::has('register'))
                <p class="login__register">
                    <span>{{ __("Don't have an account?") }}</span>
                    <a class="login__link" href="{{ route('register') }}">
                        {{ __('Sign up') }}
                    </a>
                </p>
            @endif
        </div>
    </div>
</x-layout>
