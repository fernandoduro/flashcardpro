<?php

use App\Livewire\Forms\LoginForm;
use App\Providers\RouteServiceProvider;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public LoginForm $form;

    public function login(): void
    {
        $this->validate();

        $this->form->authenticate();

        Session::regenerate();

        $this->redirectIntended(default: route('decks.index', absolute: false), navigate: true);
    }
}; ?>

<div>
    {{-- Session Status --}}
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <h2 class="text-2xl font-bold text-center text-gray-800 mb-2">
        Welcome Back
    </h2>
    <p class="text-sm text-center text-gray-500 mb-8">
        Don't have an account?
        <a href="{{ route('register') }}" wire:navigate class="font-medium text-primary-600 hover:text-primary-500">
            Sign up
        </a>
    </p>

    <form wire:submit="login" class="space-y-6">
        <!-- Email Address -->
        <div>
            <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
            <div class="mt-1">
                <input wire:model="form.email" id="email" class="block w-full border-gray-300 rounded-md shadow-sm focus:border-primary-500 focus:ring-primary-500" type="email" name="email" required autofocus autocomplete="username" />
            </div>
            <x-input-error :messages="$errors->get('form.email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div>
            <div class="flex items-center justify-between">
                <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                <div class="text-sm">
                    <a href="{{ route('password.request') }}" wire:navigate class="font-medium text-primary-600 hover:text-primary-500">
                        Forgot password?
                    </a>
                </div>
            </div>
            <div class="mt-1">
                <input wire:model="form.password" id="password" class="block w-full border-gray-300 rounded-md shadow-sm focus:border-primary-500 focus:ring-primary-500" type="password" name="password" required autocomplete="current-password" />
            </div>
             <x-input-error :messages="$errors->get('form.password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="flex items-center">
            <input id="remember" type="checkbox" wire:model="form.remember" class="h-4 w-4 rounded border-gray-300 text-primary-600 focus:ring-primary-500" />
            <label for="remember" class="ms-2 block text-sm text-gray-600">
                Remember me
            </label>
        </div>

        <div>
            <button type="submit" class="w-full flex justify-center py-2.5 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-colors">
                Sign in
            </button>
        </div>
    </form>
</div>