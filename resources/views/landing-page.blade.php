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
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body class="bg-lightgray text-darkgray">
    <!-- Navbar -->
    <livewire:navbar />

    <!-- Hero Section -->
    <section class="relative bg-lightyellow h-screen flex flex-col items-center justify-center">
        <div class="text-center mb-12 pb-5">
            <h1 class="text-darkgray text-3xl md:text-5xl font-bold px-4 mx-5">
                From files to insights: Create personalized bar charts in just a few clicks.
            </h1>
        </div>
        <!-- Animated Down Arrow -->
        <button
            onclick="document.querySelector('#features-section').scrollIntoView({ behavior: 'smooth' })"
            class="text-darkgray text-2xl animate-bounce"
        >
            ↓
        </button>
    </section>

    <!-- Features Section -->
    <section
        id="features-section"
        class="bg-gradient-to-b from-lightyellow to-orange h-screen flex items-center px-6 py-12"
    >
        <div class="max-w-6xl mx-auto grid grid-cols-1 md:grid-cols-2 gap-12 items-center">
            <div>
                <h2 class="text-darkgray text-3xl font-bold mb-6">
                    At DataBar, we empower users to turn data into compelling visuals with ease.
                </h2>
                <ul class="space-y-4">
                    <li class="flex items-center space-x-4">
                        <span class="text-darkgray">✔️</span>
                        <p>Create personalized bar charts in just a few clicks.</p>
                    </li>
                    <li class="flex items-center space-x-4">
                        <span class="text-darkgray">💡</span>
                        <p>AI integration and a user-friendly dashboard.</p>
                    </li>
                    <li class="flex items-center space-x-4">
                        <span class="text-darkgray">📂</span>
                        <p>Support for xlsx/csv files to manage projects effortlessly.</p>
                    </li>
                </ul>
                <p class="text-darkgray mt-6 font-bold text-xl">
                    Experience the future of data visualization today!
                </p>
            </div>
            <!-- Placeholder for Image -->
            <div class="bg-gray-300 w-full h-64 rounded-lg shadow-md">
                <!-- Replace with an actual image -->
            </div>
        </div>
    </section>

    <!-- Features Section 2 -->
    <section
        class="bg-gradient-to-t from-lightyellow to-orange h-screen flex items-center justify-center px-6"
    >
        <div class="max-w-6xl mx-auto text-center">
            <h2 class="text-darkgray text-3xl font-bold mb-6">Features keme</h2>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-darkgray text-lightgray px-6 py-4">
        <div class="max-w-6xl mx-auto flex justify-between">
            <p>&copy; 2024 Mama Mo</p>
            <ul class="flex space-x-4">
                <li><a href="#" class="hover:text-orange">Privacy Policy</a></li>
                <li><a href="#" class="hover:text-orange">Terms of Service</a></li>
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
