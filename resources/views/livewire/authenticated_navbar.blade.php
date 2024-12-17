<header x-data="{ isOpen: false, dropdownOpen: false }" x-cloak class="sticky top-0 z-50 bg-darkgray py-3 px-8 flex flex-row items-center justify-between">
    <!-- Left Side: Logo and Profile -->
    <div class="flex items-center space-x-4">
        <!-- Logo -->
        <a href="/" class="">
            <img src="{{ asset('images/VizOraLight.png') }}" alt="Logo" class="h-12">
        </a>
        @auth
        <!-- Profile Picture and Username -->
        <div class="flex items-center space-x-2">
            <a href="/profile" class="font-dmSerif font-bold text-lg bg-gradient-to-r from-indigo-400 via-white to-green-300 bg-[length:200%_200%] bg-[position:0%_50%] animate-gradient bg-clip-text text-transparent">
                {{ auth()->user()->username }}
            </a>
        </div>
        @endauth
    </div>

    <!-- Right Side: Buttons and Dropdown -->
    <div class="flex items-center space-x-4">
        @auth
        <!-- Buttons (Hidden in Mobile View) -->
        <div class="hidden md:flex space-x-4">
            
            <a href="/project">
                <button class="relative text-white rounded-lg shadow-lg font-bold px-4 py-1 font-dmSerif text-sm md:text-base hover:scale-105 transition-transform bg-gradient-to-r from-[#4c51bf] via-[#3b82f6] to-[#10b981] bg-[length:200%_200%] bg-[position:0%_50%] animate-gradient">
                    Upload New
                </button>
            </a>
            <a href="{{ ('savegenreports')}}">
                <button class="relative text-white rounded-lg shadow-lg font-bold px-4 py-1 font-dmSerif text-sm md:text-base hover:scale-105 transition-transform bg-gradient-to-r from-[#4c51bf] via-[#3b82f6] to-[#10b981] bg-[length:200%_200%] bg-[position:0%_50%] animate-gradient">
                    Generated Reports
                </button>
            </a>
            <!-- <a href="/clean-csv/upload">
                <button class="relative text-white rounded-lg shadow-lg font-bold px-4 py-1 font-dmSerif text-sm md:text-base hover:scale-105 transition-transform bg-gradient-to-r from-[#4c51bf] via-[#3b82f6] to-[#10b981] bg-[length:200%_200%] bg-[position:0%_50%] animate-gradient">
                    Clean
                </button>
            </a> -->
            <!-- <a href="/profile">
                <button class="relative text-white rounded-lg shadow-lg font-bold px-4 py-1 font-dmSerif text-sm md:text-base hover:scale-105 transition-transform bg-gradient-to-r from-[#4c51bf] via-[#3b82f6] to-[#10b981] bg-[length:200%_200%] bg-[position:0%_50%] animate-gradient">
                    Profile
                </button>
            </a> -->
            <a href="{{ route('logout') }}">
                <button class="bg-white border-darkgray border-dashed border-1 text-black font-bold px-4 py-1 rounded-lg hover:bg-gray-600 transition font-dmSerif text-sm md:text-base">
                    Log Out
                </button>
            </a>
        </div>

        <!-- Dropdown for Mobile View -->
        <div class="relative md:hidden" @click.away="dropdownOpen = false">
            <div class="flex items-center cursor-pointer" @click="dropdownOpen = !dropdownOpen">
                <!-- Dropdown Icon -->
                <svg class="w-6 h-6 text-white transform" :class="{'rotate-180': dropdownOpen}" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
            </div>
            <!-- Dropdown Menu -->
            <div x-show="dropdownOpen" x-transition:enter="transition transform ease-out duration-300"
                x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="transition transform ease-in duration-200"
                x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                class="absolute right-0 mt-2 bg-white rounded-lg shadow-lg w-40 py-2 z-50">
                <a href="/profile" class="block px-4 py-2 no-underline  text-darkgray hover:bg-gray-100">Edit Profile</a>
                <a href="/project" class="block px-4 py-2 no-underline text-darkgray hover:bg-gray-100">Upload New</a>
                <a href="/saved-charts" class="block px-4 py-2 no-underline text-darkgray hover:bg-gray-100">Charts</a>
                <a href="/clean-csv/upload" class="block px-4 py-2 no-underline text-darkgray hover:bg-gray-100">Clean</a>
                <a href="{{ route('logout') }}" class="block px-4 py-2 no-underline text-darkgray hover:bg-gray-100">Log Out</a>
            </div>
        </div>
        @else
        <!-- Log In and Sign Up Buttons (Hidden in Mobile View) -->
        <div class="hidden md:flex space-x-4">
            <a href="/login">
                <button class="relative text-white rounded-lg shadow-lg px-4 py-1 font-bold font-dmSerif text-sm md:text-base hover:scale-105 transition-transform bg-gradient-to-br from-indigo-600 via-blue-600 to-green-500 bg-[length:200%_200%] bg-[position:0%_50%] animate-gradient">
                    Log In
                </button>
            </a>
            <a href="/signup">
                <button class="relative text-white rounded-lg shadow-lg px-4 py-1 font-bold font-dmSerif text-sm md:text-base hover:scale-105 transition-transform bg-gradient-to-br from-purple-600 via-pink-600 to-red-500 bg-[length:200%_200%] bg-[position:0%_50%] animate-gradient">
                    Sign Up
                </button>
            </a>
        </div>
        @endauth
    </div>
</header>
