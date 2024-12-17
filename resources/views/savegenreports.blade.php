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

    <style>
        /* Blurred background image */
            /* Hide button text on screens smaller than 768px */
    @media (max-width: 767px) {
        .button-text {
            display: none;
        }
    }

    @media (max-width: 640px) {
        .saved-charts {
            font-size: 1.25rem; /* Adjust font size for smaller screens */
            padding: 8px 16px; /* Reduce padding on smaller screens */
            border-radius: 15px; /* Slightly less rounded corners */
        }
    }

    .saved-charts {
        background: linear-gradient(to right, #ff69b4, #db7093); /* Adjust the colors as needed */
        color: white; /* Adjust text color as needed */
        padding: 10px 20px; /* Adjust padding as needed */
        border-radius: 20px; /* Rounded corners */
        text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.1); /* Subtle text shadow */
        box-shadow: 2px 2px 5px rgba(0, 0, 0, 0.2); /* Box shadow for depth */
        display: inline-block; /* Keeps the background tight around the text */
        text-align: center; /* Center the text */
    }

    /* Ensure SVG icons center-align when text is not visible */
    .button-icon {
        display: flex; 
        align-items: center; 
        justify-content: center;
    }

        .background-blur {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: url('{{ asset('images/blobgif.gif') }}');
            background-size: cover;
            background-position: center;
            filter: blur(8px);
            -webkit-filter: blur(8px);
            z-index: -1;
        }
        .bg-overlay {
            background-color: rgba(255, 255, 255, 0.95);
        }
    </style>

    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>

<body class="flex h-screen bg-gray-200" >
<!-- <div class="background-blur"></div> -->

<div class="table-responsive"></div>    
    <!-- Main Content -->
    <div class="flex-1">
        <!-- Navbar -->
        <livewire:authenticated_navbar />
        <!-- Main Content Area -->
        <main class="p-6">
        <livewire:generated-reports />
    </div>
    

    @livewireScripts
</body>
</html>
