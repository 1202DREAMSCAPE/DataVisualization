<?php

namespace App\Http\Livewire;

use Livewire\Component;

class ChartBuilder extends Component
{
    public $headers = []; // Array of data headers
    public $data = [];    // Chart data
    public $chartType; // Selected chart type
    public $xAxis = null;     // X-Axis selection
    public $yAxis = null;     // Y-Axis selection
    public $selectedCategories = []; // Selected categories for radar/polarArea
    public $selectedMetric = null;   // Selected metric for radialBar
    public $chartData = [];          // Data sent to ApexCharts

    public function mount($headers = [], $data = [], $chartType = null)
    {
        $this->headers = $headers;
        $this->data = $data;
        $this->chartType = 'bar';  // Set chart type passed from the controller
        $this->prepareChartData();
    }

    public function selectChartType($type)
    {
        $this->chartType = $type;
        $this->prepareChartData();
    }

    public function updatedXAxis()
    {
        $this->prepareChartData();
    }

    public function updatedYAxis()
    {
        $this->prepareChartData();
    }

    public function updatedSelectedCategories()
    {
        $this->prepareChartData();
    }

    public function updatedSelectedMetric()
    {
        $this->prepareChartData();
    }

    private function prepareChartData()
    {
        // Ensure a chart type is selected
        if (!$this->chartType) return;

        // Prepare data based on the selected chart type
        switch ($this->chartType) {
            case 'bar':
            case 'line':
                $this->prepareBarOrLineChartData();
                break;
            case 'pie':
                $this->preparePieChartData();
                break;
            case 'radar':
                $this->prepareRadarChartData();
                break;
            case 'polarArea':
                $this->preparePolarAreaChartData();
                break;
            case 'radialBar': // For Gauge charts
                $this->prepareRadialBarChartData();
                break;
        }

        // Dispatch the chart data to the frontend
        $this->dispatch('update-chart', [
            'chart' => [
                'type' => $this->chartType,
            ],
            'series' => $this->chartData['series'] ?? [],
            'xaxis' => $this->chartData['xaxis'] ?? [],
            'labels' => $this->chartData['labels'] ?? [],
            'options' => $this->chartData['options'] ?? [],
        ]);
    }

    private function prepareBarOrLineChartData()
    {
        if (!$this->xAxis || !$this->yAxis) return;

        $labels = array_column($this->data, $this->xAxis);
        $values = array_column($this->data, $this->yAxis);

        $this->chartData = [
            'series' => [[
                'name' => $this->headers[$this->yAxis] ?? 'Dataset',
                'data' => $values,
            ]],
            'xaxis' => ['categories' => $labels],
            'options' => [
                'title' => [
                    'text' => ucfirst($this->chartType) . ' Chart',
                    'align' => 'center',
                ],
            ],
        ];
    }

    private function preparePieChartData()
    {
        if (!$this->xAxis) return;

        $values = array_count_values(array_column($this->data, $this->xAxis));

        $this->chartData = [
            'series' => array_values($values),
            'labels' => array_keys($values),
            'options' => [
                'title' => [
                    'text' => 'Pie Chart',
                    'align' => 'center',
                ],
            ],
        ];
    }

    private function prepareRadarChartData()
    {
        if (empty($this->selectedCategories)) return;

        $datasets = array_map(function ($category) {
            return [
                'name' => $this->headers[$category] ?? 'Category',
                'data' => array_column($this->data, $category),
            ];
        }, $this->selectedCategories);

        $this->chartData = [
            'series' => $datasets,
            'labels' => array_keys($this->headers),
            'options' => [
                'title' => [
                    'text' => 'Radar Chart',
                    'align' => 'center',
                ],
            ],
        ];
    }

    private function preparePolarAreaChartData()
    {
        if (empty($this->selectedCategories)) return;

        $values = array_map(function ($category) {
            return array_sum(array_column($this->data, $category));
        }, $this->selectedCategories);

        $this->chartData = [
            'series' => $values,
            'labels' => array_map(fn($key) => $this->headers[$key] ?? 'Category', $this->selectedCategories),
            'options' => [
                'title' => [
                    'text' => 'Polar Area Chart',
                    'align' => 'center',
                ],
            ],
        ];
    }

    private function prepareRadialBarChartData()
    {
        if (!$this->selectedMetric) return;

        $value = array_sum(array_column($this->data, $this->selectedMetric)) / count($this->data);

        $this->chartData = [
            'series' => [$value],
            'labels' => [$this->headers[$this->selectedMetric] ?? 'Metric'],
            'options' => [
                'title' => [
                    'text' => 'Gauge Chart',
                    'align' => 'center',
                ],
            ],
        ];
    }

    public function render()
    {
        return view('livewire.chart-builder');
    }
}
