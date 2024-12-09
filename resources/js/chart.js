// Complete updated chart.js
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

function createOrUpdateBarOrLineChart(chartData) {
    console.log('Creating bar/line chart with data:', chartData);

    const canvas = document.getElementById('chartCanvas');
    if (!canvas) {
        console.error('Canvas element not found');
        return;
    }

    const ctx = canvas.getContext('2d');
    if (window.currentChartInstance) {
        window.currentChartInstance.destroy();
    }

    window.currentChartInstance = new Chart(ctx, {
        type: chartData.type,
        data: chartData.data,
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                },
            },
        },
    });
}

function createOrUpdatePieChart(chartData) {
    console.log('Creating pie chart with data:', chartData);

    const canvas = document.getElementById('chartCanvas');
    if (!canvas) {
        console.error('Canvas element not found');
        return;
    }

    const ctx = canvas.getContext('2d');
    if (window.currentChartInstance) {
        window.currentChartInstance.destroy();
    }

    window.currentChartInstance = new Chart(ctx, {
        type: 'pie',
        data: chartData.data,
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
}

function createOrUpdateRadarChart(chartData) {
    console.log('Creating radar chart with data:', chartData);

    const canvas = document.getElementById('chartCanvas');
    if (!canvas) {
        console.error('Canvas element not found');
        return;
    }

    const ctx = canvas.getContext('2d');
    if (window.currentChartInstance) {
        window.currentChartInstance.destroy();
    }

    window.currentChartInstance = new Chart(ctx, {
        type: 'radar',
        data: chartData.data,
        options: chartData.options,
    });
}

function createOrUpdateGaugeChart(chartData) {
    console.log('Creating gauge chart with data:', chartData);

    const canvas = document.getElementById('chartCanvas');
    if (!canvas) {
        console.error('Canvas element not found');
        return;
    }

    const ctx = canvas.getContext('2d');
    if (window.currentChartInstance) {
        window.currentChartInstance.destroy();
    }

    // Get min, max, and average values from all models
    const modelValues = chartData.data.modelValues || [];
    const minModel = chartData.data.minModel || { name: '', value: 0 };
    const maxModel = chartData.data.maxModel || { name: '', value: 0 };

    // Create gauge chart configuration
    const gaugeChartConfig = {
        type: 'doughnut',
        data: {
            datasets: [{
                data: [
                    chartData.data.value,
                    chartData.data.max - chartData.data.value
                ],
                backgroundColor: [
                    chartData.data.color || 'rgba(75, 192, 192, 0.8)',
                    'rgba(200, 200, 200, 0.2)'
                ],
                circumference: 180,
                rotation: 270,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '75%',
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    enabled: false
                }
            },
            layout: {
                padding: {
                    top: 20,
                    right: 20,
                    bottom: 80,
                    left: 20
                }
            }
        },
        plugins: [{
            id: 'gaugeValue',
            afterDraw: (chart) => {
                const { ctx, width, height } = chart;
                const value = chartData.data.value;
                
                ctx.save();
                ctx.textAlign = 'center';
                ctx.textBaseline = 'middle';
                
                // Draw the central value
                ctx.font = 'bold 36px Arial';
                ctx.fillStyle = '#333';
                ctx.fillText(
                    `${value}${chartData.data.unit || '%'}`,
                    width / 2,
                    height * 0.4
                );

                // Draw min and max values
                ctx.font = 'bold 20px Arial';
                ctx.fillText('0', width * 0.15, height * 0.6);
                ctx.fillText('100%', width * 0.85, height * 0.6);

                // Draw the metric name (title)
                ctx.font = 'bold 24px Arial';
                ctx.fillText(chartData.data.label, width * 0.5, height * 0.75);

                // Draw subtitle and model information with increased spacing
                ctx.font = '16px Arial';
                ctx.fillStyle = '#555';
                
                // Add spacing between lines
                const lineHeight = height * 0.06;
                let currentY = height * 0.82;

                ctx.fillText(
                    `Average across ${chartData.data.totalModels} models`,
                    width * 0.5,
                    currentY
                );

                currentY += lineHeight;
                ctx.fillText(
                    `Best: ${maxModel.name} (${maxModel.value}%)`,
                    width * 0.5,
                    currentY
                );

                currentY += lineHeight;
                ctx.fillText(
                    `Lowest: ${minModel.name} (${minModel.value}%)`,
                    width * 0.5,
                    currentY
                );
                
                ctx.restore();
            }
        }]
    };

    try {
        window.currentChartInstance = new Chart(ctx, gaugeChartConfig);
        console.log('Gauge chart rendered successfully');
    } catch (error) {
        console.error('Error creating gauge chart:', error);
    }
}