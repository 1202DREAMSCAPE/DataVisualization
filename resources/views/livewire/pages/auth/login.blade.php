<?php

use App\Livewire\Forms\LoginForm;
use Illuminate\Support\Facades\Session;
use Livewire\Volt\Component;

new class extends Component
{
    public LoginForm $form;

    /**
     * Handle an incoming authentication request.
     */
    public function login(): void
    {
        $this->validate();

        $this->form->authenticate();

        Session::regenerate();

        $this->redirectIntended(default: route('dashboard', absolute: false), navigate: true);
    }

    /**
     * Render the component view.
     */
    public function render(): mixed
    {
        return <<<'blade'
        <div class="bg-gradient-to-b from-orange-300 to-yellow-200 min-h-screen flex items-center justify-center">
            <div class="bg-white p-8 rounded-lg shadow-lg max-w-md w-full">
                <h1 class="text-2xl font-bold text-center mb-6">Hello!</h1>
                <p class="text-center mb-4">Log In To Your Account</p>

                <!-- Session Status -->
                @if (session('status'))
                    <div class="text-green-500 text-center mb-4">{{ session('status') }}</div>
                @endif

                <form wire:submit="login">
                    <!-- Username -->
                    <div class="mb-4">
                        <label for="username" class="block font-medium text-gray-700">Username</label>
                        <input 
                            wire:model="form.username" 
                            id="username" 
                            type="text" 
                            placeholder="Username" 
                            class="w-full border-gray-300 rounded-lg px-4 py-2"
                        />
                        @error('form.username') 
                            <span class="text-red-500 text-sm">{{ $message }}</span> 
                        @enderror
                    </div>

                    <!-- Password -->
                    <div class="mb-4">
                        <label for="password" class="block font-medium text-gray-700">Password</label>
                        <input 
                            wire:model="form.password" 
                            id="password" 
                            type="password" 
                            placeholder="Password" 
                            class="w-full border-gray-300 rounded-lg px-4 py-2"
                        />
                        @error('form.password') 
                            <span class="text-red-500 text-sm">{{ $message }}</span> 
                        @enderror
                    </div>

                    <!-- Remember Me -->
                    <div class="flex items-center justify-between mb-4">
                        <label class="flex items-center">
                            <input 
                                wire:model="form.remember" 
                                id="remember" 
                                type="checkbox" 
                                class="form-checkbox"
                            />
                            <span class="ml-2 text-gray-600">Remember Me</span>
                        </label>
                        <a href="{{ route('password.request') }}" class="text-orange-600 hover:underline">Forgot password?</a>
                    </div>

                    <!-- Login Button -->
                    <button
                        type="submit"
                        class="bg-orange-500 text-white w-full py-2 rounded-lg hover:bg-orange-600"
                    >
                        Login
                    </button>
                </form>

                <p class="mt-4 text-center">
                    Don’t have an account yet? <a href="{{ route('register') }}" class="text-orange-600 hover:underline">Sign Up Now!</a>
                </p>
            </div>
        </div>
        blade;
    }
};
