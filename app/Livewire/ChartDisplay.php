<?php

namespace App\Livewire;

use Livewire\Component;

class ChartDisplay extends Component
{
    public $chartType;
    public $headers = [];
    public $data = [];
    public $chartData = [];
    public $previewData = []; // Add this property
    public $selectedColumns = []; // Holds the selected column for the pie chart

    public $xAxis;
    public $yAxis;

    public function mount($chartType = null, $headers = [], $data = [])
    {
        $this->chartType = $chartType ?? 'bar';
        $this->headers = $headers;
        $this->data = $data;

        logger('ChartDisplay mounted', [
            'chartType' => $this->chartType,
            'headers' => $this->headers,
            'data' => $this->data
        ]);

        if (!empty($this->headers) && !empty($this->data)) {
            $this->xAxis = array_key_first($this->headers);
            $this->yAxis = array_key_last($this->headers);

            $this->prepareChartData();
        }
    }

    public function updatedSelectedColumns()
    {
        if ($this->chartType === 'pie') {
            $this->preparePieChartData();
        }
    }

    private function preparePieChartData()
    {
        // Validate column selection
        if (empty($this->selectedColumns)) {
            session()->flash('error', 'Please select at least one column for the pie chart.');
            return;
        }

        // Aggregate data across selected columns
        $aggregatedData = [];

        foreach ($this->data as $item) {
            foreach ($this->selectedColumns as $column) {
                $label = $this->headers[$column] ?? $column; // Use header as label if available
                $value = (int) $item[$column] ?? 0;

                if (!isset($aggregatedData[$label])) {
                    $aggregatedData[$label] = 0;
                }
                $aggregatedData[$label] += $value;
            }
        }

        // Remove zero values
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

    // public function updatedXAxis()
    // {
    //     $this->prepareChartData();
    // }

    // public function updatedYAxis()
    // {
    //     $this->prepareChartData();
    // }

    public function updatedXAxis()
    {
        $this->prepareChartData();
    }

    public function updatedYAxis()
    {
        $this->prepareChartData();
    }

    public function prepareChartData()
    {
        if ($this->chartType === 'pie') {
            $this->preparePieChartData();
            return;
        }
    
        // Logic for bar chart (unchanged)
        if (empty($this->xAxis) || empty($this->yAxis)) {
            session()->flash('error', 'Invalid axis selection.');
            return;
        }
    
        // Extract labels and values for bar chart
        $labels = array_map(fn($item) => $item[$this->xAxis] ?? null, $this->data);
        $values = array_map(fn($item) => (int) $item[$this->yAxis] ?? 0, $this->data);
    
        // Ensure valid labels and values
        $labels = array_filter($labels); // Remove nulls
        $values = array_filter($values, fn($value) => $value !== null); // Remove nulls
    
        $backgroundColors = $this->generateColors(count($values));
        $borderColors = array_map(fn($color) => str_replace('0.2', '1', $color), $backgroundColors);
    
        // Build chart data for bar chart
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
    }

   
    public function render()
    {
        return view('livewire.chart-display', [
            'availableColumns' => $this->headers // Pass headers to the view
        ]);
    }
}