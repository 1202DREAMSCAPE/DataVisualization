<div>
    <!-- Input for X-Axis -->
    <div class="mb-4">
        <label class="block font-bold mb-1">X-Axis Column</label>
        <select wire:model="xAxis" class="form-select">
            <option value="">-- Select Column --</option>
            @foreach($headers as $header)
                <option value="{{ $header }}">{{ $header }}</option>
            @endforeach
        </select>
    </div>

    <!-- Input for Y-Axis -->
    <div class="mb-4">
        <label class="block font-bold mb-1">Y-Axis Column</label>
        <select wire:model="yAxis" class="form-select">
            <option value="">-- Select Column --</option>
            @foreach($headers as $header)
                <option value="{{ $header }}">{{ $header }}</option>
            @endforeach
        </select>
    </div>

    <!-- Generate Chart Button -->
    <button wire:click="generateChart" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded">
        Generate Chart
    </button>

    <!-- Loading Indicator -->
    <div wire:loading wire:target="generateChart" class="flex justify-center items-center mt-4">
        <svg class="animate-spin h-8 w-8 text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
        </svg>
    </div>

    <!-- Chart Display -->
     
    <div class="mt-8">
    <div id="echarts-container" class="w-full h-96"></div>
    </div>
    <!-- ECharts Script -->
        <script>
         window.addEventListener('chartDataGenerated', event => {
    console.log("chartDataGenerated event received:", event.detail);

    const { xValues, yValues, chartType } = event.detail;

    // Check if chart container exists
    const chartDom = document.getElementById('echarts-container');
    if (!chartDom) {
        console.error('ECharts container not found!');
        return;
    }

    // Dispose of existing chart instance if any
    if (window.echartsInstance) {
        window.echartsInstance.dispose();
    }

    // Initialize new chart
    window.echartsInstance = echarts.init(chartDom);

    // Define chart options
    const options = {
        title: {
            text: `Chart Type: ${chartType || 'Undefined'}`, // Fallback to 'Undefined'
            left: 'center',
        },
        tooltip: {},
        xAxis: { 
            type: 'category', 
            data: xValues 
        },
        yAxis: { 
            type: 'value' 
        },
        series: [{
            name: 'Values',
            type: chartType,
            data: yValues,
        }],
    };

    // Special handling for pie charts
    if (chartType === 'pie') {
        options.series = [{
            type: 'pie',
            radius: '50%',
            data: xValues.map((x, i) => ({ value: yValues[i], name: x })),
        }];
        delete options.xAxis;
        delete options.yAxis;
    }

    console.log("Chart options:", options); // Debug options
    window.echartsInstance.setOption(options);
});

        </script>
</div>
