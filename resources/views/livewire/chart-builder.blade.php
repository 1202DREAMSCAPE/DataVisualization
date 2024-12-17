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
        <button wire:click="selectChartType('{{ $type }}')"
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
        <div class="bg-gray-50 p-4 rounded-lg">
            <div id="chartCanvas" class="w-full h-64"></div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('livewire:load', () => {
        // Initialize ApexCharts instance
        let chart = null;

        // Livewire listener for chart updates
        Livewire.on('update-chart', (chartData) => {
            const chartOptions = {
                chart: {
                    type: chartData.chart.type,
                    height: '100%',
                    width: '100%',
                },
                series: chartData.series,
                xaxis: chartData.xaxis || {},
                labels: chartData.labels || [],
                ...chartData.options, // Additional options from Livewire
            };

            // Destroy the previous chart instance if it exists
            if (chart) {
                chart.destroy();
            }

            // Render a new chart
            const chartContainer = document.querySelector('#chartCanvas');
            if (chartContainer) {
                chart = new ApexCharts(chartContainer, chartOptions);
                chart.render();
            } else {
                console.error('Chart container not found!');
            }
        });
    });
</script>
