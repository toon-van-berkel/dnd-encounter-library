<x-layout :title="__('Register')" :description="__('Create an account')" :breadcrumbs="[['label' => __('Register'), 'url' => null]]">
    <div class="login">
        <div class="login__container">
            <header class="login__header">
                <h1 class="login__title">{{ __('Create an account') }}</h1>
                <p class="login__description">
                    {{ __('Enter your details below to create your account') }}
                </p>
            </header>

            @if (session('status'))
                <p class="login__status" role="status">
                    {{ session('status') }}
                </p>
            @endif

            <form method="POST" action="{{ route('register.store') }}" class="login__form">
                @csrf

                <div class="login__field">
                    <label for="name" class="login__label">
                        {{ __('Name') }}
                    </label>

                    <input
                        id="name"
                        class="login__input"
                        name="name"
                        type="text"
                        value="{{ old('name') }}"
                        placeholder="{{ __('Full name') }}"
                        autocomplete="name"
                        required
                        autofocus
                        @error('name')
                            aria-invalid="true"
                            aria-describedby="name-error"
                        @enderror
                    >

                    @error('name')
                        <p id="name-error" class="login__error">{{ $message }}</p>
                    @enderror
                </div>

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
                        autocomplete="new-password"
                        passwordrules="{{ \Illuminate\Validation\Rules\Password::defaults()->toPasswordRulesString() }}"
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

                <div class="login__field">
                    <label for="password_confirmation" class="login__label">
                        {{ __('Confirm password') }}
                    </label>

                    <input
                        id="password_confirmation"
                        class="login__input"
                        name="password_confirmation"
                        type="password"
                        placeholder="{{ __('Confirm password') }}"
                        autocomplete="new-password"
                        passwordrules="{{ \Illuminate\Validation\Rules\Password::defaults()->toPasswordRulesString() }}"
                        required
                        @error('password_confirmation')
                            aria-invalid="true"
                            aria-describedby="password-confirmation-error"
                        @enderror
                    >

                    @error('password_confirmation')
                        <p id="password-confirmation-error" class="login__error">
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <button
                    type="submit"
                    class="login__submit"
                    data-test="register-user-button"
                >
                    {{ __('Create account') }}
                </button>
            </form>

            <p class="login__register">
                <span>{{ __('Already have an account?') }}</span>
                <a class="login__link" href="{{ route('login') }}">
                    {{ __('Log in') }}
                </a>
            </p>
        </div>
    </div>
</x-layout>
