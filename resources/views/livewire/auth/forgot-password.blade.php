<x-layout :title="__('Forgot password')" :description="__('Reset your password')" :breadcrumbs="[['label' => __('Forgot password'), 'url' => null]]">
    <div class="login">
        <div class="login__container">
            <header class="login__header">
                <h1 class="login__title">{{ __('Forgot password') }}</h1>
                <p class="login__description">
                    {{ __('Enter your email to receive a password reset link') }}
                </p>
            </header>

            @if (session('status'))
                <p class="login__status" role="status">
                    {{ session('status') }}
                </p>
            @endif

            <form method="POST" action="{{ route('password.email') }}" class="login__form">
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

                <button
                    type="submit"
                    class="login__submit"
                    data-test="email-password-reset-link-button"
                >
                    {{ __('Email password reset link') }}
                </button>
            </form>

            <p class="login__register">
                <span>{{ __('Or, return to') }}</span>
                <a class="login__link" href="{{ route('login') }}">
                    {{ __('log in') }}
                </a>
            </p>
        </div>
    </div>
</x-layout>
