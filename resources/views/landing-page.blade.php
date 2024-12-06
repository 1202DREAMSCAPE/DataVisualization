<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VizOra - Data Visualization Platform</title>
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

    <!-- First Section -->
    <section 
    id="home" 
    class="relative bg-cover bg-center h-screen flex flex-col items-center justify-center"
    style="background-image: url('{{ asset('images/mainbg.gif') }}');"
>
    <!-- Blurred Overlay -->
    <div class="absolute inset-0 bg-white bg-opacity-10 backdrop-blur-sm"></div>

    <!-- Content -->
    <div class="relative text-center mb-12 pb-5 z-10 px-4 mx-5">
        <h1 class="text-4xl md:text-6xl font-bold drop-shadow-xl">
            <span class="text-black">
                From files to insights:
            </span>
            <span class="block font-semibold text-black text-3xl md:text-5xl">
                create personalized visualizations in just a few clicks.
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

<section id="features" class="bg-darkgray text-lightgray py-16">
    <div class="max-w-6xl mx-auto px-4 text-center">
        <h2 class="text-3xl font-bold mb-10 text-white font-dmSerif">Our Features</h2>
        <div x-data="{ activeFeature: null }" class="space-y-8">
            <!-- Feature Cards -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Feature 1 -->
                <div>
                    <div 
                        @click="activeFeature = activeFeature === 1 ? null : 1"
                        class="bg-gradient-to-br from-indigo-500 via-purple-500 to-pink-500 text-white rounded-lg shadow-lg p-6 flex flex-col justify-between cursor-pointer hover:scale-105 transition-transform">
                        <div class="mb-4">
                            <h3 class="text-lg font-semibold mb-2">Personalized Bar Charts</h3>
                            <p class="text-sm">
                                Create custom bar charts in seconds by uploading your data. Enjoy intuitive customization options.
                            </p>
                        </div>
                        <button class="bg-white text-black px-4 py-2 mt-4 rounded-full font-semibold hover:bg-gray-200 w-full">
                            See Example
                        </button>
                    </div>
                    <!-- Mobile Example Image -->
                    <div class="mt-4 lg:hidden" x-show="activeFeature === 1" x-transition>
                        <img src="{{ asset('images/bgsquare.gif') }}" alt="Bar Chart Example" class="w-full max-h-96 object-cover rounded-lg shadow-lg">
                    </div>
                </div>

                <!-- Feature 2 -->
                <div>
                    <div 
                        @click="activeFeature = activeFeature === 2 ? null : 2"
                        class="bg-gradient-to-br from-indigo-600 via-blue-600 to-green-500 text-white rounded-lg shadow-lg p-6 flex flex-col justify-between cursor-pointer hover:scale-105 transition-transform">
                        <div class="mb-4">
                            <h3 class="text-lg font-semibold mb-2">AI-Powered Dashboard</h3>
                            <p class="text-sm">
                                Leverage AI tools for faster insights and better decision-making. Our dashboard is built for efficiency.
                            </p>
                        </div>
                        <button class="bg-white text-black px-4 py-2 mt-4 rounded-full font-semibold hover:bg-gray-200 w-full">
                            See Example
                        </button>
                    </div>
                    <!-- Mobile Example Image -->
                    <div class="mt-4 lg:hidden" x-show="activeFeature === 2" x-transition>
                        <img src="{{ asset('images/bgsquare.gif') }}" alt="AI Dashboard Example" class="w-full max-h-96 object-cover rounded-lg shadow-lg">
                    </div>
                </div>

                <!-- Feature 3 -->
                <div>
                    <div 
                        @click="activeFeature = activeFeature === 3 ? null : 3"
                        class="bg-gradient-to-br from-purple-600 via-pink-600 to-red-500 text-white rounded-lg shadow-lg p-6 flex flex-col justify-between cursor-pointer hover:scale-105 transition-transform">
                        <div class="mb-4">
                            <h3 class="text-lg font-semibold mb-2">Seamless File Support</h3>
                            <p class="text-sm">
                                Effortlessly upload and work with xlsx or CSV files to streamline your projects.
                            </p>
                        </div>
                        <button class="bg-white text-black px-4 py-2 mt-4 rounded-full font-semibold hover:bg-gray-200 w-full">
                            See Example
                        </button>
                    </div>
                    <!-- Mobile Example Image -->
                    <div class="mt-4 lg:hidden" x-show="activeFeature === 3" x-transition>
                        <img src="{{ asset('images/bgsquare.gif') }}" alt="File Support Example" class="w-full max-h-96 object-cover rounded-lg shadow-lg">
                    </div>
                </div>
            </div>

            <!-- Desktop Example Image Placeholder -->
            <div class="relative overflow-hidden transition-all duration-500 ease-in-out hidden lg:block" x-show="activeFeature !== null">
                <div x-show="activeFeature === 1" x-transition>
                    <img src="{{ asset('images/bgsquare.gif') }}" alt="Bar Chart Example" class="w-full max-h-96 object-cover rounded-lg shadow-lg">
                </div>
                <div x-show="activeFeature === 2" x-transition>
                    <img src="{{ asset('images/bgsquare.gif') }}" alt="AI Dashboard Example" class="w-full max-h-96 object-cover rounded-lg shadow-lg">
                </div>
                <div x-show="activeFeature === 3" x-transition>
                    <img src="{{ asset('images/bgsquare.gif') }}" alt="File Support Example" class="w-full max-h-96 object-cover rounded-lg shadow-lg">
                </div>
            </div>
        </div>
    </div>
</section>



<section id="about" class="bg-white text-darkgray py-16 relative">
    <div class="max-w-6xl mx-auto px-4 flex flex-col lg:flex-row items-center lg:items-start">
        <!-- Left: About Content -->
        <div class="lg:w-1/2">
            <h2 class="text-3xl font-bold mb-6 font-dmSerif text-center">About VizOra</h2>
            <p class="text-lg leading-relaxed mb-8 f">
                VizOra is your go-to platform for transforming raw data into stunning, personalized visualizations.
                With just a few clicks, you can create impactful insights that empower smarter decisions and greater understanding.
            </p>
            <!-- Interactive Buttons -->
            <div x-data="{ active: null }" class="space-y-4 ">
                <!-- Button 1 -->
                <div>
                    <button 
                        @click="active !== 1 ? active = 1 : active = null"
                        class="w-full text-left bg-indigo-600 text-white py-3 px-4 rounded-lg font-semibold hover:bg-indigo-700 focus:outline-none">
                        Vision
                    </button>
                    <div x-show="active === 1" x-transition class="mt-2 p-4 bg-gray-100 rounded-lg text-gray-700">
                        To make data visualization accessible, intuitive, and enjoyable for everyone—regardless of their expertise.
                    </div>
                </div>
                <!-- Button 2 -->
                <div>
                    <button 
                        @click="active !== 2 ? active = 2 : active = null"
                        class="w-full text-left bg-indigo-600 text-white py-3 px-4 rounded-lg font-semibold hover:bg-indigo-700 focus:outline-none">
                        Mission
                    </button>
                    <div x-show="active === 2" x-transition class="mt-2 p-4 bg-gray-100 rounded-lg text-gray-700">
                        To empower individuals and businesses to transform complex datasets into actionable insights through innovative tools and design.
                    </div>
                </div>
                <!-- Button 3 -->
                <div>
                    <button 
                        @click="active !== 3 ? active = 3 : active = null"
                        class="w-full text-left bg-indigo-600 text-white py-3 px-4 rounded-lg font-semibold hover:bg-indigo-700 focus:outline-none">
                        Core Values
                    </button>
                    <div x-show="active === 3" x-transition class="mt-2 p-4 bg-gray-100 rounded-lg text-gray-700">
                        Innovation, accessibility, creativity, and user-centric design are the cornerstones of everything we do.
                    </div>
                </div>
            </div>
        </div>

        <!-- Right: Large Logo Section -->
        <div class="lg:w-1/2 flex justify-center lg:justify-end mt-10 lg:mt-0">
            <img src="{{ asset('images/VizOraLight.png') }}" alt="VizOra Logo" class="w-full max-h-96 object-contain">
        </div>
    </div>
</section>


<!-- Scroll to Top Button -->
<button
    onclick="window.scrollTo({ top: 0, behavior: 'smooth' });"
    class="fixed bottom-6 right-6 text-lg bg-pink-500 text-white px-4 py-2 rounded-full shadow-lg hover:bg-green-500 transition"
>
    ↑
</button>

@livewireScripts
</body>
</html>
