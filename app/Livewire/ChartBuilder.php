<?php

namespace App\Livewire;

use Livewire\Component;

class ChartBuilder extends Component
{
    public $headers = [];
    public $data = [];
    public $chartType = '';
    public $xAxis = null;
    public $yAxis = null;
    public $selectedCategories = [];
    public $selectedMetric = null;
    public $chartData = [];
    public $id; // Unique ID for charts

    public function mount($headers = [], $data = [], $chartType = null)
    {
        $this->headers = $headers;
        $this->data = $data;
        $this->chartType = $chartType ?: '';
        $this->id = uniqid(); // Generate a unique ID for the chart
        $this->prepareChartData();
    }

    public function selectChartType($type)
    {
        $this->chartType = $type;
        $this->prepareChartData();
    }

    public function updated($property)
    {
        if ($this->chartType && (
            ($this->chartType === 'pie' && $this->xAxis) ||
            (in_array($this->chartType, ['bar', 'line']) && $this->xAxis && $this->yAxis) ||
            ($this->chartType === 'radialBar' && $this->selectedMetric) ||
            (in_array($this->chartType, ['radar', 'polarArea']) && !empty($this->selectedCategories))
        )) {
            $this->prepareChartData();
        }
    }
    


private function prepareChartData()
{
    if (!$this->chartType) return;

    // Validate required fields based on chart type
    if (in_array($this->chartType, ['bar', 'line']) && (!$this->xAxis || !$this->yAxis)) {
        return; // Wait until both X and Y axes are selected
    }

    if ($this->chartType === 'pie' && !$this->xAxis) {
        return; // Wait until X-Axis is selected
    }

    if ($this->chartType === 'radialBar' && !$this->selectedMetric) {
        return; // Wait until metric is selected
    }

    if (in_array($this->chartType, ['radar', 'polarArea']) && empty($this->selectedCategories)) {
        return; // Wait until categories are selected
    }

    // Prepare the chart data based on the chart type
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
        case 'radialBar':
            $this->prepareRadialBarChartData();
            break;
    }

    // Dispatch the event only if chart data is valid
    if (!empty($this->chartData['series'])) {
        $this->dispatch('update-chart', [
            'chart' => ['type' => $this->chartType],
            'series' => $this->chartData['series'],
            'xaxis' => $this->chartData['xaxis'] ?? [],
            'labels' => $this->chartData['labels'] ?? [],
            'options' => $this->chartData['options'] ?? [],
            'id' => $this->id,
        ]);
    }
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
            'labels' => array_intersect_key($this->headers, array_flip($this->selectedCategories)),
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
