<?php

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Livewire\Volt\Component;

new class extends Component
{
    public string $name = '';
    public string $email = '';
    public string $username = '';
    public string $password = '';
    public string $password_confirmation = '';

    /**
     * Handle an incoming registration request.
     */
    public function register(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'username' => ['required', 'string', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
        ]);

        $validated['password'] = Hash::make($validated['password']);

        event(new Registered($user = User::create($validated)));

        Auth::login($user);

        $this->redirect(route('dashboard', absolute: false), navigate: true);
    }

    /**
     * Render the component view.
     */
    public function render(): mixed
    {
        return <<<'blade'
        <div class="bg-gradient-to-b from-orange-300 to-yellow-200 min-h-screen flex items-center justify-center">
            <div class="bg-white p-8 rounded-lg shadow-lg max-w-md w-full">
                <h1 class="text-2xl font-bold text-center mb-2">Get Started</h1>
                <p class="text-center mb-6">Sign Up for <span class="font-bold">Free.</span></p>

                <form wire:submit="register">
                    <!-- Name -->
                    <div class="mb-4">
                        <label for="name" class="block font-medium text-gray-700">Name</label>
                        <input
                            wire:model="name"
                            id="name"
                            type="text"
                            placeholder="Name"
                            class="w-full border-gray-300 rounded-lg px-4 py-2"
                            required
                        />
                        @error('name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <!-- Email -->
                    <div class="mb-4">
                        <label for="email" class="block font-medium text-gray-700">Email Address</label>
                        <input
                            wire:model="email"
                            id="email"
                            type="email"
                            placeholder="Email Address (e.g., example@gmail.com)"
                            class="w-full border-gray-300 rounded-lg px-4 py-2"
                            required
                        />
                        @error('email') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <!-- Username -->
                    <div class="mb-4">
                        <label for="username" class="block font-medium text-gray-700">Username</label>
                        <input
                            wire:model="username"
                            id="username"
                            type="text"
                            placeholder="Username"
                            class="w-full border-gray-300 rounded-lg px-4 py-2"
                            required
                        />
                        @error('username') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <!-- Password -->
                    <div class="mb-4">
                        <label for="password" class="block font-medium text-gray-700">Password</label>
                        <input
                            wire:model="password"
                            id="password"
                            type="password"
                            placeholder="Password"
                            class="w-full border-gray-300 rounded-lg px-4 py-2"
                            required
                        />
                        @error('password') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <!-- Confirm Password -->
                    <div class="mb-4">
                        <label for="password_confirmation" class="block font-medium text-gray-700">Confirm Password</label>
                        <input
                            wire:model="password_confirmation"
                            id="password_confirmation"
                            type="password"
                            placeholder="Confirm Password"
                            class="w-full border-gray-300 rounded-lg px-4 py-2"
                            required
                        />
                        @error('password_confirmation') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <!-- Terms and Privacy -->
                    <div class="mb-4">
                        <label class="inline-flex items-center">
                            <input type="checkbox" required class="form-checkbox border-gray-300 rounded">
                            <span class="ml-2 text-sm text-gray-600">
                                By signing up, you confirm in <a href="#" class="text-orange-600 hover:underline">data privacy</a> secures your account by ensuring only authorized access to your data.
                            </span>
                        </label>
                    </div>

                    <!-- Register Button -->
                    <button
                        type="submit"
                        class="bg-orange-500 text-white w-full py-2 rounded-lg hover:bg-orange-600"
                    >
                        Enter DataBar
                    </button>
                </form>

                <p class="mt-4 text-center">
                    Already have an account? <a href="{{ route('login') }}" class="text-orange-600 hover:underline">Log in</a>.
                </p>
            </div>
        </div>
        blade;
    }
};
