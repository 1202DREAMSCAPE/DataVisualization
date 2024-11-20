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
<body class="bg-gray-100 flex">

    <!-- Sidebar -->
    <aside class="w-48 bg-gray-900 text-gray-100 h-screen">
        <div class="p-6 text-center text-xl font-bold">DataBar</div>
        <nav>
            <a href="#" class="block px-6 py-3 hover:bg-gray-800">Dashboard</a>
            <a href="#" class="block px-6 py-3 bg-gray-800">My Project</a>
            <a href="#" class="block px-6 py-3 hover:bg-gray-800">Saved</a>
            <a href="#" class="block px-6 py-3 hover:bg-gray-800">Draft</a>
            <a href="#" class="block px-6 py-3 hover:bg-gray-800">Trash</a>
        </nav>
    </aside>

    <!-- Main Content -->
    <div class="flex-1">
        <!-- Navbar -->
        <header class="bg-white shadow-sm py-4 px-6 flex justify-between items-center">
            <h1 class="text-lg font-bold">Project Directory</h1>
            <div>
                <button class="bg-yellow-500 text-white px-4 py-2 rounded hover:bg-yellow-600">+ New Project</button>
                <img src="https://via.placeholder.com/40" alt="User Avatar" class="inline-block rounded-full ml-4">
            </div>
        </header>

        <!-- Main Content Area -->
        <main class="p-6">
                <!-- Call Livewire FileUpload Component -->
                <livewire:file-upload />
        </main>
    </div>

    @livewireScripts
</body>
</html>
