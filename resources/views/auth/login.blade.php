<x-guest-layout>
    <x-auth-card>
      

        <!-- Session Status -->
        <x-auth-session-status class="mb-4" :status="session('status')" />
        <x-auth-session-status class="mb-4" :status="session('message')" />
        <!-- Validation Errors -->
        <x-auth-validation-errors class="mb-4" :errors="$errors" />

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <!-- Email Address -->
            <div>
                <x-label for="Username" :value="__('Username')" />

                <x-input id="Username" class="block mt-1 w-full" type="text" name="username" :value="old('Username')" required autofocus />
            </div>

            <!-- Password -->
            <div class="mt-4">
                <x-label for="password" :value="__('Password')" />

                <x-input id="password" class="block mt-1 w-full"
                                type="password"
                                name="password"
                                required autocomplete="current-password" />
            </div>

            <!-- Remember Me -->
         

            <div class="flex items-center justify-end mt-4">
                @if (Route::has('password.request'))
                    <a class="underline text-sm text-gray-600 hover:text-gray-900" href="{{ route('forget.password.get') }}">
                        {{ __('Forgot your password?') }}
                    </a>
                    
                @endif


                <x-button class="ml-3">
                    {{ __('Log in') }}
                </x-button>
            </div>
        </form>
    </x-auth-card>
</x-guest-layout>
