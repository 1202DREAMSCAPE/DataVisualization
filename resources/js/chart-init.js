// resources/js/chart-init.js

// Initialize global chart instances
window.savedChartInstances = window.savedChartInstances || [];
window.currentChartInstance = null;

// Add this to your JavaScript file or in a script tag
window.addEventListener('chartDeleted', event => {
    const chartId = event.detail.chartId;
    if (window.savedChartInstances && window.savedChartInstances[chartId]) {
        window.savedChartInstances[chartId].destroy();
        delete window.savedChartInstances[chartId];
    }
});

// Alpine.js Chart Initialization
document.addEventListener('alpine:init', () => {
    Alpine.data('chartInit', (chartData) => ({
        chart: null,
        init() {
            this.$nextTick(() => {
                const canvas = this.$el.querySelector('canvas');
                const ctx = canvas.getContext('2d');
                if (this.chart) {
                    this.chart.destroy();
                }
                this.chart = new Chart(ctx, chartData);
            });
        },
        destroy() {
            if (this.chart) {
                this.chart.destroy();
                this.chart = null;
            }
        },
    }));
});

// Livewire Event Handlers
document.addEventListener('livewire:initialized', () => {
    Livewire.on('debug-info', (data) => {
        console.log('Debug Info:', data);
    });

    // Handle chart deletion
    Livewire.on('chart-deleted', (data) => {
        const { index } = data;
        console.log('Chart deletion requested for index:', index);
        
        // Clean up chart instance
        if (window.savedChartInstances[index]) {
            window.savedChartInstances[index].destroy();
            delete window.savedChartInstances[index];
            console.log(`Chart instance ${index} destroyed`);
        }
        
        // Remove from DOM
        const chartElement = document.querySelector(`[data-chart-index="${index}"]`);
        if (chartElement) {
            chartElement.remove();
            console.log(`Chart element ${index} removed from DOM`);
        }
    });

    // Handle saved charts update
    Livewire.on('charts-updated', (data) => {
        console.log('Charts update received:', data);
        renderSavedCharts(data.charts);
    });
});

// Chart Rendering Functions
function renderSavedCharts(savedCharts) {
    if (!Array.isArray(savedCharts)) {
        console.error('Invalid charts data:', savedCharts);
        return;
    }

    // Clean up existing instances first
    window.savedChartInstances.forEach((instance, index) => {
        if (instance) {
            instance.destroy();
            delete window.savedChartInstances[index];
        }
    });

    // Render new charts
    savedCharts.forEach((chart, index) => {
        console.log('Rendering chart:', chart);
        
        const canvas = document.getElementById(`chart-${index}`);
        if (!canvas) {
            console.warn(`Canvas not found for chart index: ${index}`);
            return;
        }

        const ctx = canvas.getContext('2d');
        window.savedChartInstances[index] = new Chart(ctx, {
            type: chart.data.type,
            data: chart.data.data,
            options: chart.data.options || {}
        });
    });
}

function renderChart(chartData, canvasId) {
    const canvas = document.getElementById(canvasId);
    if (!canvas) {
        console.error(`Canvas with ID ${canvasId} not found`);
        return;
    }

    const ctx = canvas.getContext('2d');
    
    // Clean up existing instance
    if (window.currentChartInstance) {
        window.currentChartInstance.destroy();
    }

    // Create new instance
    window.currentChartInstance = new Chart(ctx, {
        type: chartData.type,
        data: chartData.data,
        options: chartData.options || {}
    });
}

// Legacy Event Listeners (for backwards compatibility)
window.addEventListener('chartSaved', (event) => {
    console.log('Chart saved:', event.detail);
    renderSavedCharts(event.detail);
});

window.addEventListener('updateChart', (event) => {
    console.log('Chart update requested:', event.detail);
    const chartData = Array.isArray(event.detail) ? event.detail[0] : event.detail;
    
    if (!chartData || !chartData.data) {
        console.error('Invalid chart data:', chartData);
        return;
    }

    renderChart(chartData, `chart-${chartData.id}`);
});