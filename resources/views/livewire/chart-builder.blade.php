<div class="p-6 bg-white rounded-lg shadow-lg">
    <h2 class="text-2xl font-bold mb-4 text-gray-800">Build Your Chart</h2>
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Left Column: Chart Canvas -->
        <div class="bg-gray-50 p-4 rounded-lg shadow-md">
            <h3 class="text-lg font-semibold mb-4 text-gray-700">Chart Preview</h3>
            <div class="border border-gray-300 rounded-lg h-64 flex items-center justify-center relative">
                <div wire:loading class="absolute inset-0 bg-gray-100 bg-opacity-80 flex items-center justify-center">
                    <p class="text-gray-500 text-lg">Loading chart...</p>
                </div>
                @if ($chartType && !empty($chartData['series']))
                    <div id="chartCanvas-{{ $id }}" class="w-full h-full"></div>
                @else
                    <p class="text-gray-500">Please select a chart type and configure the data to view the chart.</p>
                @endif
            </div>
        </div>

        <!-- Right Column: Options -->
        <div>
            <form action="{{ route('generate.pdf') }}" method="POST">
                @csrf
                <!-- Form inputs for title and remarks -->
                <div class="mb-4">
                    <h3 class="text-sm font-semibold text-gray-700">Chart Title:</h3>
                    <input 
                        type="text" 
                        name="chart_title" 
                        class="w-full mt-2 p-2 border rounded-md" 
                        placeholder="Enter chart title" 
                        required
                    />
                </div>

                <div class="mb-4">
                    <h3 class="text-sm font-semibold text-gray-700">Description/Remarks:</h3>
                    <textarea 
                        name="chart_remarks" 
                        class="w-full mt-2 p-2 border rounded-md h-32 overflow-y-auto resize-none" 
                        placeholder="Add a description or remarks" 
                        required
                    ></textarea>
                </div>

                <div class="mb-4">
                    <h3 class="text-sm font-semibold text-gray-700">Select Chart Type:</h3>
                    <div class="flex gap-2 mt-2 flex-wrap">
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
                <!-- Axis Selection for Bar, Line, and Pie Charts -->
                <div>
                    <label for="xAxis" class="block text-sm font-medium text-gray-700">
                        X-Axis (Categories)
                    </label>
                    <select id="xAxis" wire:model="xAxis" class="block w-full border-gray-300 rounded-md">
                        <option value="">Select X-Axis</option>
                        @foreach ($headers as $key => $header)
                            <option value="{{ $key }}">{{ $header }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="yAxis" class="block text-sm font-medium text-gray-700">
                        Y-Axis (Values)
                    </label>
                    <select id="yAxis" wire:model="yAxis" class="block w-full border-gray-300 rounded-md">
                        <option value="">Select Y-Axis</option>
                        @foreach ($headers as $key => $header)
                            <option value="{{ $key }}">{{ $header }}</option>
                        @endforeach
                    </select>
                </div>
            @endif

            @if (in_array($chartType, ['radialBar', 'polarArea', 'radar']))
                <!-- Checkboxes for Gauge, Polar Area, and Radar Metrics -->
                <div>
                    <label for="metrics" class="block text-sm font-medium text-gray-700">Select Metrics</label>
                    <div class="space-y-2">
                        @foreach ($headers as $key => $header)
                            <label class="flex items-center">
                                <input 
                                    type="checkbox" 
                                    wire:model="selectedMetrics" 
                                    value="{{ $key }}" 
                                    class="mr-2 border-gray-300 rounded"
                                />
                                {{ $header }}
                            </label>
                        @endforeach
                    </div>
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
    