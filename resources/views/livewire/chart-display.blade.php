<div class="bg-white rounded-lg p-6 shadow-lg">
    <h2 class="text-xl font-semibold text-center mb-4">Chart: {{ ucfirst($chartType) }}</h2>

    <div class="grid grid-cols-2 gap-4 mb-4">
        <div>
            <label for="xAxis" class="block text-sm font-medium text-gray-700">Select X-Axis</label>
            <select wire:model="xAxis" id="xAxis" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                @foreach ($headers as $key => $header)
                    <option value="{{ $key }}">{{ $header }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label for="yAxis" class="block text-sm font-medium text-gray-700">Select Y-Axis</label>
            <select wire:model="yAxis" id="yAxis" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                @foreach ($headers as $key => $header)
                    <option value="{{ $key }}">{{ $header }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div wire:ignore class="relative h-96">
        @if (!empty($chartData))
            <canvas id="chartCanvas"></canvas>
        @else
            <p class="text-gray-600 text-center">No chart data available. Please select X and Y axes.</p>
        @endif
    </div>
    @vite('resources/js/chart.js')


    <div class="mt-6 flex justify-center gap-4">
        <button wire:click="$dispatch('openChartSelector')" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg shadow">
            Add Another Chart
        </button>
        <!-- <button wire:click="resetChart" class="bg-gray-100 hover:bg-gray-200 text-gray-800 px-4 py-2 rounded-lg shadow border">
            Reset
        </button> -->
    </div>

</div>
