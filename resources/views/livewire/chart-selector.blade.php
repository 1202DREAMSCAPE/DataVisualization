<div class="fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center z-50 p-4"
     style="display: {{ $isOpen ? 'flex' : 'none' }};">
    <div class="bg-white rounded-lg p-6 max-w-xl w-full relative shadow-lg">
        <!-- Close Button -->
        <button
            wire:click="close"
            class="absolute top-2 right-2 text-gray-500 hover:text-gray-800"
        >
            ✖
        </button>

        <!-- Modal Content -->
        <h2 class="text-xl font-bold text-center mb-6">Select your Chart!</h2>

        <!-- Chart Options -->
        <div class="grid grid-cols-2 sm:grid-cols-3 gap-2">
            <!-- Bar Chart -->
            <div class="flex flex-col items-center">
                <button
                    wire:click="selectChart('bar')"
                    class="focus:outline-none border-2 p-2 rounded-lg
                           {{ $selectedChart === 'bar' ? 'border-yellow-500 bg-yellow-100' : 'border-gray-300 bg-white' }}"
                >
                    <img src="/images/place.gif" alt="Bar Chart" class="w-24 h-24 sm:w-28 sm:h-28 rounded shadow" />
                    <p class="mt-2 font-semibold text-sm text-center">Bar Chart</p>
                </button>
            </div>

            <!-- Pie Chart -->
            <div class="flex flex-col items-center">
                <button
                    wire:click="selectChart('pie')"
                    class="focus:outline-none border-2 p-2 rounded-lg
                           {{ $selectedChart === 'pie' ? 'border-pink-500 bg-pink-100' : 'border-gray-300 bg-white' }}"
                >
                    <img src="/images/place.gif" alt="Pie Chart" class="w-24 h-24 sm:w-28 sm:h-28 rounded shadow" />
                    <p class="mt-2 font-semibold text-sm text-center">Pie Chart</p>
                </button>
            </div>

            <!-- Word Cloud -->
            <div class="flex flex-col items-center">
                <button
                    wire:click="selectChart('word-cloud')"
                    class="focus:outline-none border-2 p-2 rounded-lg
                           {{ $selectedChart === 'word-cloud' ? 'border-green-500 bg-green-100' : 'border-gray-300 bg-white' }}"
                >
                    <img src="/images/place.gif" alt="Word Cloud" class="w-24 h-24 sm:w-28 sm:h-28 rounded shadow" />
                    <p class="mt-2 font-semibold text-sm text-center">Word Cloud</p>
                </button>
            </div>

            <!-- Radar Chart -->
            <div class="flex flex-col items-center">
                <button
                    wire:click="selectChart('radar')"
                    class="focus:outline-none border-2 p-2 rounded-lg
                           {{ $selectedChart === 'radar' ? 'border-red-500 bg-red-100' : 'border-gray-300 bg-white' }}"
                >
                    <img src="/images/place.gif" alt="Radar Chart" class="w-24 h-24 sm:w-28 sm:h-28 rounded shadow" />
                    <p class="mt-2 font-semibold text-sm text-center">Radar Chart</p>
                </button>
            </div>

            <!-- Gauge Chart -->
            <div class="flex flex-col items-center">
                <button
                    wire:click="selectChart('gauge')"
                    class="focus:outline-none border-2 p-2 rounded-lg
                           {{ $selectedChart === 'gauge' ? 'border-blue-500 bg-blue-100' : 'border-gray-300 bg-white' }}"
                >
                    <img src="/images/place.gif" alt="Gauge Chart" class="w-24 h-24 sm:w-28 sm:h-28 rounded shadow" />
                    <p class="mt-2 font-semibold text-sm text-center">Gauge Chart</p>
                </button>
            </div>
        </div>

        <!-- Proceed Button -->
        <div class="text-center mt-6">
            <button
                wire:click="proceed"
                class="bg-yellow-500 hover:bg-yellow-600 text-white px-6 py-2 rounded-lg w-full sm:w-auto"
            >
                Proceed!
            </button>
        </div>
    </div>
</div>