<div class="fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center z-50 p-4"
     x-data="{ isOpen: false }"
     x-on:open-chart-selector.window="isOpen = true"
     x-on:close-chart-selector.window="isOpen = false"
     x-show="isOpen"
     x-cloak
     x-transition:enter="ease-out duration-300"
     x-transition:enter-start="opacity-0 scale-95"
     x-transition:enter-end="opacity-100 scale-100"
     x-transition:leave="ease-in duration-200"
     x-transition:leave-start="opacity-100 scale-100"
     x-transition:leave-end="opacity-0 scale-95">
    <div class="bg-white rounded-lg p-6 max-w-xl w-full relative shadow-lg">
        <button x-on:click="isOpen = false; $dispatch('close-chart-selector')"
                class="absolute top-2 right-2 text-gray-500 hover:text-gray-800 focus:outline-none">✖</button>

        <h2 class="text-xl font-bold text-center mb-6">Select Your Chart</h2>

        <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
    @foreach ([
        'bar' => ['label' => 'Bar Chart', 'image' => '/images/barchart.png'],
        'pie' => ['label' => 'Pie Chart', 'image' => '/images/piechart.png'],
        'radar' => ['label' => 'Radar Chart', 'image' => '/images/radarchart.png'],
        'gauge' => ['label' => 'Gauge Chart', 'image' => '/images/gaugechart.png'],
        'polarArea' => ['label' => 'Polar Area Chart', 'image' => '/images/polar.png'],
        ] as $chartType => $chart)
        <div class="flex flex-col items-center">
            <button wire:click="selectChart('{{ $chartType }}')"
                    class="focus:outline-none border-2 p-2 rounded-lg transition
                           {{ $selectedChart === $chartType ? 'border-yellow-500 bg-yellow-100' : 'border-gray-300 bg-white' }}">
                <img src="{{ $chart['image'] }}" alt="{{ $chart['label'] }}" class="w-24 h-24 sm:w-28 sm:h-28 rounded shadow" />
                <p class="mt-2 font-semibold text-sm text-center">{{ $chart['label'] }}</p>
            </button>
        </div>
    @endforeach
</div>


        <div class="text-center mt-6">
            <button wire:click="proceed"
                    class="bg-yellow-500 hover:bg-yellow-600 text-white px-6 py-2 rounded-lg w-full sm:w-auto transition focus:outline-none">
                Proceed!
            </button>
        </div>
    </div>
</div>
