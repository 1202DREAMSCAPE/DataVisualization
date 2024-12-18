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
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.0.0"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body class="flex h-screen bg-cover bg-center" style="background-image: url('{{ asset('images/blobgif.gif') }}')" >

<!--style="background-image: url('{{ asset('images/gifbg.gif') }}')"-->
    <!-- Main Content -->
    <div class="flex-1">
        <!-- Navbar -->
        <livewire:authenticated_navbar />
        <!-- Main Content Area -->
        <main class="p-6">
                <!-- Call Livewire FileUpload Component -->
                <livewire:file-upload />
                <livewire:chart-selector />

        </main>
        
    </div>
    

    @livewireScripts
</body>
</html>
