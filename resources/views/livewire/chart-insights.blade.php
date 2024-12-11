<div>
    <div class="chart-container">
        <h3 class="text-lg font-bold text-center mt-4">{{ $chart['title'] ?? 'Untitled Chart' }}</h3>
        <canvas id="chartCanvas"></canvas>
    </div>
    <button wire:click="generateInsights" class="mt-4 bg-blue-500 text-white px-4 py-2 rounded">
        Generate Insights
    </button>
    <div class="insights mt-4 bg-gray-100 p-4 rounded">
        <h4 class="font-bold">AI Insights:</h4>
        <p>{{ $insights }}</p>
    </div>
</div>
