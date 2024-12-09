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
                   <button type="button"
                           wire:click="deleteChart({{ $chart['id'] }})"
                           class="absolute top-4 right-4 text-gray-400 hover:text-red-500 transition-colors">
                       <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                           <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                       </svg>
                   </button>
               </div>
           @endforeach
       </div>
   @else
       <p class="text-center text-gray-500 mt-8">No charts have been saved yet.</p>
   @endif
</div>