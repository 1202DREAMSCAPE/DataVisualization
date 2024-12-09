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
</head>
<body class="bg-gray-100 text-gray-800">
    
    <!-- Navbar -->
    <livewire:navbar />

    <!-- Main Content Area -->
    <div class="container mx-auto p-6">
        <!-- Title Section with Rename Modal -->
        <div x-data="{ showModal: false, newTitle: 'Saved Charts', tempTitle: '' }" x-cloak class="flex items-center justify-between mb-6">
            <div class="flex items-center space-x-4">
                <h2 class="text-4xl font-serif font-bold text-gray-700" x-text="newTitle"></h2>
                
            </div>
            <div class="flex items-center space-x-4">
    <!-- Existing Create New Chart button -->
    <button 
        @click="window.location.href='/project'"
        class="bg-green-500 hover:bg-green-600 font-dmSerif text-white px-4 py-2 rounded-lg flex items-center space-x-2"
    >
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
        </svg>
        <span>Create New Chart</span>
    </button>

    <!-- New Export PDF button -->
    <button 
        x-data
        @click="generatePDF()"
        class="bg-yellow-500 hover:bg-yellow-600 font-dmSerif text-white px-4 py-2 rounded-lg flex items-center space-x-2"
    >
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" />
        </svg>
        <span>Export PDF</span>
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
        // Create new jsPDF instance in landscape mode
        const { jsPDF } = window.jspdf;
        const doc = new jsPDF({
            orientation: 'landscape',
            unit: 'mm',
            format: 'a4'
        });

        // Use html2canvas to capture the entire container
        const container = document.querySelector('.container');
        
        // First hide any elements you don't want in the screenshot
        const buttonsToHide = container.querySelectorAll('button');
        buttonsToHide.forEach(button => button.style.display = 'none');
        
        // Capture the screenshot
        html2canvas(container, {
            scale: 2, // Higher quality
            useCORS: true,
            logging: false,
            backgroundColor: '#ffffff'
        }).then(canvas => {
            // Show the buttons again
            buttonsToHide.forEach(button => button.style.display = 'flex');
            
            // Convert to image
            const imgData = canvas.toDataURL('image/png', 1.0);
            
            // Calculate dimensions to fit on page
            const pageWidth = doc.internal.pageSize.getWidth();
            const pageHeight = doc.internal.pageSize.getHeight();
            const imgWidth = pageWidth - 20;
            const imgHeight = (canvas.height * imgWidth) / canvas.width;

            // Add the screenshot on top
            doc.addImage(imgData, 'PNG', 10, 10, imgWidth, imgHeight);
            
            // Generate blob and open in new tab
            const pdfBlob = doc.output('blob');
            const pdfUrl = URL.createObjectURL(pdfBlob);
            
            // Create a link and trigger download with new filename
            const link = document.createElement('a');
            link.href = pdfUrl;
            link.target = '_blank';
            link.download = 'VizOraChartReport.pdf';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
            
            // Cleanup
            setTimeout(() => URL.revokeObjectURL(pdfUrl), 100);
        });
        
    } catch (err) {
        console.error('Error generating PDF:', err);
        alert('There was an error generating the PDF. Please try again.');
    }
}
</script>
    
</body>
</html>
