<div class="bg-white rounded-lg p-6 shadow-lg" wire:ignore>
    @vite('resources/js/chart.js')

    <h2 class="text-xl font-semibold text-center mb-4">{{ ucfirst($chartType) }} Chart Visualization</h2>

    <!-- Chart Title Input -->
    <div class="mb-4">
        <label for="chartTitle" class="block text-gray-700 text-sm font-bold mb-2">Chart Title</label>
        <input 
            type="text" 
            id="chartTitle"
             name="chartTitle"
            wire:model="chartTitle"
            class="w-full p-2 border rounded-lg focus:ring-blue-500 focus:border-blue-500"
            placeholder="Enter chart title"
        />
    </div>
    <div class="grid grid-cols-1 gap-4 mb-4">
        @if ($chartType === 'radar')
            {{-- Radar Chart Controls --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Metrics to Compare</label>
                    <div class="flex flex-wrap gap-2">
                        @foreach ($headers as $key => $label)
                            @if ($key !== 'A')
                                <label class="inline-flex items-center bg-gray-100 rounded-md px-2 py-1">
                                    <input type="checkbox" wire:model.live="selectedMetrics" value="{{ $key }}" class="form-checkbox mr-2">
                                    <span>{{ $label }}</span>
                                </label>
                            @endif
                        @endforeach
                    </div>
                    @error('selectedMetrics') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Categories to Include</label>
                    <div class="flex flex-wrap gap-2">
                        @foreach ($categories as $category)
                            <label class="inline-flex items-center bg-gray-100 rounded-md px-2 py-1">
                                <input type="checkbox" wire:model.live="selectedCategories" value="{{ $category }}" class="form-checkbox mr-2">
                                <span>{{ $category }}</span>
                            </label>
                        @endforeach
                    </div>
                    @error('selectedCategories') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>
        @elseif ($chartType === 'pie')
            {{-- Pie Chart Controls --}}
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Select Data to Show</label>
                <div class="flex flex-wrap gap-2">
                    @foreach ($headers as $key => $label)
                        @if ($key !== 'A')
                            <label class="inline-flex items-center bg-gray-100 rounded-md px-2 py-1">
                                <input type="checkbox" wire:model.live="selectedColumns" value="{{ $key }}" class="form-checkbox mr-2">
                                <span>{{ $label }}</span>
                            </label>
                        @endif
                    @endforeach
                </div>
                @error('selectedColumns') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
        @elseif ($chartType === 'gauge')
            {{-- Gauge Chart Controls --}}
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Select Metric</label>
                <div class="flex flex-wrap gap-2">
                    @foreach ($headers as $key => $label)
                        @if ($key !== 'A')
                            <label class="inline-flex items-center bg-gray-100 rounded-md px-2 py-1">
                                <input type="radio" wire:model.live="selectedColumns" value="{{ $key }}" class="form-radio mr-2" name="gauge-metric">
                                <span>{{ $label }}</span>
                            </label>
                        @endif
                    @endforeach
                </div>
                @error('selectedColumns') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
        @elseif ($chartType === 'bar')
            {{-- Bar Chart Controls --}}
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="xAxis" class="block text-sm font-medium text-gray-700">X-Axis</label>
                    <select wire:model.live="xAxis" id="xAxis" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Choose X-Axis</option>
                        @foreach ($headers as $key => $header)
                            <option value="{{ $key }}">{{ $header }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="yAxis" class="block text-sm font-medium text-gray-700">Y-Axis</label>
                    <select wire:model.live="yAxis" id="yAxis" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Choose Y-Axis</option>
                        @foreach ($headers as $key => $header)
                            <option value="{{ $key }}">{{ $header }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        @endif
    </div>

      <!-- Chart Display Area -->
<div class="relative bg-gray-50 rounded-lg h-96 flex justify-center items-center">
    <canvas 
        id="chartCanvas" 
        class="{{ empty($chartData) ? 'hidden' : '' }} w-full max-w-xs md:max-w-md lg:max-w-lg"
    ></canvas>
</div>

    {{-- Control Buttons --}}
    <div class="mt-6 flex justify-center space-x-4">
       
        <button type="button" wire:click="saveChart"
                class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg transition-colors">
            Save Chart
        </button>
    </div>

    <script>
        document.addEventListener('livewire:initialized', () => {
            Livewire.on('updateChart', (chartData) => {
                const canvas = document.getElementById('chartCanvas');
                const existingChart = Chart.getChart(canvas);
                
                if (existingChart) {
                    existingChart.destroy();
                }

                if (!chartData || !chartData.data) {
                    console.error('Invalid chart data:', chartData);
                    return;
                }

                new Chart(canvas.getContext('2d'), {
                    type: chartData.type || '{{ $chartType }}',
                    data: chartData.data,
                    options: chartData.options || {}
                });
            });
        });
    </script>
</div>