<div class="p-6 bg-white rounded-lg shadow-lg">
    <h2 class="text-xl font-bold mb-4">Build Your Chart</h2>

    <!-- Chart Type Selection -->
    <div class="mb-4">
        <h3 class="text-sm font-semibold">Select Chart Type</h3>
        <div class="flex gap-4 mt-2">
            @foreach (['bar' => 'Bar', 'pie' => 'Pie', 'line' => 'Line'] as $type => $label)
                <button wire:click="selectChartType('{{ $type }}')"
                        class="px-4 py-2 text-sm rounded-md {{ $chartType === $type ? 'bg-blue-500 text-white' : 'bg-gray-100' }}">
                    {{ $label }}
                </button>
            @endforeach
        </div>
    </div>

    <!-- Axis Selection -->
    @if ($chartType)
        <div class="grid grid-cols-2 gap-4 mb-4">
            <div>
                <label for="xAxis" class="block text-sm font-medium text-gray-700">X-Axis</label>
                <select id="xAxis" wire:model="xAxis" class="block w-full border-gray-300 rounded-md">
                    <option value="">Select X-Axis</option>
                    @foreach ($headers as $key => $header)
                        <option value="{{ $key }}">{{ $header }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="yAxis" class="block text-sm font-medium text-gray-700">Y-Axis</label>
                <select id="yAxis" wire:model="yAxis" class="block w-full border-gray-300 rounded-md">
                    <option value="">Select Y-Axis</option>
                    @foreach ($headers as $key => $header)
                        <option value="{{ $key }}">{{ $header }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    @endif

    <!-- Chart Preview -->
    <div class="mt-4">
        <div class="bg-gray-50 p-4 rounded-lg">
            <canvas id="chartCanvas" class="w-full h-64"></canvas>
        </div>
    </div>
</div>

<script>
    document.addEventListener('livewire:load', () => {
        Livewire.on('updateChart', (chartData) => {
            const ctx = document.getElementById('chartCanvas').getContext('2d');
            if (window.chartInstance) {
                window.chartInstance.destroy();
            }
            window.chartInstance = new Chart(ctx, chartData);
        });
    });
</script>
