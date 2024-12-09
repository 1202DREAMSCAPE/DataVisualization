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
                                window.savedChartInstances[{{ $chart['id'] }}] = new Chart(ctx, {
                                    ...{{ json_encode($chart['data']) }},
                                    options: {
                                        ...{{ json_encode($chart['data']['options'] ?? []) }},
                                        responsive: true,
                                        maintainAspectRatio: false
                                    }
                                });
                            });
                        }
                    }"
                    x-init="init">
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