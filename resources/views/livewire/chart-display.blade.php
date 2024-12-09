<div class="bg-white rounded-lg p-6 shadow-lg">
    <h2 class="text-xl font-semibold text-center mb-4">{{ ucfirst($chartType) }} Visualization</h2>

    <div class="grid grid-cols-1 gap-4 mb-4">
        @if ($chartType === 'pie')
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">
                    Select Columns to Aggregate
                </label>
                <div class="flex flex-wrap gap-2">
                    @foreach ($headers as $key => $label)
                        @if ($key !== 'A') {{-- Exclude the category column if it exists --}}
                            <label class="inline-flex items-center bg-gray-100 rounded-md px-2 py-1">
                                <input 
                                    type="checkbox" 
                                    wire:model.live="selectedColumns" 
                                    value="{{ $key }}" 
                                    class="form-checkbox mr-2"
                                >
                                <span>{{ $label }}</span>
                            </label>
                        @endif
                    @endforeach
                </div>
                @error('selectedColumns')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
        @elseif ($chartType === 'bar')
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="xAxis" class="block text-sm font-medium text-gray-700">X-Axis</label>
                    <select 
                        wire:model.live="xAxis" 
                        id="xAxis" 
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                    >
                        <option value="">Select X-Axis</option>
                        @foreach ($headers as $key => $header)
                            <option value="{{ $key }}">{{ $header }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="yAxis" class="block text-sm font-medium text-gray-700">Y-Axis</label>
                    <select 
                        wire:model.live="yAxis" 
                        id="yAxis" 
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                    >
                        <option value="">Select Y-Axis</option>
                        @foreach ($headers as $key => $header)
                            <option value="{{ $key }}">{{ $header }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        @endif
    </div>

    <div class="relative h-96 bg-gray-50 rounded-lg">
        @if (!empty($chartData))
            <canvas id="chartCanvas"></canvas>
        @else
            <div class="flex items-center justify-center h-full text-gray-500">
                <p class="text-center">
                    @if ($chartType === 'pie')
                        Select columns to generate a pie chart
                    @elseif ($chartType === 'bar')
                        Select X and Y axes to generate a bar chart
                    @else
                        No chart data available
                    @endif
                </p>
            </div>
        @endif
    </div>

    @vite('resources/js/chart.js')

    <div class="mt-6 flex justify-center space-x-4">
        <button 
            wire:click="$dispatch('openChartSelector')" 
            class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg transition-colors"
        >
            Change Chart Type
        </button>
        <button 
            wire:click="resetChart" 
            class="bg-gray-100 hover:bg-gray-200 text-gray-800 px-4 py-2 rounded-lg border transition-colors"
        >
            Reset Chart
        </button>
    </div>
</div>