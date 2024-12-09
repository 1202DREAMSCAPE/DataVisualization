<div class="bg-white rounded-lg p-6 shadow-lg" wire:ignore>
@vite('resources/js/chart.js')

    <h2 class="text-xl font-semibold text-center mb-4">{{ ucfirst($chartType) }} Visualization</h2>

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

    {{-- Chart Display Area --}}
    <div class="relative h-96 bg-gray-50 rounded-lg">
        <canvas id="chartCanvas" class="{{ empty($chartData) ? 'hidden' : '' }}"></canvas>
        
        <div class="flex items-center justify-center h-full text-gray-500 {{ !empty($chartData) ? 'hidden' : '' }}">
            <p class="text-center">
                @switch($chartType)
                    @case('pie')
                        Select data to generate a pie chart
                        @break
                    @case('bar')
                        Choose axes to create a bar chart
                        @break
                    @case('radar')
                        Pick metrics and categories for radar comparison
                        @break
                    @case('gauge')
                        Select a metric for gauge visualization
                        @break
                    @default
                        Select options to generate chart
                @endswitch
            </p>
        </div>
    </div>

    {{-- Control Buttons --}}
    <div class="mt-6 flex justify-center space-x-4">
        <button wire:click="$dispatch('openChartSelector')" 
                class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg transition-colors">
            Change Chart Type
        </button>
        <button wire:click="resetChart" 
                class="bg-gray-100 hover:bg-gray-200 text-gray-800 px-4 py-2 rounded-lg border transition-colors">
            Reset Chart
        </button>
    </div>

    <script>
    document.addEventListener('livewire:initialized', () => {
        console.log('Livewire initialized, setting up chart listeners');
    });

    Livewire.on('updateChart', (chartData) => {
        console.log('Livewire updateChart event received:', chartData);
        const canvas = document.getElementById('chartCanvas');
        if (canvas) {
            canvas.classList.remove('hidden');
        }
    });
    </script>
</div>