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

    <!-- Alpine.js -->
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    
    <!-- Chart.js with Radar Plugin -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.0.0"></script>
</head>
<body class="bg-lightgray text-darkgray">
    <livewire:authenticated_navbar />

    <div class="container mx-auto p-4">
        

        <!-- ChartDisplay Component -->
        @livewire('chart-display', [
            'chartType' => $chartType,
            'headers' => $headers,
            'data' => $previewData,
            'savedCharts' => $savedCharts ?? [] // Use an empty array as fallback
        ])

    </div>

    @livewireScripts
    @stack('scripts')

    <!-- Chart initialization script -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Register the datalabels plugin
            Chart.register(ChartDataLabels);
            
            // Set default Chart.js options for better visibility
            Chart.defaults.font.family = "'DM Serif Display', serif";
            Chart.defaults.font.size = 14;
            Chart.defaults.plugins.tooltip.padding = 10;
            Chart.defaults.plugins.legend.labels.padding = 20;
            
            // Set specific defaults for radar charts
            Chart.defaults.elements.line.borderWidth = 2;
            Chart.defaults.elements.point.radius = 4;
            Chart.defaults.elements.point.hoverRadius = 6;
            
            // Enable responsiveness
            Chart.defaults.responsive = true;
            Chart.defaults.maintainAspectRatio = false;
        });
    </script>
</body>
</html>