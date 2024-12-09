<?php

namespace App\Livewire;

use Livewire\Component;

class ChartCustomizer extends Component
{
    public $chartType;
    public $headers = [];
    public $previewData = [];

    public $xAxis;
    public $yAxis;

    public function mount($chartType, $headers, $previewData)
{
    logger("ChartCustomizer Mounted with chartType: {$chartType}");
    logger("Headers:", $headers);
    logger("Preview Data:", $previewData);

    $this->chartType = $chartType;
    $this->headers = $headers;
    $this->previewData = $previewData;
}

    public function generateChart()
    {
        logger("generateChart called with X-Axis: {$this->xAxis}, Y-Axis: {$this->yAxis}");

        $this->headers = array_values($this->headers);
        foreach ($this->previewData as &$row) {
            $row = array_values($row);
        }
        unset($row);

        $xIndex = array_search($this->xAxis, $this->headers);
        $yIndex = array_search($this->yAxis, $this->headers);

        if ($xIndex === false || $yIndex === false) {
            $this->dispatch('alert', ['message' => 'Selected columns not found in data.']);
            return;
        }

        $xValues = [];
        $yValues = [];

        foreach ($this->previewData as $row) {
            if (!array_key_exists($xIndex, $row) || !array_key_exists($yIndex, $row)) {
                continue;
            }

            $xValues[] = $row[$xIndex] ?? null;
            $yValues[] = is_numeric($row[$yIndex]) ? (float)$row[$yIndex] : null;
        }

        if (empty($xValues) || empty($yValues)) {
            $this->dispatch('alert', ['message' => 'Insufficient data to generate chart.']);
            return;
        }

        // Dispatch the data for ECharts
        $this->dispatch('chartDataGenerated', [
            'xValues' => $xValues,
            'yValues' => $yValues,
            'chartType' => $this->chartType
        ]);

        logger("Dispatched chartDataGenerated with data: ", [
            'xValues' => $xValues,
            'yValues' => $yValues,
            'chartType' => $this->chartType
        ]);
        logger("ChartCustomizer: Dispatched chartDataGenerated event with chart type - {$this->chartType}");
    }

    public function render()
    {
        return view('livewire.chart-customizer');
    }
}
