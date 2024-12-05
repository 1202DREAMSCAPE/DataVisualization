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
        <main class="p-6 w-full flex justify-center items-center">
        <div class="p-6 bg-white rounded-lg shadow max-w-lg w-full">
        <h1 class="text-2xl font-bold mb-6 text-center">Choose an Option</h1>

        <!-- Options Section -->
        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-4">Select what you want to do:</label>

            <!-- Generate AI CSV Button -->
            <div class="text-center mb-4">
                <a href="{{ route('generate-csv') }}"
                   class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600 transition-colors block">
                    Generate AI CSV File
                </a>
            </div>

            <!-- Upload File Button -->
            <div class="text-center">
                <a href="{{ route('upload-file') }}"
                   class="bg-green-500 text-white px-4 py-2 rounded-lg hover:bg-green-600 transition-colors block">
                    Upload Your Own File
                </a>
            </div>
        </div>

        <!-- Additional Notes or Instructions -->
        <p class="text-sm text-gray-500 text-center">
            You can either generate a dataset using AI or upload your existing file for processing.
        </p>
    </div>
        </main>
    </div>

    @livewireScripts
</body>
</html>
