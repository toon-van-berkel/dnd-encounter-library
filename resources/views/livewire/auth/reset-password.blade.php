<x-layout :title="__('Reset password')" :description="__('Set a new password')" :breadcrumbs="[['label' => __('Reset password'), 'url' => null]]">
    <div class="login">
        <div class="login__container">
            <header class="login__header">
                <h1 class="login__title">{{ __('Reset password') }}</h1>
                <p class="login__description">
                    {{ __('Please enter your new password below') }}
                </p>
            </header>

            @if (session('status'))
                <p class="login__status" role="status">
                    {{ session('status') }}
                </p>
            @endif

            <form method="POST" action="{{ route('password.update') }}" class="login__form">
                @csrf

                <input type="hidden" name="token" value="{{ request()->route('token') }}">

                <div class="login__field">
                    <label for="email" class="login__label">
                        {{ __('Email address') }}
                    </label>

                    <input
                        id="email"
                        class="login__input"
                        name="email"
                        type="email"
                        value="{{ old('email', request('email')) }}"
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
                    data-test="reset-password-button"
                >
                    {{ __('Reset password') }}
                </button>
            </form>
        </div>
    </div>
</x-layout>
