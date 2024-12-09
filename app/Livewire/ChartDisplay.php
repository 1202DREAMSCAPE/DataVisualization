<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\SavedChart;

class ChartDisplay extends Component
{
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

    public function mount($chartType = null, $headers = [], $data = [])
    {
        $this->chartType = $chartType ?? 'bar';
        $this->headers = $headers;
        $this->data = $data;
        $this->savedCharts = [];

        logger('ChartDisplay mounted', [
            'chartType' => $this->chartType,
            'headers' => $this->headers,
            'data' => $this->data
        ]);

        if (!empty($this->headers) && !empty($this->data)) {
            $this->xAxis = array_key_first($this->headers);
            $this->yAxis = array_key_last($this->headers);

            if ($this->chartType === 'radar') {
                $this->categories = array_map(function($item) {
                    return $item['A']; // Get Model names
                }, $this->data);
            }

            $this->prepareChartData();
        }
    }


    public function saveChart()
    {
        try {
            // This will trigger JavaScript to collect chart data
            $this->dispatch('getChartData');
        } catch (\Exception $e) {
            session()->flash('error', 'Error saving chart: ' . $e->getMessage());
        }
    }

    // Add this method to receive chart data from JavaScript
    public function handleChartData($chartData)
{
    try {
        SavedChart::create([
            'title' => $this->chartTitle,
            'chart_data' => $chartData,
            'user_id' => auth()->id()
        ]);

        session()->flash('message', 'Chart saved successfully!');
        $this->dispatch('chartSaved');
        
        // Redirect to saved charts page
        return redirect()->route('saved-charts');
    } catch (\Exception $e) {
        session()->flash('error', 'Error saving chart: ' . $e->getMessage());
    }
}


public function deleteChart($index)
{
    unset($this->savedCharts[$index]);
    $this->savedCharts = array_values($this->savedCharts);

    $this->dispatchBrowserEvent('chartDeleted', $this->savedCharts);
}

public function saveChartAndRedirect()
{
    try {
        $this->validate([
            'chartData' => 'required',
            'chartType' => 'required',
        ]);

        // Manually set the chartData and chartType properties
        $this->chartData = $this->chartData;
        $this->chartType = $this->chartType;

        // Save the chart (this assumes you have a database or session-based saving logic)
        $title = ucfirst($this->chartType) . ' Chart';
        $this->saveChart($this->chartData, $title);

        // Redirect to /project
        return redirect()->route('project')->with('success', 'Chart saved successfully!');
    } catch (\Exception $e) {
        logger()->error('Error saving chart:', ['error' => $e->getMessage()]);
        session()->flash('error', 'An error occurred while saving the chart. Please try again.');
    }
}

    public function updatedSelectedColumns($value)
    {
        logger()->debug('Selected column updated', ['value' => $value]);

        if ($this->chartType === 'pie') {
            $this->preparePieChartData();
        } elseif ($this->chartType === 'gauge') {
            $this->prepareGaugeChartData();
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
                        'text' => 'Distribution of Selected Columns'
                    ]
                ],
            ],
        ];

        logger()->info('Pie Chart Data Prepared:', $this->chartData);
        $this->dispatch('updateChart', $this->chartData);
    }

    public function prepareChartData()
    {
        switch ($this->chartType) {
            case 'radar':
                $this->prepareRadarChartData();
                break;
            case 'pie':
                $this->preparePieChartData();
                break;
            case 'gauge':
                $this->prepareGaugeChartData();
                break;
            default:
                if (empty($this->xAxis) || empty($this->yAxis)) {
                    session()->flash('error', 'Invalid axis selection.');
                    return;
                }
            
                $labels = array_map(fn($item) => $item[$this->xAxis] ?? null, $this->data);
                $values = array_map(fn($item) => (int) $item[$this->yAxis] ?? 0, $this->data);
            
                $labels = array_filter($labels);
                $values = array_filter($values, fn($value) => $value !== null);
            
                $backgroundColors = $this->generateColors(count($values));
                $borderColors = array_map(fn($color) => str_replace('0.2', '1', $color), $backgroundColors);
            
                $this->chartData = [
                    'type' => $this->chartType,
                    'data' => [
                        'labels' => array_values($labels),
                        'datasets' => [
                            [
                                'label' => $this->headers[$this->yAxis] ?? 'Dataset',
                                'data' => array_values($values),
                                'backgroundColor' => $backgroundColors,
                                'borderColor' => $borderColors,
                                'borderWidth' => 1,
                            ],
                        ],
                    ],
                    'options' => [
                        'responsive' => true,
                        'scales' => [
                            'y' => ['beginAtZero' => true],
                        ],
                    ],
                ];
                logger()->info('Bar Chart data prepared.', $this->chartData);
                $this->dispatch('updateChart', $this->chartData);
        }
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