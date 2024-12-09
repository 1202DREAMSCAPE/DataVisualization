<header x-data="{ isOpen: false, dropdownOpen: false }" x-cloak class="sticky top-0 z-50 bg-darkgray py-3 px-8 flex flex-row items-center justify-between">
    <!-- Left Side: Logo -->
    <div class="flex items-center">
        <a href="/" class="">
            <img src="{{ asset('images/VizOraLight.png') }}" alt="Logo" class="h-12">
        </a>
    </div>

    <!-- Right Side: Profile Dropdown or Log In / Sign Up Buttons -->
    <div class="flex items-center space-x-4">
        @auth
        <!-- Profile Picture and Username with Dropdown -->
        <div class="relative" @click.away="dropdownOpen = false">
            <div class="flex items-center cursor-pointer" @click="dropdownOpen = !dropdownOpen">
                <!-- Profile Picture -->
                <img src="{{ asset(auth()->user()->profile_picture) }}" alt="Profile Picture" class="h-10 w-10 rounded-full border border-gray-300">
                <!-- Username -->
                <span class="text-white font-dmSerif text-lg ml-2">{{ auth()->user()->username }}</span>
                <!-- Dropdown Caret -->
                <svg class="ml-2 w-4 h-4 text-white transform" :class="{'rotate-180': dropdownOpen}" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
            </div>
            <!-- Dropdown Menu -->
            <div x-show="dropdownOpen" x-transition:enter="transition transform ease-out duration-300"
                x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="transition transform ease-in duration-200"
                x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                class="absolute right-0 mt-2 bg-white rounded-lg shadow-lg w-40 py-2 z-50">
                <!-- Edit Profile -->
                <a href="/profile" class="block px-4 py-2 text-gray-800 hover:bg-gray-100">Edit Profile</a>
                <!-- Log Out -->
                <a href="{{ route('logout') }}" class="block px-4 py-2 text-gray-800 hover:bg-gray-100">
                    Log Out
                </a>
            </div>
        </div>
        @else
        <!-- Log In Button -->
        <a href="/login">
            <button class="bg-green-400 border border-white text-white px-4 py-1 rounded-lg hover:bg-darkgray transition font-bold font-dmSerif text-sm md:text-base">
                Log In
            </button>
        </a>
        <!-- Sign Up Button -->
        <a href="/signup">
            <button class="bg-pink-400 border border-white text-white px-4 py-1 rounded-lg hover:bg-darkgray hover:border-black transition font-dmSerif font-bold text-sm md:text-base">
                Sign Up
            </button>
        </a>
        @endauth
    </div>
</header>
