<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>VizOra - Saved Charts</title>
    @vite('resources/js/chart.js')
    @vite('resources/css/app.css')
    @vite('resources/js/chart-init.js')
    @livewireStyles
    <link href="https://fonts.googleapis.com/css2?family=DM+Serif+Display&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    

    <!-- Alpine.js -->
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    
    <!-- Chart.js with Radar Plugin -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.0.0"></script>
</head>
<body class="bg-gray-100 text-gray-800">
    <!-- Navbar -->
    <livewire:navbar />

    <!-- Main Content Area -->
    <div class="container mx-auto p-6">
        <!-- Title Section with Rename Modal -->
        <div x-data="{ showModal: false, newTitle: 'Saved Charts', tempTitle: '' }" x-cloak class="flex items-center justify-between mb-6">
            <div class="flex items-center space-x-4">
                <h2 class="text-2xl font-serif font-bold text-gray-700" x-text="newTitle"></h2>
                <button 
                    @click="tempTitle = newTitle; showModal = true" 
                    class="text-sm text-blue-500 hover:text-blue-700 underline">
                    Rename
                </button>
            </div>
            
<button 
    x-data
    @click="$dispatch('open-chart-selector')"
    class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg flex items-center space-x-2"
>
    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
        <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
    </svg>
    <span>Create New Chart</span>
</button>

            <!-- Rename Modal -->
            <div x-show="showModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
                <div class="bg-white p-6 rounded-lg shadow-lg w-1/3">
                    <h3 class="text-lg font-bold text-gray-700 mb-4">Rename Saved Charts</h3>
                    <input 
                        type="text" 
                        x-model="tempTitle" 
                        class="w-full p-2 border rounded-lg focus:outline-none focus:ring focus:ring-blue-200"
                        placeholder="Enter new title">
                    <div class="flex justify-end mt-4 space-x-2">
                        <button 
                            @click="showModal = false; tempTitle = ''" 
                            class="px-4 py-2 bg-gray-300 hover:bg-gray-400 text-gray-700 rounded-lg">
                            Cancel
                        </button>
                        <button 
                            @click="newTitle = tempTitle; showModal = false" 
                            class="px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-lg">
                            Save
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Cards Section -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Add more cards dynamically using Livewire -->
            <livewire:saved-charts />
        </div>
    </div>

    <!-- Chart Selector -->
    <livewire:chart-selector />

    @livewireScripts
    
</body>
</html>
