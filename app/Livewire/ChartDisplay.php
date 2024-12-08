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
    if (empty($this->xAxis) || empty($this->yAxis)) {
        session()->flash('error', 'Invalid axis selection.');
        return;
    }

    // Extract labels and values
    $labels = array_map(fn($item) => $item[$this->xAxis] ?? null, $this->data);
    $values = array_map(fn($item) => (int) $item[$this->yAxis] ?? 0, $this->data);

    // Ensure valid labels and values
    $labels = array_filter($labels); // Remove nulls
    $values = array_filter($values, fn($value) => $value !== null); // Remove nulls

    // Check for empty labels or values
    if (empty($labels) || empty($values)) {
        session()->flash('error', 'No valid data for chart.');
        logger()->error('Empty labels or values.', ['labels' => $labels, 'values' => $values]);
        return;
    }

    $backgroundColors = $this->generateColors(count($values));
    $borderColors = array_map(fn($color) => str_replace('0.2', '1', $color), $backgroundColors);


    // Build chart data
    $this->chartData = [
        'type' => $this->chartType, // e.g., 'bar', 'line', etc.
        'data' => [
            'labels' => array_values($labels), // Ensure zero-based index
            'datasets' => [
                [
                    'label' => $this->headers[$this->yAxis] ?? 'Dynamic Dataset', // Use Y-axis header as dataset label
                    'data' => array_values($values), // Ensure zero-based index
                    'backgroundColor' => $backgroundColors,
                    'borderColor' => $borderColors,
                    'borderWidth' => 1,
                ],
            ],
        ],
    ];


    // Update previewData for the table
    logger()->info('Dispatching hardcoded chartData:', $this->chartData);
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
        return view('livewire.chart-display');
    }
}
