<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VizOra - Data Visualization Platform</title>

    @vite('resources/css/app.css')
    @livewireStyles
    <link href="https://fonts.googleapis.com/css2?family=DM+Serif+Display&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">

    <!-- Alpine.js and Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>


</head>
<body class="bg-lightgray text-darkgray">
        <!-- Event listener set up BEFORE Livewire scripts -->

    <livewire:navbar />

    <div class="container mx-auto p-4">
        <h1 class="text-2xl font-bold mb-4">Customize Your {{ ucfirst($chartType ?? 'Chart') }}</h1>
        <p class="mb-4">Select which columns from your data will be used for the chart's axes or metrics.</p>

        <!-- ChartDisplay Component -->
        @livewire('chart-display', ['chartType' => $chartType, 'headers' => $headers, 'data' => $previewData])
    </div>

    @livewireScripts
    @stack('scripts')
</body>
</html>
