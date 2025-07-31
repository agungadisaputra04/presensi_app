<x-guest-layout>
  <x-authentication-card>
    <x-slot name="logo">
      <style>
        @keyframes fadeInUp {
          0% {
            opacity: 0;
            transform: translateY(20px) scale(0.98);
          }
          100% {
            opacity: 1;
            transform: translateY(0) scale(1);
           

          }
        }
      </style>
    
      <div class="text-center animate-fade  animation: fadeInUp 0.8s ease-out 0.8s forwards;" style="animation: fadeInUp 0.8s ease-out forwards;">
        <img src="{{ asset('assets/agung/logo-S247.png') }}"
     alt="Logo"
     class="w-40 h-20 mx-auto transition-transform duration-300 hover:scale-105" />

        <p class="mt-2 text-lg font-semibold text-gray-800 dark:text-gray-200">
          {{ config('app.name') }}
        </p>
      </div>
    </x-slot>
    
    
    
    <x-validation-errors class="mb-4" />

    @session('status')
    <div class="mb-4 text-sm font-medium text-green-600 dark:text-green-400">
      {{ $value }}
    </div>
    @endsession

    <form method="POST" action="{{ route('login') }}">
      @csrf

      <div>
        <x-label for="email" value="{{ __('Email or Phone') }}" />
        <x-input id="email" class="mt-1 block w-full" type="text" name="email" :value="old('email')" required
        autofocus autocomplete="username" />
      </div>

      <div class="mt-4">
        <x-label for="password" value="{{ __('Password') }}" />
        <x-input id="password" class="mt-1 block w-full" type="password" name="password" required
        autocomplete="current-password" />
      </div>

      <div class="mt-4 block">
        <label for="remember_me" class="flex items-center">
          <x-checkbox id="remember_me" name="remember" checked />
          <span class="ms-2 text-sm text-gray-600 dark:text-gray-400">{{ __('Remember me') }}</span>
        </label>
      </div>

      <div class="mb-3 mt-4 flex items-center justify-end">
        {{-- <a href="{{ route('register') }}">
          <x-secondary-button class="ms-4" type="button">
            {{ __('Register') }}
          </x-secondary-button>
        </a> --}}

        <x-button class="ms-4">
          {{ __('Log in') }}
        </x-button>
      </div>
    </form>

    @if (Route::has('password.request'))
    <a class="rounded-md text-sm text-gray-600 underline hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:text-gray-400 dark:hover:text-gray-100 dark:focus:ring-offset-gray-800"
    href="{{ route('password.request') }}">

  </a>
  @endif
  <hr>
  {{-- Include footer --}}
  @include('layouts.footer')
</x-authentication-card>
</x-guest-layout>
