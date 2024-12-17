<?php

namespace App\Livewire;

use Livewire\Component;

class ChartBuilder extends Component
{
    public $fileName;
    public $headers = [];
    public $data = [];
    public $chartType = null;
    public $xAxis = null;
    public $yAxis = null;
    public $chartData = [];

    public function mount($fileName = null, $headers = [], $data = [])
    {
        $this->fileName = $fileName;
        $this->headers = $headers;
        $this->data = $data;
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

    private function prepareChartData()
    {
        if (!$this->chartType || !$this->xAxis || !$this->yAxis) {
            return;
        }

        $labels = array_column($this->data, $this->xAxis);
        $values = array_column($this->data, $this->yAxis);

        $this->chartData = [
            'type' => $this->chartType,
            'data' => [
                'labels' => $labels,
                'datasets' => [
                    [
                        'label' => $this->headers[$this->yAxis] ?? 'Dataset',
                        'data' => $values,
                        'backgroundColor' => $this->generateColors(count($values)),
                    ]
                ]
            ],
            'options' => [
                'responsive' => true,
                'plugins' => [
                    'legend' => ['position' => 'top'],
                    'title' => ['display' => true, 'text' => ucfirst($this->chartType) . ' Chart'],
                ],
            ],
        ];

        $this->dispatchBrowserEvent('updateChart', $this->chartData);
    }

    private function generateColors($count)
    {
        $colors = [];
        for ($i = 0; $i < $count; $i++) {
            $r = rand(0, 255);
            $g = rand(0, 255);
            $b = rand(0, 255);
            $colors[] = "rgba($r, $g, $b, 0.5)";
        }
        return $colors;
    }

    public function render()
    {
        return view('livewire.chart-builder', [
            'headers' => $this->headers,
        ]);
    }
}
