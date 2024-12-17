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
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

    
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body class="flex h-screen bg-cover bg-center" style="background-image: url('{{ asset('images/blobgif.gif') }}')" >
<div class="table-responsive"></div>    
    <!-- Main Content -->
    <div class="flex-1">
        <!-- Navbar -->
        <livewire:authenticated_navbar />
        <!-- Main Content Area -->
        <main class="p-6">
        @livewire('chart-builder', ['headers' => $headers, 'data' => $cleanedData, 'chartType' => $chartType])
    </div>
    

    @livewireScripts
</body>
</html>
