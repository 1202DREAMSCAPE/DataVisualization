<div>
    @if (!empty($savedCharts))
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach ($savedCharts as $chart)
                <div class="bg-white rounded-lg shadow-lg p-4 relative" 
                     data-chart-index="{{ $chart['id'] }}"
                     x-data="{ 
                         init() {
                             this.$nextTick(() => {
                                 const ctx = this.$refs.canvas.getContext('2d');
                                 if (window.savedChartInstances && window.savedChartInstances[{{ $chart['id'] }}]) {
                                     window.savedChartInstances[{{ $chart['id'] }}].destroy();
                                 }
                                 window.savedChartInstances = window.savedChartInstances || [];
                                 window.savedChartInstances[{{ $chart['id'] }}] = new Chart(ctx, {{ json_encode($chart['data']) }});
                             });
                         }
                     }"
                     x-init="init">
                    <div class="h-48">
                        <canvas x-ref="canvas" id="chart-{{ $chart['id'] }}"></canvas>
                    </div>
                    <h3 class="text-lg font-bold text-center mt-4">{{ $chart['title'] }}</h3>
                    <button type="button"
                            wire:click="deleteChart({{ $chart['id'] }})"
                            wire:loading.attr="disabled"
                            onclick="console.log('Delete button clicked for chart {{ $chart['id'] }}')"
                            class="absolute top-2 right-2 text-white px-2 py-1 rounded-full hover:bg-red-300 transition">
                        ✖
                    </button>
                </div>
            @endforeach
        </div>
    @else
        <p class="text-gray-500">No charts have been saved yet.</p>
    @endif
</div>