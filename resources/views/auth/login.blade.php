<x-guest-layout>

    <div class="w-full sm:max-w-md mx-auto">

        {{-- HEADER CARD --}}
        <div class="bg-blue-600 rounded-t-lg py-8 px-6 text-center">
            <x-application-logo class="h-32 mx-auto mb-3" />

            <h1 class="text-black text-lg font-semibold leading-tight">
                Sistem Stock Opname
            </h1>
            <h1 class="text-blue-100 text-md">
                Konveksi
            </h1>
        </div>

        {{-- BODY CARD --}}
        <div class="bg-white shadow-md rounded-b-lg px-6 py-6">

            <!-- Session Status -->
            <x-auth-session-status class="mb-4" :status="session('status')" />

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <!-- Email -->
                <div>
                    <x-input-label for="email" :value="__('Email')" />
                    <x-text-input
                        id="email"
                        class="block mt-1 w-full"
                        type="email"
                        name="email"
                        :value="old('email')"
                        required
                        autofocus
                        autocomplete="username"
                    />
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>

                <!-- Password -->
                <div class="mt-4">
                    <x-input-label for="password" :value="__('Password')" />
                    <x-text-input
                        id="password"
                        class="block mt-1 w-full"
                        type="password"
                        name="password"
                        required
                        autocomplete="current-password"
                    />
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>

                <!-- Remember -->
                <div class="block mt-4">
                    <label class="inline-flex items-center">
                        <input type="checkbox"
                               name="remember"
                               class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                        <span class="ms-2 text-sm text-gray-600">
                            {{ __('Remember me') }}
                        </span>
                    </label>
                </div>

                <div class="flex items-center justify-between mt-6">
                    @if (Route::has('password.request'))
                        <a class="text-sm text-gray-600 hover:text-gray-900 underline"
                           href="{{ route('password.request') }}">
                            {{ __('Forgot your password?') }}
                        </a>
                    @endif

                    <x-primary-button>
                        {{ __('Log in') }}
                    </x-primary-button>
                </div>

            </form>
        </div>
    </div>

</x-guest-layout>