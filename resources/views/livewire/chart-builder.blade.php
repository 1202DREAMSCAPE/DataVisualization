<div class="p-6 bg-white rounded-lg shadow-lg">
    <h2 class="text-xl font-bold mb-4 text-gray-800">Build Your Chart</h2>
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Left Column: Chart Canvas -->
        <div class="bg-gray-50 p-4 rounded-lg shadow-md">
            <h3 class="text-lg font-semibold mb-4 text-gray-700">Chart Preview</h3>
            <div class="border border-gray-300 rounded-lg h-64 flex items-center justify-center relative">
                <div wire:loading class="absolute inset-0 bg-gray-100 bg-opacity-80 flex items-center justify-center">
                    <p class="text-gray-500 text-lg">Loading chart...</p>
                </div>
                @if ($chartType && !empty($chartData['series']))
                    <div id="chartCanvas" class="w-full h-full"></div>
                @else
                    <p class="text-gray-500">Please select a chart type and configure the data to view the chart.</p>
                @endif
            </div>
        </div>

        <!-- Right Column: Chart Options -->
        <div>
        <form action="{{ route('generate.pdf') }}" method="POST">
    @csrf
    <!-- Chart Title Field -->
    <div class="mb-4">
        <h3 class="text-sm font-semibold text-gray-700">Chart Title</h3>
        <input 
            type="text" 
            name="chart_title" 
            class="w-full p-2 border rounded-md" 
            placeholder="Enter chart title" 
            required
        />
    </div>

    <!-- Chart Description/Remarks Field -->
    <div class="mb-4">
        <h3 class="text-sm font-semibold text-gray-700">Description/Remarks</h3>
        <textarea id="chartRemarks" name="chart_remarks" class="w-full mt-2 p-2 border text-[13px] rounded-md h-32 overflow-y-auto resize-none" 
        placeholder="Add a description or remarks" required></textarea>
    </div>

    <!-- Chart Type Field -->
    <div class="mb-4">
        <h3 class="text-sm font-semibold text-gray-700">Select Chart Type:</h3>
        <div class="flex gap-2 mt-2 flex-wrap">
            @foreach ([
                'bar' => 'Bar',
                'pie' => 'Pie',
                'line' => 'Line',
                'radar' => 'Radar',
                'polarArea' => 'Polar Area',
            ] as $type => $label)
                <button 
                    type="button"
                    wire:click="selectChartType('{{ $type }}')" 
                    class="px-4 py-2 text-sm rounded-md {{ $chartType === $type ? 'bg-blue-500 text-white' : 'bg-gray-100' }}">
                    {{ $label }}
                </button>
            @endforeach
        </div>
    </div>

    <!-- Submit Button -->

            <!-- Chart Type Selection -->
            <!-- Axis/Column Selection -->
            @if ($chartType)
                <div class="grid grid-cols-2 gap-4 mb-4">
                    @if (in_array($chartType, ['radar']))
                        <!-- Radar/Polar Area Chart Categories -->
                        <div>
                            <label for="categories" class="block text-sm font-medium text-gray-700">Select Categories</label>
                            <div class="space-y-2">
                                @foreach ($headers as $key => $header)
                                    <label class="flex items-center">
                                        <input type="checkbox" wire:model="selectedCategories" value="{{ $key }}" class="mr-2">
                                        {{ $header }}
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    @if (in_array($chartType, ['bar', 'line', 'pie','polarArea']))
                        <!-- Axis Selection for Bar, Line, and Pie Charts -->
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
                    @endif

                    @if ($chartType === 'radialBar')
                        <!-- Gauge Metric Selection -->
                        <div>
                            <label for="metric" class="block text-sm font-medium text-gray-700">Select Metric</label>
                            <select id="metric" wire:model="selectedMetric" class="block w-full border-gray-300 rounded-md">
                                <option value="">Select Metric</option>
                                @foreach ($headers as $key => $header)
                                    <option value="{{ $key }}">{{ $header }}</option>
                                @endforeach
                            </select>
                        </div>
                    @endif
                </div>
            @endif
        <button 
            type="submit" 
            class="px-4 py-2 text-md bg-pink-500 text-white rounded-md hover:bg-red-600"
        >
            Download Report
        </button>

        <!-- Button -->
        <button type="button" onclick="generateRandomText()" class="px-4 py-2 text-md bg-violet-500 text-white rounded-md hover:bg-green-600">
            VizOra Insights
        </button>

    </div>
</form>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>

<script>
    document.querySelector('form').addEventListener('submit', async (e) => {
        e.preventDefault();

        const chartCanvas = document.querySelector('#chartCanvas');
        if (!chartCanvas) {
            alert('Chart not found!');
            return;
        }

        // Capture chart as an image
        const canvasImage = await html2canvas(chartCanvas).then((canvas) => canvas.toDataURL('image/png'));

        // Create hidden input to send the image data
        const chartImageInput = document.createElement('input');
        chartImageInput.type = 'hidden';
        chartImageInput.name = 'chart_image';
        chartImageInput.value = canvasImage;

        // Add the hidden input for the chart image
        e.target.appendChild(chartImageInput);

        // Create hidden input for the filename
        const filenameInput = document.createElement('input');
        filenameInput.type = 'hidden';
        filenameInput.name = 'filename';
        filenameInput.value = "{{ session('filename', 'default_filename') }}"; // Default or session filename

        // Add the hidden input for the filename
        e.target.appendChild(filenameInput);

        // Create hidden input for xAxis
        const xAxisInput = document.createElement('input');
        xAxisInput.type = 'hidden';
        xAxisInput.name = 'xAxis';
        xAxisInput.value = "{{ $xAxis }}"; // Pass xAxis value from Livewire

        // Add the hidden input for the xAxis
        e.target.appendChild(xAxisInput);

        // Create hidden input for yAxis
        const yAxisInput = document.createElement('input');
        yAxisInput.type = 'hidden';
        yAxisInput.name = 'yAxis';
        yAxisInput.value = "{{ $yAxis }}"; // Pass yAxis value from Livewire

        // Add the hidden input for the yAxis
        e.target.appendChild(yAxisInput);

        // Submit the form
        e.target.submit();
    });
</script>


<!-- Include ApexCharts Library -->
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
    let chart = null;
    window.chartDataAi = null;
    document.addEventListener('livewire:init', () => {

        // Throttle function to limit the rate of function execution
        function throttle(func, delay) {
            let lastCall = 0;
            return function(...args) {
                const now = Date.now();
                if (now - lastCall >= delay) {
                    lastCall = now;
                    func.apply(this, args);
                }
            };
        }

        // Function to render the chart
        function renderChart(chartData) {
            console.log('Chart Data Received:', chartData);

            // Destroy the existing chart if it exists
            if (chart) {
                chart.destroy();
                chart = null;
            }

            // Use setTimeout to ensure that the DOM has been updated
            setTimeout(() => {
                // Select the chart container using the fixed ID
                let chartContainer = document.querySelector('#chartCanvas');
                if (!chartContainer) {
                    // If not found for any reason, create it dynamically
                    const containerParent = document.createElement('div');
                    containerParent.className = 'bg-gray-50 p-4 rounded-lg';
                    chartContainer = document.createElement('div');
                    chartContainer.id = 'chartCanvas';
                    chartContainer.className = 'w-full h-64';
                    containerParent.appendChild(chartContainer);
                    document.body.appendChild(containerParent);
                }

                // ApexCharts Configuration with Dynamic Chart Type
                if(chartData[0].chartType =="bar" || chartData[0].chartType == "line"){
                                    chart = new ApexCharts(chartContainer, {
                    chart: {
                        type: chartData[0].chartType ?? "bar", // Dynamic chart type with fallback
                        height: '100%',
                        width: '100%',
                        animations: {
                            enabled: true,
                            easing: 'easeinout',
                            speed: 800,
                        },
                        toolbar: {
                            show: true
                        },
                        zoom: {
                            enabled: true
                        },
                    },
                    series: [
                        {
                            name: ' ',
                            data: chartData[0].series[0].data, // Default hardcoded data
                        },
                    ],
                    xaxis: {
                        categories: chartData[0].xaxis.categories || [' '], // Default hardcoded categories
                        title: {
                            text: chartData.xAxisLabel || ' ',
                        },
                    },
                    yaxis: chartData.yAxis ? {
                        title: {
                            text: chartData.yAxisLabel || 'Values',
                        },
                    } : {},
                    title: {
                        text: chartData.title || '  ', // Default hardcoded title
                        align: 'center',
                        style: {
                            fontSize: '16px',
                            fontWeight: 'bold',
                            color: '#263238'
                        }
                    },
                    plotOptions: {
                        bar: {
                            horizontal: false,
                            columnWidth: '50%',
                            endingShape: 'rounded',
                        },
                    },
                    dataLabels: {
                        enabled: true,
                    },
                    responsive: [{
                        breakpoint: 1000,
                        options: {
                            chart: {
                                height: '100%',
                                width: '100%',
                            },
                            legend: {
                                position: 'bottom'
                            }
                        }
                    }]
                });
                }
                
                if(chartData[0].chartType=="pie"){
                    chart = new ApexCharts(chartContainer, {
                    chart: {
                        type: "pie", // Dynamic chart type with fallback
                        height: '100%',
                        width: '100%',
                        animations: {
                            enabled: true,
                            easing: 'easeinout',
                            speed: 800,
                        },
                        toolbar: {
                            show: true
                        },
                        zoom: {
                            enabled: true
                        },
                    },
                    series: chartData[0].series,
                    labels: chartData[0].labels,
                    title: {
                        text: chartData[0].options.title.text || ' ', // Default hardcoded title
                        align: 'left',
                        style: {
                            fontSize: '16px',
                            fontWeight: 'bold',
                            color: '#263238'
                        }
                    },
                    responsive: [{
                        breakpoint: 200,
                        options: {
                            chart: {
                                height: '100%',
                                width: '100%',
                            },
                            legend: {
                                position: 'bottom'
                            }
                        }
                    }]
                });
                }

                if(chartData[0].chartType=="radar"){
                    chart = new ApexCharts(chartContainer, {
                    chart: {
                        type: "radar", // Dynamic chart type with fallback
                        height: '100%',
                        width: '100%',
                        animations: {
                            enabled: true,
                            easing: 'easeinout',
                            speed: 800,
                        },
                        toolbar: {
                            show: true
                        },
                        zoom: {
                            enabled: true
                        },
                    },
                    series: chartData[0].series,
                    labels: chartData[0].labels,
                    title: {
                        text: chartData[0].options.title.text || ' ', // Default hardcoded title
                        align: 'left',
                        style: {
                            fontSize: '16px',
                            fontWeight: 'bold',
                            color: '#263238'
                        }
                    },
                    responsive: [{
                        breakpoint: 200,
                        options: {
                            chart: {
                                height: '100%',
                                width: '100%',
                            },
                            legend: {
                                position: 'bottom'
                            }
                        }
                    }]
                });
                }

                if(chartData[0].chartType=="polarArea"){
                    chart = new ApexCharts(chartContainer, {
                    chart: {
                        type: "polarArea", // Dynamic chart type with fallback
                        height: '100%',
                        width: '100%',
                        animations: {
                            enabled: true,
                            easing: 'easeinout',
                            speed: 800,
                        },
                        toolbar: {
                            show: true
                        },
                        zoom: {
                            enabled: true
                        },
                    },
                    series: chartData[0].series,
                    labels: chartData[0].labels,
                    title: {
                        text: chartData[0].options.title.text || ' ', // Default hardcoded title
                        align: 'left',
                        style: {
                            fontSize: '16px',
                            fontWeight: 'bold',
                            color: '#263238'
                        }
                    },
                    responsive: [{
                        breakpoint: 200,
                        options: {
                            chart: {
                                height: '100%',
                                width: '100%',
                            },
                            legend: {
                                position: 'bottom'
                            }
                        }
                    }]
                });
                }


                // Render the chart
                chart.render();
            }, 500); // 500ms delay to ensure DOM update
        }

        // Create a throttled version of the renderChart function
        const throttledRenderChart = throttle(renderChart, 2000); // 2-second throttle

        // Listen for the 'update-chart' event emitted from Livewire
        Livewire.on('update-chart', (chartData) => {
            chartDataAi = chartData;
            throttledRenderChart(chartData);
        });
    });
</script>

<script>
    async function generateRandomText() {
        if (!chartDataAi || chartDataAi.length === 0) {
            document.getElementById("chartRemarks").value = "No chart data available.";
            return;
        }

        // Extract the first object from the array
        const chartInfo = chartDataAi[0];

        // Function to format object fields into a string
        const formatObject = (obj, indent = 0) =>
            Object.entries(obj).map(([key, value]) => {
                const padding = '  '.repeat(indent);
                if (value && typeof value === 'object') {
                    return Array.isArray(value)
                        ? `${padding}${key}: [${value.join(', ')}]`
                        : `${padding}${key}:\n${formatObject(value, indent + 1)}`;
                }
                return `${padding}${key}: ${value || 'N/A'}`;
            }).join('\n');

        // Format the chartInfo object
        const formattedChartData = formatObject(chartInfo);

        // Configuration for prompt rules
        const promptRules = [
            "Make the answer short and concise.",
            "Focus on key trends and base on the type of chart.",
            "Use simple language for better understanding.",
            "No more than 95 words",
            "Make this very convincing and always show like an insight",
            "Always start with the chart name and the most important insight",
            "End with a very good conclusion",
            "Always make it only in one paragraph directly no other paragraphs",
            "Always make it very clear and easy to understand",
            "Ensure end is trim without space or new line space",
        ];

        // Join rules into a single instruction
        const rulesText = promptRules.map((rule, index) => `${index + 1}. ${rule}`).join('\n');

        // Prepare the prompt for the AI API
        const prompt = `Analyze the following chart data and provide insights based on these rules:\n\n${rulesText}\n\nChart Data:\n\n${formattedChartData}`;

        // Correct structure for Gemini AI API
        const body = {
            contents: [
                {
                    parts: [
                        { text: prompt }
                    ]
                }
            ]
        };

        const apiKey = "AIzaSyDfOJetSni2WRzl1UHv9S0f1zZPoJxJrqk";
        const endpoint = `https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash-latest:generateContent?key=${apiKey}`;

        try {
            const response = await fetch(endpoint, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(body),
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const data = await response.json();
            console.log(data);
            // Get AI-generated insight from the response
            const aiInsight = data?.candidates?.[0]?.content.parts[0].text || "No insight generated by AI.";

            // Update the textarea with the AI-generated insight
            document.getElementById("chartRemarks").value = aiInsight;

        } catch (error) {
            document.getElementById("chartRemarks").value = `Error: ${error.message}`;
        }
    }
</script>
