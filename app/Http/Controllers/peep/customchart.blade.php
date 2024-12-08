<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VizOra - Data Visualization Platform</title>
    <!-- Styles -->
    @vite('resources/css/app.css')
    @livewireStyles
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=DM+Serif+Display&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <!-- Scripts -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.3/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="bg-lightgray text-darkgray">
    <!-- Navbar -->
    <livewire:navbar />

    <!-- Main Content -->
    <div class="container py-4">
        <!-- Embed Livewire Chart Customizer Component -->
        <livewire:chart-customizer :type="$type" :headers="$headers" :previewData="$previewData" />
    </div>

    <!-- Livewire Scripts -->
    @livewireScripts
</body>
</html>
