<?php


namespace App\Livewire;

use Livewire\Component;
use App\Models\SavedChart;

class ChartDisplay extends Component
{
    // Existing properties remain the same
    public $chartType;
    public $headers = [];
    public $data = [];
    public $chartTitle = ' ';
    public $chartData = [];
    public $previewData = [];
    public $selectedColumns = [];
    public $selectedMetrics = [];
    public $categories = [];
    public $xAxis;
    public $yAxis;
    public $selectedCategories = [];
    public $savedCharts = [];

    // Mount method remains the same but adds bubble chart type handling
    public function mount($chartType = null, $headers = [], $data = [])
    {
        $this->chartType = $chartType ?? 'bar';
        $this->headers = $headers;
        $this->data = $data;
        $this->savedCharts = [];

        if (!empty($this->headers) && !empty($this->data)) {
            $this->xAxis = array_key_first($this->headers);
            $this->yAxis = array_key_last($this->headers);

            if ($this->chartType === 'radar') {
                $this->categories = array_map(function($item) {
                    return $item['A'];
                }, $this->data);
            }

            if ($this->chartType === 'bubble' && !empty($this->headers)) {
                // Default to first numeric column for bubble size
                foreach ($this->headers as $key => $header) {
                    if ($key !== 'A') {
                        $this->selectedColumns = [$key];
                        break;
                    }
                }
            }

            $this->prepareChartData();
        }
    }

    // Updated prepare chart data method to include bubble
    public function prepareChartData()
    {
        if (empty($this->headers)) {
            return;
        }

        try {
            switch ($this->chartType) {
                case 'bubble':
                    $this->prepareBubbleChartData();
                    break;
                case 'radar':
                    $this->prepareRadarChartData();
                    break;
                case 'pie':
                    $this->preparePieChartData();
                    break;
                case 'gauge':
                    $this->prepareGaugeChartData();
                    break;
                case 'bar':
                default:
                    $this->prepareBarChartData();
                    break;
            }

            if (!empty($this->chartData)) {
                $this->dispatch('updateChart', $this->chartData);
            }
        } catch (\Exception $e) {
            logger()->error('Error preparing chart data', [
                'error' => $e->getMessage(),
                'type' => $this->chartType
            ]);
            session()->flash('error', 'Error preparing chart: ' . $e->getMessage());
        }
    }

    // Update the saveChart method in ChartDisplay.php
public function saveChart()
{
    try {
        // Validate required data
        if (empty($this->chartData)) {
            session()->flash('error', 'No chart data to save.');
            return;
        }

        // Create the saved chart record
        $savedChart = SavedChart::create([
            'title' => $this->chartTitle ?: ucfirst($this->chartType) . ' Chart',
            'type' => $this->chartType,
            'chart_data' => $this->chartData,
            'user_id' => auth()->id()
        ]);

        session()->flash('message', 'Chart saved successfully!');
        return redirect()->route('saved-charts');
    } catch (\Exception $e) {
        logger()->error('Error saving chart:', ['error' => $e->getMessage()]);
        session()->flash('error', 'Error saving chart: ' . $e->getMessage());
    }
}

    private function prepareBarChartData()
{
    if (empty($this->xAxis) || empty($this->yAxis)) {
        session()->flash('error', 'Please select both X and Y axes.');
        return;
    }

    $labels = [];
    $values = [];

    foreach ($this->data as $row) {
        $label = $row[$this->xAxis] ?? null;
        $value = floatval($row[$this->yAxis] ?? 0);

        if ($label !== null) {
            $labels[] = $label;
            $values[] = $value;
        }
    }

    // Filter out empty values
    $labels = array_values(array_filter($labels));
    $values = array_values(array_filter($values, fn($value) => $value !== null));

    $backgroundColors = $this->generateColors(count($values));
    $borderColors = array_map(fn($color) => str_replace('0.2', '1', $color), $backgroundColors);

    $this->chartData = [
        'type' => 'bar',
        'data' => [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => $this->headers[$this->yAxis] ?? 'Dataset',
                    'data' => $values,
                    'backgroundColor' => $backgroundColors,
                    'borderColor' => $borderColors,
                    'borderWidth' => 1
                ]
            ]
        ],
        'options' => [
            'responsive' => true,
            'maintainAspectRatio' => false,
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'grid' => [
                        'display' => true
                    ]
                ],
                'x' => [
                    'grid' => [
                        'display' => false
                    ]
                ]
            ],
            'plugins' => [
                'legend' => [
                    'position' => 'top'
                ],
                'title' => [
                    'display' => false
                ]
            ]
        ]
    ];

    $this->dispatch('updateChart', $this->chartData);
}

// Add these methods to handle axis updates
public function updatedXAxis()
{
    $this->prepareBarChartData();
}

public function updatedYAxis()
{
    $this->prepareBarChartData();
}
    // Updated bubble chart data preparation
    private function prepareBubbleChartData()
    {
        if (empty($this->selectedColumns)) {
            return;
        }

        $columnKey = is_array($this->selectedColumns) ? $this->selectedColumns[0] : $this->selectedColumns;
        $bubbleData = [];
        $maxValue = 0;

        foreach ($this->data as $row) {
            $value = floatval($row[$columnKey] ?? 0);
            if ($value > $maxValue) {
                $maxValue = $value;
            }
        }

        foreach ($this->data as $index => $row) {
            $label = $row['A'] ?? 'Unknown';
            $value = floatval($row[$columnKey] ?? 0);
            
            if ($value > 0) {
                $bubbleData[] = [
                    'x' => rand(20, 80) / 100, // Random x between 0.2 and 0.8
                    'y' => $value,
                    'r' => 5 + (($value / $maxValue) * 20), // Radius between 5 and 25
                    'label' => $label,
                    'value' => $value
                ];
            }
        }

        $this->chartData = [
            'type' => 'bubble',
            'data' => [
                'datasets' => [[
                    'label' => $this->headers[$columnKey] ?? 'Dataset',
                    'data' => $bubbleData,
                    'backgroundColor' => array_map(function() {
                        return "hsla(" . rand(0, 360) . ", 70%, 50%, 0.6)";
                    }, $bubbleData),
                    'borderColor' => array_map(function() {
                        return "hsla(" . rand(0, 360) . ", 70%, 50%, 1)";
                    }, $bubbleData)
                ]]
            ],
            'options' => [
                'responsive' => true,
                'maintainAspectRatio' => false,
                'scales' => [
                    'x' => [
                        'min' => 0,
                        'max' => 1,
                        'grid' => ['display' => true],
                        'ticks' => [
                            'callback' => "function(value) { return value.toFixed(1); }"
                        ]
                    ],
                    'y' => [
                        'beginAtZero' => true,
                        'grid' => ['display' => true]
                    ]
                ],
                'plugins' => [
                    'tooltip' => [
                        'callbacks' => [
                            'label' => "function(context) { return context.raw.label + ': ' + context.raw.value; }"
                        ]
                    ],
                    'legend' => ['display' => false]
                ]
            ]
        ];
    }

    // Add updatedSelectedColumns method to handle bubble chart updates
    public function updatedSelectedColumns($value)
    {
        logger()->debug('Selected column updated', ['value' => $value]);

        switch ($this->chartType) {
            case 'bubble':
                $this->prepareBubbleChartData();
                break;
            case 'pie':
                $this->preparePieChartData();
                break;
            case 'gauge':
                $this->prepareGaugeChartData();
                break;
        }
    }


    public function updatedSelectedMetrics()
    {
        logger('Selected metrics updated:', ['metrics' => $this->selectedMetrics]);
        if ($this->chartType === 'radar') {
            $this->prepareRadarChartData();
        }
    }

    public function updatedSelectedCategories()
    {
        if ($this->chartType === 'radar') {
            $this->prepareRadarChartData();
        }
    }

    private function prepareGaugeChartData()
    {
        logger()->debug('Preparing gauge chart data', ['selectedColumn' => $this->selectedColumns]);
    
        if (empty($this->selectedColumns)) {
            logger()->debug('No column selected for gauge');
            return;
        }
    
        // Get values for all models
        $modelValues = [];
        foreach ($this->data as $row) {
            $modelValues[] = [
                'name' => $row['A'],
                'value' => floatval($row[$this->selectedColumns] ?? 0)
            ];
        }
    
        // Sort to find min and max
        usort($modelValues, fn($a, $b) => $b['value'] - $a['value']);
        
        // Calculate average
        $value = !empty($modelValues) 
            ? array_sum(array_column($modelValues, 'value')) / count($modelValues)
            : 0;
        
        $value = round($value, 1);
    
        // Get min and max models
        $maxModel = $modelValues[0] ?? ['name' => '', 'value' => 0];
        $minModel = end($modelValues) ?: ['name' => '', 'value' => 0];
    
        $this->chartData = [
            'type' => 'gauge',
            'data' => [
                'value' => $value,
                'max' => 100,
                'label' => $this->headers[$this->selectedColumns] ?? 'Gauge',
                'color' => $this->getGaugeColor($value),
                'unit' => '%',
                'totalModels' => count($modelValues),
                'modelValues' => $modelValues,
                'maxModel' => $maxModel,
                'minModel' => $minModel
            ]
        ];
    
        logger()->debug('Gauge chart data prepared', ['chartData' => $this->chartData]);
        $this->dispatch('updateChart', $this->chartData);
    }
    private function getGaugeColor($value)
    {
        if ($value >= 80) {
            return 'rgba(75, 192, 75, 0.8)'; // Green
        } elseif ($value >= 60) {
            return 'rgba(255, 206, 86, 0.8)'; // Yellow
        } else {
            return 'rgba(255, 99, 132, 0.8)'; // Red
        }
    }

    private function prepareRadarChartData()
    {
        logger('Preparing radar chart data');
        
        if (empty($this->selectedMetrics) || empty($this->selectedCategories)) {
            logger('No metrics or categories selected');
            return;
        }
    
        $datasets = [];
        $backgroundColors = $this->generateColors(count($this->selectedCategories));
        $borderColors = array_map(fn($color) => str_replace('0.2', '1', $color), $backgroundColors);
    
        foreach ($this->selectedCategories as $index => $category) {
            $categoryData = [];
            foreach ($this->selectedMetrics as $metric) {
                $value = 0;
                foreach ($this->data as $row) {
                    if ($row['A'] === $category) {
                        $value = (float) ($row[$metric] ?? 0);
                        break;
                    }
                }
                $categoryData[] = $value;
            }
    
            $datasets[] = [
                'label' => $category,
                'data' => $categoryData,
                'fill' => true,
                'backgroundColor' => str_replace('0.2', '0.2', $backgroundColors[$index]),
                'borderColor' => $borderColors[$index],
                'pointBackgroundColor' => $borderColors[$index],
                'pointBorderColor' => '#fff',
                'pointHoverBackgroundColor' => '#fff',
                'pointHoverBorderColor' => $borderColors[$index]
            ];
        }
    
        $this->chartData = [
            'type' => 'radar',
            'data' => [
                'labels' => array_map(function($metricKey) {
                    return $this->headers[$metricKey] ?? $metricKey;
                }, $this->selectedMetrics),
                'datasets' => $datasets
            ],
            'options' => [
                'responsive' => true,
                'plugins' => [
                    'legend' => [
                        'position' => 'top',
                    ],
                    'title' => [
                        'display' => true,
                        'text' => 'Radar Chart Comparison'
                    ]
                ],
                'scales' => [
                    'r' => [
                        'angleLines' => [
                            'display' => true
                        ],
                        'suggestedMin' => 0,
                        'suggestedMax' => 100,
                        'ticks' => [
                            'stepSize' => 20
                        ],
                        'pointLabels' => [
                            'font' => [
                                'size' => 12
                            ]
                        ]
                    ]
                ]
            ]
        ];
    
        logger('Radar Chart Data Prepared:', $this->chartData);
        $this->dispatch('updateChart', $this->chartData);
    }

    private function preparePieChartData()
    {
        if (empty($this->selectedColumns)) {
            session()->flash('error', 'Please select at least one column for the pie chart.');
            return;
        }

        $aggregatedData = [];

        foreach ($this->data as $item) {
            foreach ($this->selectedColumns as $column) {
                $label = $this->headers[$column] ?? $column;
                $value = (int) $item[$column] ?? 0;

                if (!isset($aggregatedData[$label])) {
                    $aggregatedData[$label] = 0;
                }
                $aggregatedData[$label] += $value;
            }
        }

        $aggregatedData = array_filter($aggregatedData);

        if (empty($aggregatedData)) {
            session()->flash('error', 'No valid data for pie chart.');
            return;
        }

        $backgroundColors = $this->generateColors(count($aggregatedData));

        $this->chartData = [
            'type' => 'pie',
            'data' => [
                'labels' => array_keys($aggregatedData),
                'datasets' => [
                    [
                        'data' => array_values($aggregatedData),
                        'backgroundColor' => $backgroundColors,
                        'hoverOffset' => 4,
                    ],
                ],
            ],
            'options' => [
                'responsive' => true,
                'plugins' => [
                    'legend' => ['position' => 'top'],
                    'title' => [
                        'display' => true,
                        'text' => ''
                    ]
                ],
            ],
        ];

        logger()->info('Pie Chart Data Prepared:', $this->chartData);
        $this->dispatch('updateChart', $this->chartData);
    }


    private function generateColors($count)
    {
        $colors = [];
        for ($i = 0; $i < $count; $i++) {
            $r = rand(0, 255);
            $g = rand(0, 255);
            $b = rand(0, 255);
            $colors[] = "rgba($r, $g, $b, 0.2)";
        }
        return $colors;
    }

    public function resetChart()
    {
        $this->chartType = null;
        $this->headers = [];
        $this->data = [];
        $this->chartData = [];
        $this->selectedCategories = [];
        $this->selectedMetrics = [];
        $this->categories = [];
    }

    public function proceed()
{
    $this->validate([
        'chartType' => 'required',
        // other validation rules
    ]);

    $chartData = $this->chartData;
    $chartTitle = 'New Chart';

    $this->saveChart($chartData, $chartTitle);

    $this->dispatchBrowserEvent('close-chart-selector');
    $this->resetChart();
}

    public function render()
    {
        return view('livewire.chart-display', [
            'availableColumns' => $this->headers
        ]);
    }
}