<div>
    @if (!empty($savedCharts))
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach ($savedCharts as $chart)
                <div class="bg-white rounded-lg shadow-lg p-6 relative h-[400px]" 
                     data-chart-index="{{ $chart['id'] }}"
                     x-data="{ 
                         init() {
                             this.$nextTick(() => {
                                 const ctx = this.$refs.canvas.getContext('2d');
                                 if (window.savedChartInstances && window.savedChartInstances[{{ $chart['id'] }}]) {
                                     window.savedChartInstances[{{ $chart['id'] }}].destroy();
                                 }
                                 window.savedChartInstances = window.savedChartInstances || [];

                                 const chartType = {{ json_encode($chart['data']['type']) }};
                                 const chartData = {{ json_encode($chart['data']) }};
                                 const options = {
                                     ...chartData.options || {},
                                     responsive: true,
                                     maintainAspectRatio: false
                                 };

                                 switch (chartType) {
                                     case 'bar':
                                     case 'line':
                                     case 'scatter':
                                         window.savedChartInstances[{{ $chart['id'] }}] = new Chart(ctx, {
                                             type: chartType,
                                             data: chartData.data,
                                             options: options
                                         });
                                         break;

                                     case 'pie':
                                     case 'doughnut':
                                         window.savedChartInstances[{{ $chart['id'] }}] = new Chart(ctx, {
                                             type: chartType,
                                             data: chartData.data,
                                             options: options
                                         });
                                         break;

                                     case 'radar':
                                         window.savedChartInstances[{{ $chart['id'] }}] = new Chart(ctx, {
                                             type: 'radar',
                                             data: chartData.data,
                                             options: {
                                                 ...options,
                                                 plugins: {
                                                     legend: { position: 'top' },
                                                     title: { display: false, text: 'Radar Chart' }
                                                 },
                                                 scales: {
                                                     r: {
                                                         ticks: { beginAtZero: true },
                                                         angleLines: { display: true },
                                                         grid: { color: '#e2e8f0' }
                                                     }
                                                 }
                                             }
                                         });
                                         break;

                                     case 'gauge':
                                         const gaugeData = chartData.data;
                                         window.savedChartInstances[{{ $chart['id'] }}] = new Chart(ctx, {
                                             type: 'doughnut',
                                             data: {
                                                 datasets: [{
                                                     data: [gaugeData.value, gaugeData.max - gaugeData.value],
                                                     backgroundColor: [
                                                         gaugeData.color || 'rgba(75, 192, 192, 0.8)',
                                                         'rgba(200, 200, 200, 0.2)'
                                                     ],
                                                     borderWidth: 0
                                                 }]
                                             },
                                             options: {
                                                 ...options,
                                                 cutout: '75%',
                                                 rotation: 270,
                                                 circumference: 180,
                                                 plugins: {
                                                     legend: { display: false },
                                                     tooltip: { enabled: false }
                                                 }
                                             },
                                             plugins: [{
                                                 id: 'gaugeOverlay',
                                                 afterDraw: (chart) => {
                                                     const { ctx, width, height } = chart;
                                                     ctx.save();
                                                     ctx.textAlign = 'center';
                                                     ctx.font = 'bold 24px Arial';
                                                     ctx.fillStyle = '#333';
                                                     ctx.fillText(`${gaugeData.value}${gaugeData.unit || '%'}`, width / 2, height / 2 - 10);
                                                     ctx.font = 'bold 16px Arial';
                                                     ctx.fillText(gaugeData.label, width / 2, height / 2 + 20);
                                                     ctx.restore();
                                                 }
                                             }]
                                         });
                                         break;

                                     default:
                                         console.warn('Unsupported chart type:', chartType);
                                 }
                             });
                         }
                     }"
                     x-init="init">
                    <!-- Delete Button (X) -->
                    <form action="{{ route('charts.delete', $chart['id']) }}" method="POST" class="absolute top-2 right-2">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="bg-red-500 hover:bg-red-600 text-white px-2 py-1 rounded-full text-xs">
                            X
                        </button>
                    </form>

                    <!-- Chart Type Label -->
                    @if (isset($chart['data']['type']))
                        <div class="absolute top-2 left-2 bg-gray-800 text-white text-xs px-2 py-1 rounded">
                            {{ ucfirst($chart['data']['type']) }} Chart
                        </div>
                    @endif

                    <div class="h-[300px]">
                        <canvas x-ref="canvas" id="chart-{{ $chart['id'] }}"></canvas>
                    </div>
                    <h3 class="text-lg font-bold text-center mt-4">{{ $chart['title'] }}</h3>
                </div>
            @endforeach
        </div>
    @else
        <p class="text-center text-gray-500 mt-8">No charts have been saved yet.</p>
    @endif
</div>
