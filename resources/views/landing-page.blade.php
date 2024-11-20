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
<body class="bg-lightgray text-darkgray">
    <!-- Navbar -->
    <livewire:navbar />

    <!--First Section-->
    <section 
    id="home" 
    class="relative bg-cover bg-center h-screen flex flex-col items-center justify-center"
    style="background-image: url('{{ asset('images/mainbg.gif') }}');"
>
    <!-- Blurred Overlay -->
    <div class="absolute inset-0 bg-white bg-opacity-10 backdrop-blur-sm"></div>

    <!-- Content -->
     <!-- <span class="bg-gradient-to-r from-lightyellow to-warmyellow text-transparent bg-clip-text"> -->
    <div class="relative text-center mb-12 pb-5 z-10">
    <h1 class="text-4xl md:text-6xl font-bold px-4 mx-5 drop-shadow-xl">
        <span class="text-black">
            From files to insights:
        </span>
        <span class="block font-semibold text-black text-3xl md:text-5xl">
            create personalized bar charts in just a few clicks.
        </span>
    </h1>
</div>

    
    <!-- Animated Down Arrow -->
    <button
        onclick="document.querySelector('#features').scrollIntoView({ behavior: 'smooth' })"
        class="relative text-black text-3xl animate-bounce z-10 drop-shadow-xl"
    >
        ↓
    </button>
</section>

    <!-- Features Section -->
    <section
    id="features"
    class="bg-gradient-to-b from-lightyellow to-orange h-screen flex items-center px-6 py-12"
>
    <div class="max-w-6xl mx-auto grid grid-cols-1 md:grid-cols-2 gap-12 items-center">
        <!-- Left Section: Features List -->
        <div x-data="{ activeFeature: 'chart' }">
            <h2 class="text-darkgray text-3xl mt-3 md:text-2xl sm:text-xl font-bold mb-6">
                At DataBar, we empower users to turn data into compelling visuals with ease.
            </h2>
            <ul class="space-y-8">
                <!-- Feature 1 -->
                <li>
                    <button
                        @click="activeFeature = 'chart'"
                        :class="activeFeature === 'chart' ? 'text-black underline' : 'text-darkgray'"
                        class="text-lg font-bold hover:text-black hover:underline transition"
                    >
                        ✔️ Create personalized bar charts in just a few clicks.
                    </button>
                </li>
                <!-- Feature 2 -->
                <li>
                    <button
                        @click="activeFeature = 'ai'"
                        :class="activeFeature === 'ai' ? 'text-black underline' : 'text-darkgray'"
                        class="text-lg font-bold hover:text-black hover:underline transition"
                    >
                        💡 AI integration and a user-friendly dashboard.
                    </button>
                </li>
                <!-- Feature 3 -->
                <li>
                    <button
                        @click="activeFeature = 'csv'"
                        :class="activeFeature === 'csv' ? 'text-black underline' : 'text-darkgray'"
                        class="text-lg font-bold hover:text-black hover:underline transition"
                    >
                        📂 Support for xlsx/csv files to manage projects effortlessly.
                    </button>
                </li>
            </ul>
            <p class="text-center text-darkgray mt-6 font-bold text-xl">
                Experience the future of data visualization today!
            </p>
        </div>

        <!-- Right Section: Photo Stack -->
        <div x-data="{ activeFeature: 'chart' }" class="relative w-full h-96">
            <!-- Chart Placeholder -->
            <div
                :class="activeFeature === 'chart' ? 'z-20 scale-100 opacity-100 top-0' : 'z-10 scale-90 opacity-80 top-4'"
                class="absolute left-0 w-full h-full bg-gray-300 rounded-lg shadow-lg flex items-center justify-center text-white font-bold text-xl transition-all duration-500"
            >
                Chart Placeholder
            </div>
            <!-- AI Placeholder -->
            <div
                :class="activeFeature === 'ai' ? 'z-20 scale-100 opacity-100 top-0' : 'z-10 scale-90 opacity-80 top-4'"
                class="absolute left-0 w-full h-full bg-gray-400 rounded-lg shadow-lg flex items-center justify-center text-white font-bold text-xl transition-all duration-500"
            >
                AI Placeholder
            </div>
            <!-- CSV Placeholder -->
            <div
                :class="activeFeature === 'csv' ? 'z-20 scale-100 opacity-100 top-0' : 'z-10 scale-90 opacity-80 top-8'"
                class="absolute left-0 w-full h-full bg-gray-500 rounded-lg shadow-lg flex items-center justify-center text-white font-bold text-xl transition-all duration-500"
            >
                CSV Placeholder
            </div>
        </div>
    </div>
</section>





    <!-- Additional Features Section -->
    <section
        id="additional-features"
        class="bg-gradient-to-t from-lightyellow to-orange h-screen flex items-center justify-center px-6"
    >
        <div class="max-w-6xl mx-auto text-center">
            <h2 class="text-darkgray text-3xl font-bold mb-6">Additional Features</h2>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-darkgray text-lightgray px-6 py-4">
        <div class="max-w-6xl mx-auto flex justify-between">
            <p>&copy; 2024 Mama Mo</p>
            <ul class="flex space-x-4">
                <li><a href="#" class="hover:text-orange">Privacy Policy</a></li>
                <li><a href="#" class="hover:text-orange">Terms of Service</a></li>
                <li><a href="#" class="hover:text-orange">Meet the Team</a></li>
            </ul>
        </div>
    </footer>

    <!-- Scroll to Top Button -->
    <button
        onclick="window.scrollTo({ top: 0, behavior: 'smooth' });"
        class="fixed bottom-6 right-6 text-lg bg-orange text-white px-4 py-2 rounded-full shadow-lg hover:bg-yellow-500 transition"
    >
        ↑
    </button>

    @livewireScripts
</body>
</html>
