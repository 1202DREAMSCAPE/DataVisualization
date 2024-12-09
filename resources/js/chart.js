window.addEventListener('updateChart', (event) => {
    console.log('updateChart event triggered:', event.detail);

    const chartData = Array.isArray(event.detail) ? event.detail[0] : event.detail;

    if (!chartData || !chartData.data) {
        console.error('chartData or chartData.data is undefined:', chartData);
        return;
    }

    if (chartData.type === 'pie') {
        createOrUpdatePieChart(chartData); // Redirect to the pie chart function
    } else {
        createOrUpdateBarOrLineChart(chartData); // Use the existing function for bar or line charts
    }
});

function createOrUpdateBarOrLineChart(chartData) {
    console.log('Processing bar/line chartData:', chartData);

    // Validate chartData structure
    if (!chartData || !chartData.type || !chartData.data) {
        console.error('chartData or required properties are missing:', chartData);
        return;
    }

    if (!Array.isArray(chartData.data.labels) || chartData.data.labels.length === 0) {
        console.error('chartData.data.labels must be a non-empty array:', chartData.data.labels);
        return;
    }

    if (!Array.isArray(chartData.data.datasets) || chartData.data.datasets.length === 0) {
        console.error('chartData.data.datasets must be a non-empty array:', chartData.data.datasets);
        return;
    }

    // Ensure the canvas element exists
    const canvas = document.getElementById('chartCanvas');
    if (!canvas) {
        console.error('Canvas element with id "chartCanvas" not found.');
        return;
    }

    const ctx = canvas.getContext('2d');
    if (!ctx) {
        console.error('Failed to get canvas context.');
        return;
    }

    console.log('Valid chartData and canvas context found, proceeding to render bar/line chart.');

    // Destroy the previous chart instance if it exists
    if (window.currentChartInstance) {
        console.log('Destroying previous chart instance...');
        window.currentChartInstance.destroy();
    }

    // Render the new chart
    try {
        window.currentChartInstance = new Chart(ctx, {
            type: chartData.type, // e.g., 'bar', 'line'
            data: chartData.data, // Includes labels and datasets
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true, // Start Y-axis at zero
                    },
                },
            },
        });

        console.log('Bar/Line chart rendered successfully.');
    } catch (error) {
        console.error('Error occurred while rendering the bar/line chart:', error);
    }
}

function createOrUpdatePieChart(chartData) {
    console.log('Processing pie chartData:', chartData);

    // Validate chartData structure
    if (!chartData || !chartData.type || !chartData.data) {
        console.error('chartData or required properties are missing:', chartData);
        return;
    }

    if (!Array.isArray(chartData.data.labels) || chartData.data.labels.length === 0) {
        console.error('chartData.data.labels must be a non-empty array:', chartData.data.labels);
        return;
    }

    if (!Array.isArray(chartData.data.datasets) || chartData.data.datasets.length === 0) {
        console.error('chartData.data.datasets must be a non-empty array:', chartData.data.datasets);
        return;
    }

    // Ensure the canvas element exists
    const canvas = document.getElementById('chartCanvas');
    if (!canvas) {
        console.error('Canvas element with id "chartCanvas" not found.');
        return;
    }

    const ctx = canvas.getContext('2d');
    if (!ctx) {
        console.error('Failed to get canvas context.');
        return;
    }

    console.log('Valid chartData and canvas context found, proceeding to render pie chart.');

    // Destroy the previous chart instance if it exists
    if (window.currentChartInstance) {
        console.log('Destroying previous chart instance...');
        window.currentChartInstance.destroy();
    }

    // Render the new pie chart
    try {
        window.currentChartInstance = new Chart(ctx, {
            type: 'pie',
            data: chartData.data, // Includes labels and datasets
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                },
            },
        });

        console.log('Pie chart rendered successfully.');
    } catch (error) {
        console.error('Error occurred while rendering the pie chart:', error);
    }
}
