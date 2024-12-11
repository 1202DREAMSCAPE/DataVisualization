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
    <script src="https://html2canvas.hertzen.com/dist/html2canvas.min.js"></script>

    <!-- Alpine.js -->
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>

    
    <!-- Chart.js with Radar Plugin -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.0.0"></script>


<style>
        /* Blurred background image */
            /* Hide button text on screens smaller than 768px */
    @media (max-width: 767px) {
        .button-text {
            display: none;
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

</head>
<body class="bg-gray-100 text-gray-800">

    <div class="background-blur"></div>
    <!-- Navbar -->
    <livewire:authenticated_navbar />

    <!-- Main Content Area -->
    <div class="container mx-auto p-6">
        <!-- Title Section with Rename Modal -->
        <div x-data="{ showModal: false, newTitle: 'Saved Charts', tempTitle: '' }" x-cloak class="flex items-center justify-between mb-6">
            <div class="flex items-center space-x-4 saved-charts">
                <h2 class="text-3xl font-serif font-bold" x-text="newTitle"></h2>
                
            </div>
            <div class="flex items-center space-x-4">
    <!-- Create New Chart button -->
<button @click="window.location.href='/project'"
    class="bg-green-500 hover:bg-green-600 font-dmSerif text-white px-4 py-2 rounded-lg flex items-center space-x-2 button-icon">
    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
        <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
    </svg>
    <span class="button-text">Create New Chart</span>
</button>

<!-- Export PDF button -->
<button @click="generatePDF()"
    class="bg-yellow-500 hover:bg-yellow-600 font-dmSerif text-white px-4 py-2 rounded-lg flex items-center space-x-2 button-icon">
    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
        <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" />
    </svg>
    <span class="button-text">Export PDF</span>
</button>
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


    <script>
        
      async function generatePDF() {
    try {
        const { jsPDF } = window.jspdf;

        const doc = new jsPDF({
            orientation: 'landscape',
            unit: 'px',
            format: 'a4',
        });

        const pageWidth = doc.internal.pageSize.getWidth();
        const pageHeight = doc.internal.pageSize.getHeight();

        const charts = document.querySelectorAll('[data-chart-index]');
        if (charts.length === 0) {
            alert('No charts to export!');
            return;
        }

        const logoURL = "{{ asset('images/vizora.png') }}";
        const chartsPerRow = 3;
        const rowsPerPage = 2;
        const margin = 20;
        const chartWidth = (pageWidth - margin * (chartsPerRow + 1)) / chartsPerRow;
        const chartHeight = (pageHeight - margin * (rowsPerPage + 2) - 60) / rowsPerPage;

        let currentChart = 0;

        while (currentChart < charts.length) {
            // Add logo to the page
            try {
                doc.addImage(logoURL, 'PNG', pageWidth - 80, 20, 60, 40);
            } catch (error) {
                console.error('Error adding logo:', error);
            }

            for (let row = 0; row < rowsPerPage && currentChart < charts.length; row++) {
                for (let col = 0; col < chartsPerRow && currentChart < charts.length; col++) {
                    const chartContainer = charts[currentChart];
                    const canvas = chartContainer.querySelector('canvas');

                    if (!canvas) {
                        console.warn('Canvas not found for chart:', currentChart);
                        continue;
                    }

                    // Render chart to canvas
                    const renderedCanvas = await html2canvas(chartContainer, {
                        scale: 2,
                        backgroundColor: '#ffffff',
                    });

                    const imgData = renderedCanvas.toDataURL('image/png');
                    const aspectRatio = renderedCanvas.width / renderedCanvas.height;

                    let printWidth = chartWidth;
                    let printHeight = printWidth / aspectRatio;

                    if (printHeight > chartHeight) {
                        printHeight = chartHeight;
                        printWidth = printHeight * aspectRatio;
                    }

                    const posX = margin + col * (chartWidth + margin);
                    const posY = margin + 60 + row * (chartHeight + margin);

                    try {
                        doc.addImage(imgData, 'PNG', posX, posY, printWidth, printHeight);
                        doc.setDrawColor(0);
                        doc.setLineWidth(0.5);
                        doc.rect(posX, posY, printWidth, printHeight);
                    } catch (error) {
                        console.error('Error adding chart image to PDF:', error);
                    }

                    currentChart++;
                }
            }

            if (currentChart < charts.length) {
                doc.addPage();
            }
        }

        doc.save('VizOra_Charts.pdf');
    } catch (error) {
        console.error('Error generating PDF:', error);
        alert('Failed to generate the PDF. Please try again.');
    }
}




</script>
    
</body>
</html>
