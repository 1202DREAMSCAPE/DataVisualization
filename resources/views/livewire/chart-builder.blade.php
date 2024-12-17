<div class="p-6 bg-white rounded-lg shadow-lg">
    <h2 class="text-xl font-bold mb-4">Build Your Chart</h2>

    <!-- Chart Type Selection -->
    <div class="mb-4">
        <h3 class="text-sm font-semibold">Select Chart Type</h3>
        <div class="flex gap-4 mt-2">
            @foreach ([
                'bar' => 'Bar',
                'pie' => 'Pie',
                'line' => 'Line',
                'radar' => 'Radar',
                'polarArea' => 'Polar Area',
                'radialBar' => 'Gauge'
            ] as $type => $label)
                <button 
                    wire:click="selectChartType('{{ $type }}')" 
                    class="px-4 py-2 text-sm rounded-md {{ $chartType === $type ? 'bg-blue-500 text-white' : 'bg-gray-100' }}">
                    {{ $label }}
                </button>
            @endforeach
        </div>
    </div>

    <!-- Axis/Column Selection -->
    @if ($chartType)
        <div class="grid grid-cols-2 gap-4 mb-4">
            @if (in_array($chartType, ['radar', 'polarArea']))
                <!-- Radar/Polar Area Chart Categories -->
                <div>
                    <label for="categories" class="block text-sm font-medium text-gray-700">Select Categories</label>
                    <div class="space-y-2">
                        @foreach ($headers as $key => $header)
                            <label class="flex items-center">
                                <input type="checkbox" wire:model="selectedCategories" value="{{ $key }}" class="mr-2">
                                {{ $header }}
                            </label>
                        @endforeach
                    </div>
                </div>
            @endif

            @if (in_array($chartType, ['bar', 'line', 'pie']))
                <!-- Bar/Line/Pie Axis Selection -->
                <div>
                    <label for="xAxis" class="block text-sm font-medium text-gray-700">X-Axis</label>
                    <select id="xAxis" wire:model="xAxis" class="block w-full border-gray-300 rounded-md">
                        <option value="">Select X-Axis</option>
                        @foreach ($headers as $key => $header)
                            <option value="{{ $key }}">{{ $header }}</option>
                        @endforeach
                    </select>
                </div>
                @if ($chartType !== 'pie')
                    <div>
                        <label for="yAxis" class="block text-sm font-medium text-gray-700">Y-Axis</label>
                        <select id="yAxis" wire:model="yAxis" class="block w-full border-gray-300 rounded-md">
                            <option value="">Select Y-Axis</option>
                            @foreach ($headers as $key => $header)
                                <option value="{{ $key }}">{{ $header }}</option>
                            @endforeach
                        </select>
                    </div>
                @endif
            @endif

            @if ($chartType === 'radialBar')
                <!-- Gauge Metric Selection -->
                <div>
                    <label for="metric" class="block text-sm font-medium text-gray-700">Select Metric</label>
                    <select id="metric" wire:model="selectedMetric" class="block w-full border-gray-300 rounded-md">
                        <option value="">Select Metric</option>
                        @foreach ($headers as $key => $header)
                            <option value="{{ $key }}">{{ $header }}</option>
                        @endforeach
                    </select>
                </div>
            @endif
        </div>
    @endif

    <!-- Chart Preview -->
    <div class="mt-4">
        <div wire:loading>
            <p class="text-center text-gray-500">Loading chart...</p>
        </div>

        <!-- Always include the chart container so it can be found -->
        <div class="bg-gray-50 p-4 rounded-lg">
            <div id="chartCanvas" class="w-full h-64"></div>
        </div>

        @if (!($chartType && !empty($chartData['series'])))
            <div class="text-center text-gray-500">Please select a chart type and configure the data to view the chart.</div>
        @endif
    </div>
</div>
<!-- Include ApexCharts Library -->
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
    document.addEventListener('livewire:init', () => {
        let chart = null;

        // Throttle function to limit the rate of function execution
        function throttle(func, delay) {
            let lastCall = 0;
            return function(...args) {
                const now = Date.now();
                if (now - lastCall >= delay) {
                    lastCall = now;
                    func.apply(this, args);
                }
            };
        }

        // Function to render the chart
        function renderChart(chartData) {
            console.log('Chart Data Received:', chartData);

            // Destroy the existing chart if it exists
            if (chart) {
                chart.destroy();
                chart = null;
            }

            // Use setTimeout to ensure that the DOM has been updated
            setTimeout(() => {
                // Select the chart container using the fixed ID
                let chartContainer = document.querySelector('#chartCanvas');
                if (!chartContainer) {
                    // If not found for any reason, create it dynamically
                    const containerParent = document.createElement('div');
                    containerParent.className = 'bg-gray-50 p-4 rounded-lg';
                    chartContainer = document.createElement('div');
                    chartContainer.id = 'chartCanvas';
                    chartContainer.className = 'w-full h-64';
                    containerParent.appendChild(chartContainer);
                    document.body.appendChild(containerParent);
                }

                // ApexCharts Configuration with Dynamic Chart Type
                chart = new ApexCharts(chartContainer, {
                    chart: {
                        type: chartData.chartType || 'bar', // Dynamic chart type with fallback
                        height: '100%',
                        width: '100%',
                        animations: {
                            enabled: true,
                            easing: 'easeinout',
                            speed: 800,
                        },
                        toolbar: {
                            show: true
                        },
                        zoom: {
                            enabled: true
                        },
                    },
                    series: [
                        {
                            name: 'Sales',
                            data: chartData[0].series[0].data, // Default hardcoded data
                        },
                    ],
                    xaxis: {
                        categories: chartData[0].xaxis.categories || ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'], // Default hardcoded categories
                        title: {
                            text: chartData.xAxisLabel || 'Days',
                        },
                    },
                    yaxis: chartData.yAxis ? {
                        title: {
                            text: chartData.yAxisLabel || 'Values',
                        },
                    } : {},
                    title: {
                        text: chartData.title || 'Weekly Sales Data', // Default hardcoded title
                        align: 'center',
                        style: {
                            fontSize: '20px',
                            fontWeight: 'bold',
                            color: '#263238'
                        }
                    },
                    plotOptions: {
                        bar: {
                            horizontal: false,
                            columnWidth: '50%',
                            endingShape: 'rounded',
                        },
                    },
                    dataLabels: {
                        enabled: true,
                    },
                    responsive: [{
                        breakpoint: 1000,
                        options: {
                            chart: {
                                height: '100%',
                                width: '100%',
                            },
                            legend: {
                                position: 'bottom'
                            }
                        }
                    }]
                });

                // Render the chart
                chart.render();
            }, 500); // 500ms delay to ensure DOM update
        }

        // Create a throttled version of the renderChart function
        const throttledRenderChart = throttle(renderChart, 2000); // 2-second throttle

        // Listen for the 'update-chart' event emitted from Livewire
        Livewire.on('update-chart', (chartData) => {
            throttledRenderChart(chartData);
        });
    });
</script>
    