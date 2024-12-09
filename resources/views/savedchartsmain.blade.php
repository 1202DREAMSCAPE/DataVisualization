<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>VizOra - Saved Charts</title>
    @vite('resources/css/app.css')
    @livewireStyles
    <link href="https://fonts.googleapis.com/css2?family=DM+Serif+Display&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
</head>
<body class="bg-gray-100 text-gray-800">
    <!-- Navbar -->
    <livewire:navbar />

    <!-- Main Content Area -->
    <div class="container mx-auto p-6">
        <!-- Title Section -->
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-4xl font-serif font-bold text-gray-700">Saved Charts</h2>
            <button 
                @click="window.location.href='/project'"
                class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg flex items-center space-x-2"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                </svg>
                <span>Create New Chart</span>
            </button>
        </div>

        <!-- Cards Section -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @foreach ($charts as $chart)
            <div class="bg-white p-4 rounded-lg shadow relative">
                <h3 class="text-lg font-semibold mb-2">{{ $chart->title }}</h3>
                <div class="text-sm text-gray-600">
                    {{ json_encode($chart->chart_data) }}
                </div>
                <!-- Delete Button -->
                <button 
                    @click="openModal({{ $chart->id }}, '{{ $chart->title }}')"
                    class="absolute top-4 right-4 text-red-500 hover:text-red-700 transition-colors"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            @endforeach
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div 
        x-data="{ show: false, chartId: null, chartTitle: '' }"
        x-init="window.openModal = (id, title) => { show = true; chartId = id; chartTitle = title; }"
        x-show="show"
        x-cloak
        class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
    >
        <div class="bg-white p-6 rounded-lg shadow-lg w-1/3">
            <h3 class="text-lg font-bold text-gray-700 mb-4">Delete Chart</h3>
            <p class="text-sm text-gray-600">
                Are you sure you want to delete the chart: 
                <span class="font-semibold text-gray-800" x-text="chartTitle"></span>?
            </p>
            <div class="flex justify-end mt-6 space-x-4">
                <button 
                    @click="show = false" 
                    class="px-4 py-2 bg-gray-300 hover:bg-gray-400 text-gray-700 rounded-lg"
                >
                    Cancel
                </button>
                <button 
                    @click="deleteChart(chartId)" 
                    class="px-4 py-2 bg-red-500 hover:bg-red-600 text-white rounded-lg"
                >
                    Delete
                </button>
            </div>
        </div>
    </div>

    <!-- Alpine.js Functionality -->
    <script>
        function deleteChart(chartId) {
            fetch(`/delete-chart/${chartId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                },
            })
            .then(response => {
                if (response.ok) {
                    location.reload(); // Reload the page after deletion
                } else {
                    alert('Failed to delete the chart. Please try again.');
                }
            });
        }
    </script>
</body>
</html>
