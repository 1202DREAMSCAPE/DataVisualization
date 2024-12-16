<div class="min-h-screen w-full bg-white p-4 md:p-6 lg:p-8 shadow-lg" wire:ignore>
    @vite('resources/js/chart.js')

    <h2 class="text-xl md:text-2xl font-semibold text-center mb-4 md:mb-6">{{ ucfirst($chartType) }} Chart Visualization</h2>

    <!-- Two Column Layout for Desktop -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 h-full">
        <!-- Controls Column -->
        <div class="lg:col-span-1 space-y-6">
            <!-- Chart Title Input -->
            <div>
                <label for="chartTitle" class="block text-gray-700 text-sm font-bold mb-2">Chart Title</label>
                <input 
                    type="text" 
                    id="chartTitle"
                    name="chartTitle"
                    wire:model="chartTitle"
                    class="w-full p-2 border rounded-lg focus:ring-blue-500 focus:border-blue-500"
                    placeholder="Enter chart title"
                />
                <label for="remarks" class="block text-gray-700 text-sm mt-2 font-bold mb-2">Remarks</label>
                <input 
                    type="text" 
                    id="remarks"
                    name="remarks"
                    wire:model="remarks"
                    class="w-full p-2 border rounded-lg focus:ring-blue-500 focus:border-blue-500"
                    placeholder="Enter Your Remarks"
                />
            </div>

            <!-- Chart Controls -->
            <div class="space-y-4">
                @if ($chartType === 'radar')
                    {{-- Radar Chart Controls --}}
                    <div class="space-y-4">
                        <div>
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

                        <div>
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

            @elseif ($chartType === 'polarArea')
                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2">Select Data Columns</label>
                    <div class="space-y-2">
                        @foreach ($headers as $key => $label)
                            <label class="flex items-center space-x-2">
                            <input type="checkbox" wire:model.live="selectedColumns" value="{{ $key }}" class="form-checkbox text-blue-500 focus:ring-blue-500">
                            <span>{{ $label }}</span>
                            </label>
                        @endforeach
                    </div>
                    @error('selectedColumns') 
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p> 
                    @enderror
                </div>

            @elseif ($chartType === 'bar')
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
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
                    @elseif ($chartType === 'pie')
    <!-- Pie Chart Controls -->
    <div>
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
        @error('selectedColumns') 
            <p class="text-red-500 text-xs mt-1">{{ $message }}</p> 
        @enderror
    </div>

                 @elseif ($chartType === 'gauge')
                    <!-- Gauge Chart Controls -->
                    <div>
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
                @endif

                <!-- Save Button -->
                <div class="mt-4">
                    <button type="button" wire:click="saveChart"
                            class="w-full bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg transition-colors">
                        Save Chart
                    </button>
                </div>
            </div>
        </div>

        <!-- Chart Display Column -->
        <div class="lg:col-span-2">
            <div class="relative bg-gray-50 rounded-lg h-[calc(100vh-12rem)] md:h-[calc(100vh-14rem)] w-full">
                <canvas 
                    id="chartCanvas" 
                    class="{{ empty($chartData) ? 'hidden' : '' }} w-full h-full"
                ></canvas>
            </div>
        </div>
    </div>
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
                    options: {
                        ...chartData.options,
                        responsive: true,
                        maintainAspectRatio: false
                    }
                });
            });
        });
    </script>
</div>