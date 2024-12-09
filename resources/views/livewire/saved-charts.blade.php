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

                                 const chartData = {{ json_encode($chart['data']) }};
                                 const isGauge = chartData.type === 'gauge';

                                 window.savedChartInstances = window.savedChartInstances || [];
                                 window.savedChartInstances[{{ $chart['id'] }}] = new Chart(ctx, {
                                     type: isGauge ? 'doughnut' : chartData.type,
                                     data: chartData.data,
                                     options: {
                                         ...chartData.options,
                                         responsive: true,
                                         maintainAspectRatio: false,
                                         ...(isGauge ? {
                                             cutout: '75%',
                                             circumference: 180,
                                             rotation: 270,
                                             plugins: {
                                                 tooltip: { enabled: false },
                                                 legend: { display: false }
                                             }
                                         } : {})
                                     }
                                 });

                                 if (isGauge) {
                                     // Add value label
                                     const value = chartData.data.datasets[0].data[0];
                                     ctx.font = 'bold 24px Arial';
                                     ctx.textAlign = 'center';
                                     ctx.fillText(value + '%', ctx.canvas.width/2, ctx.canvas.height * 0.6);
                                 }
                             });
                         }
                     }"
                     x-init="init">
                    <div class="h-[300px]">
                        <canvas x-ref="canvas" id="chart-{{ $chart['id'] }}"></canvas>
                    </div>
                    <h3 class="text-2xl font-bold text-center mt-6">{{ $chart['title'] }}</h3>
                </div>
            @endforeach
        </div>
    @else
        <p class="text-center text-gray-500 mt-8">No charts have been saved yet.</p>
    @endif
</div>