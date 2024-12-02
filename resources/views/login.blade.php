<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DataBar - Data Visualization Platform</title>
    @vite('resources/css/app.css')
    @livewireStyles
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=DM+Serif+Display&display=swap" rel="stylesheet">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.3/dist/cdn.min.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>

<body>
    <!-- Single Root Element -->
    <div class="min-h-screen flex flex-col bg-gradient-to-b from-orange-300 to-yellow-200">
        <!-- Header with Alpine.js for responsive menu -->
        <header x-data="{ isOpen: false }" class="bg-darkgray py-4 px-6 flex flex-row items-center justify-between">
            <!-- Left Side: Logo and Navigation Links / Hamburger -->
            <div class="flex items-center">
                <a href="/" class="">
                    <img src="{{ asset('images/Logo.png') }}" alt="DataBar Logo" class="ml-4 h-12">
                </a>
                <!-- Navigation Links for large screens -->
                <nav class="hidden md:flex space-x-8 ml-8">
                    <a href="/" class="text-white no-underline font-dmSerif text-lg mt-2">Home</a>
                    <a href="/" class="text-white no-underline font-dmSerif text-lg mt-2">About us</a>
                    <a href="/" class="text-white no-underline font-dmSerif text-lg mt-2">Features</a>
                </nav>
            </div>

            <!-- Right Side: Log In and Sign Up Buttons and Hamburger Icon -->
            <div class="flex items-center space-x-4">
                <!-- Log In Button -->
                <a href="/login">
                    <button
                        class="bg-yellow-500 border border-black text-white px-4 py-1 rounded-lg hover:bg-yellow-400 hover:text-black transition font-bold text-sm md:text-base">
                        Log In
                    </button>
                </a>
                <!-- Sign Up Button -->
                <a href="/register">
                    <button
                        class="bg-darkgray border border-white text-white px-4 py-1 rounded-lg hover:bg-medgray hover:text-black hover:border-black transition font-bold text-sm md:text-base">
                        Sign Up
                    </button>
                </a>
                <!-- Hamburger Icon for mobile -->
                <button @click="isOpen = true" class="md:hidden mt-2 focus:outline-none">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                        xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                </button>
            </div>

            <!-- Mobile Menu Modal -->
            <div x-show="isOpen" @click.away="isOpen = false"
                class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
                <div @click.stop class="bg-white rounded-lg w-3/4 max-w-sm p-6 relative">
                    <!-- Close Button -->
                    <button @click="isOpen = false" class="absolute top-2 right-2 text-gray-600 hover:text-gray-800">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                            xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                    <!-- Navigation Links -->
                    <nav class="flex flex-col space-y-4 mt-4 ">
                        <a href="/" class="text-gray-800 no-underline text-lg  font-dmSerif">Home</a>
                        <a href="/" class="text-gray-800 no-underline text-lg font-dmSerif">About us</a>
                        <a href="/" class="text-gray-800 no-underline text-lg font-dmSerif">Features</a>
                    </nav>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <div class="flex items-center justify-center flex-grow px-4">
            <div class="bg-white rounded-[40px] shadow-lg max-w-md w-full py-6 px-4 xl:py-12 xl:px-12">
                <h1 class="text-xl sm:text-2xl font-bold text-darkgray text-center mb-1 font-serif">Hello!</h1>
                <p class="text-center text-xs sm:text-sm text-darkgray mb-2 italic">Log In To Your Account</p>

                <!-- Session Status -->
                @if (session('status'))
                    <div class="text-green-500 text-center mb-4">{{ session('status') }}</div>
                @endif

                <form wire:submit.prevent="login">
                    <!-- Username -->
                    <div class="mb-2 flex items-center justify-center">
                        <input wire:model="form.username" id="username" type="text" placeholder="Username"
                            class="w-3/4 border-[1px] border-darkgray rounded-lg px-3 py-2 text-xs sm:text-sm focus:outline-none focus:ring focus:ring-orange-300" />
                        @error('form.username')
                            <span class="text-red-500 text-xs">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Password -->
                    <div class="mb-3 flex items-center justify-center"">
                        <input wire:model="form.password" id="password" type="password" placeholder="Password"
                            class="w-3/4  border-[1px] border-darkgray rounded-lg px-3 py-2 text-xs sm:text-sm focus:outline-none focus:ring focus:ring-orange-300" />
                        @error('form.password')
                            <span class="text-red-500 text-xs">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class = "w-full items-center justify-center ">
                        <div class="flex items-center justify-between px-12 mt-4 mb-6 text-xs sm:text-sm">
                            <label class="flex items-center">
                                <input wire:model="form.remember" id="remember" type="checkbox"
                                    class="form-checkbox h-4 w-4 text-orange-600">
                                <span class="ml-2 text-darkgray">Remember Me</span>
                            </label>
                            <a href="{{ route('password.request') }}"
                                class="text-orange no-underline hover:underline">Forgot password?</a>
                        </div>
    
                    </div>

                    <!-- Login Button -->
                    <div class="flex justify-center">
                        <button type="submit"
                            class="bg-warmyellow font-bold border-[1px] border-darkgray text-darkgray w-3/4 sm:w-1/2 py-2 rounded-full hover:bg-lightyellow hover:text-white hover:font-bold text-xs sm:text-sm">
                            Login
                        </button>
                    </div>
                </form>

                <p class="mt-6 text-center text-xs sm:text-sm">
                    Don’t have an account yet? <a href="{{ route('register') }}"
                        class="text-darkgray italic font-bold hover:underline">Sign Up Now!</a>
                </p>
            </div>
        </div>
    </div>

    @livewireScripts
</body>

</html>
