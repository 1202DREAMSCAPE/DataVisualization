<nav class="sticky top-0 z-50 bg-lightgray px-8 py-3 flex justify-between items-center shadow-md font-dmSerif">
    <div class="flex items-center">
    <a href="/" class="flex-shrink-0">
            <img src="{{ asset('images/Logo.png') }}" alt="Logo" class="w-13 h-12 object-cover">
        </a>     
        <!--href="{{ url('/') }}"-->   
        <ul class="hidden md:flex space-x-8 ml-6 text-black font-medium">
            <li><a href="#" class="hover:text-black">Home</a></li>
            <li><a href="#" class="hover:text-black">About Us</a></li>
            <li><a href="#" class="hover:text-black">Features</a></li>
        </ul>
        <!-- Mobile Menu -->
        <div class="md:hidden" x-data="{ open: false }">
            <button @click="open = !open" class="text-black text-xl">
                XX
            </button>
            <ul x-show="open" class="absolute bg-white p-4 space-y-2 shadow-md right-4 top-16 rounded-lg">
                <li><a href="#" class="hover:text-black">Home</a></li>
                <li><a href="#" class="hover:text-black">About Us</a></li>
                <li><a href="#" class="hover:text-black">Features</a></li>
            </ul>
        </div>
    </div>
    <div class="flex items-center space-x-4">
    <!-- Log In Button -->
    <a href="{{ route('login') }}">
        <button class="bg-yellow-500 border border-black text-black px-6 py-1 rounded-lg hover:bg-yellow-400 hover:text-black transition font-bold">
            Log In
        </button>
    </a>
    <!-- Sign Up Button -->
    <a href="{{ route('register') }}">
        <button class="bg-lightgray border border-black text-black px-6 py-1 rounded-lg hover:bg-medgray hover:text-black hover:border-black transition font-bold">
            Sign Up
        </button>
    </a>

    
</div>

</nav>
