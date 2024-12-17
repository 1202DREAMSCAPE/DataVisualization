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

                <div class="flex justify-end">
                    <button 
                        type="submit" 
                        class="px-4 py-2 text-md bg-pink-500 text-white rounded-md hover:bg-red-600"
                    >
                        Generate Report
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        document.addEventListener('livewire:load', () => {
            let chart = null;

            // Listen for the update-chart event
            Livewire.on('update-chart', (chartData) => {
                console.log(chartData);

                if (!chartData.series || chartData.series.length === 0) {
                    console.error('Invalid chart data:', chartData);
                    return;
                }

                // Destroy the old chart if it exists
                if (chart) chart.destroy();

                // Select the chart container
                const chartContainer = document.querySelector(`#chartCanvas-${chartData.id}`);
                if (chartContainer) {
                    // Initialize the chart
                    chart = new ApexCharts(chartContainer, {
                        chart: {
                            type: chartData.chart.type,
                            height: '100%',
                            width: '100%',
                        },
                        series: chartData.series,
                        xaxis: chartData.xaxis || {},
                        labels: chartData.labels || [],
                        ...chartData.options,
                    });
                    chart.render();
                } else {
                    console.error('Chart container not found!');
                }
            });
        });
    });
</script>