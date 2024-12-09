<header x-data="{ isOpen: false }" x-cloak class="sticky top-0 z-50 bg-darkgray py-3 px-8 flex flex-row items-center justify-between">
    <!-- Left Side: Logo and Navigation Links / Hamburger -->
    <div class="flex items-center">
        <a href="/" class="">
            <img src="{{ asset('images/VizOraLight.png') }}" alt="Logo" class="h-12">
        </a>
        <!-- Navigation Links for large screens -->
        <nav class="hidden md:flex space-x-8 ml-8">
            <a href="/" class="text-white no-underline font-dmSerif text-lg mt-2">Home</a>
            <a href="/" class="text-white no-underline font-dmSerif text-lg mt-2">Features</a>
            <a href="/" class="text-white no-underline font-dmSerif text-lg mt-2">About Us</a>

        </nav>
    </div>

    <!-- Right Side: Log In and Sign Up Buttons and Hamburger Icon -->
    <div class="flex items-center space-x-4">
        <!-- Log In Button -->
        <a href="/login">
            <button class="bg-green-400 border border-white text-white px-4 py-1 rounded-lg hover:bg-darkgray transition font-bold font-dmSerif text-sm md:text-base">
                Log In
            </button>
        </a>
        <!-- Sign Up Button -->
        <a href="/register">
            <button class="bg-pink-400 border border-white text-white px-4 py-1 rounded-lg hover:bg-darkgray hover:border-black transition font-dmSerif font-bold text-sm md:text-base">
                Sign Up
            </button>
        </a>
        <!-- Hamburger Icon for mobile -->
        <button @click="isOpen = true" class="md:hidden mt-2 focus:outline-none">
            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
            </svg>
        </button>
    </div>

    <!-- Side Drawer for Mobile -->
    <div x-show="isOpen" x-transition:enter="transition transform ease-out duration-300"
        x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0"
        x-transition:leave="transition transform ease-in duration-300"
        x-transition:leave-start="translate-x-0" x-transition:leave-end="translate-x-full"
        class="fixed inset-y-0 right-0 bg-darkgray w-1/3 max-w-xs shadow-lg z-50 flex flex-col px-6 py-4">
        <!-- Close Button -->
        <button @click="isOpen = false" class="absolute top-4 right-4 text-gray-600 hover:text-gray-800">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
        <!-- Navigation Links -->
        <nav class="mt-12 flex flex-col space-y-6">
            <a href="/" class="text-white no-underline text-lg font-dmSerif">Home</a>
            <a href="/" class="text-white no-underline text-lg font-dmSerif">Features</a>
            <a href="/" class="text-white no-underline text-lg font-dmSerif">About us</a>
        </nav>
    </div>

    <!-- Background Overlay -->
    <div x-show="isOpen" @click="isOpen = false" x-cloak
        class="fixed inset-0 bg-black bg-opacity-50 z-40 transition-opacity duration-300"></div>
</header>
