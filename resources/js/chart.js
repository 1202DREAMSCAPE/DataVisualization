// Event Listener for Chart Saved
window.addEventListener('chartSaved', (event) => {
    console.log('Charts saved:', event.detail);
    renderSavedCharts(event.detail);
});

// Event Listener for Chart Deleted
window.addEventListener('chartDeleted', (event) => {
    console.log('Chart deleted:', event.detail);
    renderSavedCharts(event.detail);
});

// Function to Render Saved Charts
function renderSavedCharts(savedCharts) {
    if (!Array.isArray(savedCharts)) {
        console.error('Invalid charts data:', savedCharts);
        return;
    }

    window.savedChartInstances = window.savedChartInstances || [];

    savedCharts.forEach((chart, index) => {
        console.log('Rendering chart:', chart);

        const canvas = document.getElementById(`chart-${index}`);
        if (canvas) {
            const ctx = canvas.getContext('2d');
            if (window.savedChartInstances[index]) {
                window.savedChartInstances[index].destroy(); // Destroy previous instance
            }
            window.savedChartInstances[index] = new Chart(ctx, {
                type: chart.data.type,
                data: chart.data.data,
                options: chart.data.options,
            });
        } else {
            console.warn(`Canvas not found for chart index: ${index}`);
        }
    });
}

// Event Listener for Updating Charts
window.addEventListener('updateChart', (event) => {
    console.log('updateChart event triggered:', event.detail);

    const chartData = Array.isArray(event.detail) ? event.detail[0] : event.detail;

    if (!chartData || !chartData.data) {
        console.error('chartData or chartData.data is undefined:', chartData);
        return;
    }

    switch (chartData.type) {
        case 'pie':
            createOrUpdatePieChart(chartData);
            break;
        case 'radar':
            createOrUpdateRadarChart(chartData);
            break;
        case 'gauge':
            createOrUpdateGaugeChart(chartData);
            break;
        default:
            createOrUpdateBarOrLineChart(chartData);
    }
});

// Helper Functions for Specific Chart Types
function createOrUpdateBarOrLineChart(chartData) {
    renderChart(chartData, 'chartCanvas', {
        responsive: true,
        maintainAspectRatio: false,
        scales: { y: { beginAtZero: true } },
    });
}

function createOrUpdatePieChart(chartData) {
    renderChart(chartData, 'chartCanvas', {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { position: 'top' } },
    });
}

function createOrUpdateRadarChart(chartData) {
    renderChart(chartData, 'chartCanvas', chartData.options || {});
}

function createOrUpdateGaugeChart(chartData) {
    const canvas = document.getElementById('chartCanvas');
    if (!canvas) {
        console.error('Canvas element not found');
        return;
    }

    const ctx = canvas.getContext('2d');
    if (window.currentChartInstance) {
        window.currentChartInstance.destroy();
    }

    const gaugeConfig = {
        type: 'doughnut',
        data: {
            datasets: [
                {
                    data: [chartData.data.value, chartData.data.max - chartData.data.value],
                    backgroundColor: [
                        chartData.data.color || 'rgba(75, 192, 192, 0.8)',
                        'rgba(200, 200, 200, 0.2)',
                    ],
                    circumference: 180,
                    rotation: 270,
                },
            ],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '75%',
            plugins: {
                legend: { display: false },
                tooltip: { enabled: false },
            },
            layout: { padding: { top: 20, right: 20, bottom: 80, left: 20 } },
        },
        plugins: [
            {
                id: 'gaugeValue',
                afterDraw: (chart) => renderGaugeOverlay(chart, chartData),
            },
        ],
    };

    try {
        window.currentChartInstance = new Chart(ctx, gaugeConfig);
        console.log('Gauge chart rendered successfully');
    } catch (error) {
        console.error('Error creating gauge chart:', error);
    }
}

// Utility Function to Render Generic Charts
function renderChart(chartData, canvasId, options) {
    const canvas = document.getElementById(canvasId);
    if (!canvas) {
        console.error(`Canvas element with ID ${canvasId} not found`);
        return;
    }

    const ctx = canvas.getContext('2d');
    if (window.currentChartInstance) {
        window.currentChartInstance.destroy();
    }

    window.currentChartInstance = new Chart(ctx, {
        type: chartData.type,
        data: chartData.data,
        options: options,
    });
}

// Utility Function for Gauge Overlays
function renderGaugeOverlay(chart, chartData) {
    const { ctx, width, height } = chart;
    const value = chartData.data.value;
    const minModel = chartData.data.minModel || { name: '', value: 0 };
    const maxModel = chartData.data.maxModel || { name: '', value: 0 };

    ctx.save();
    ctx.textAlign = 'center';
    ctx.textBaseline = 'middle';

    // Draw central value
    ctx.font = 'bold 36px Arial';
    ctx.fillStyle = '#333';
    ctx.fillText(`${value}${chartData.data.unit || '%'}`, width / 2, height * 0.4);

    // Draw min and max values
    ctx.font = 'bold 20px Arial';
    ctx.fillText('0', width * 0.15, height * 0.6);
    ctx.fillText('100%', width * 0.85, height * 0.6);

    // Draw title
    ctx.font = 'bold 24px Arial';
    ctx.fillText(chartData.data.label, width * 0.5, height * 0.75);

    // Draw model info
    ctx.font = '16px Arial';
    const lineHeight = height * 0.06;
    let currentY = height * 0.82;
    ctx.fillText(`Average across ${chartData.data.totalModels} models`, width * 0.5, currentY);
    currentY += lineHeight;
    ctx.fillText(`Best: ${maxModel.name} (${maxModel.value}%)`, width * 0.5, currentY);
    currentY += lineHeight;
    ctx.fillText(`Lowest: ${minModel.name} (${minModel.value}%)`, width * 0.5, currentY);

    ctx.restore();
}
