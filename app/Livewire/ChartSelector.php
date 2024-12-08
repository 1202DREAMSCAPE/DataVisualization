<?php

namespace App\Livewire;

use Livewire\Component;

class ChartSelector extends Component
{
    public $isOpen = false;
    public $headers = [];
    public $previewData = [];
    public $selectedChart = null;

    protected $listeners = [
        'open-chart-selector' => 'loadChartData',
        'close-chart-selector' => 'close',
    ];

    public function loadChartData(array $headers, array $previewData)
    {
        $this->headers = $headers;
        $this->previewData = $previewData;
        $this->isOpen = true;

        logger('ChartSelector: Data loaded', [
            'headers' => $this->headers,
            'previewData' => $this->previewData,
        ]);
    }

    public function selectChart($chartType)
    {
        $this->selectedChart = $chartType;

        logger('ChartSelector: Chart selected.', [
            'selectedChart' => $this->selectedChart,
        ]);
    }

    public function proceed()
    {
        if (!$this->selectedChart) {
            session()->flash('error', 'Please select a chart type before proceeding.');
            logger('ChartSelector: Proceed failed. No chart selected.');
            return;
        }

        if (empty($this->headers) || empty($this->previewData)) {
            session()->flash('error', 'Missing data for chart visualization.');
            logger('ChartSelector: Proceed failed. Headers or preview data missing.', [
                'headers' => $this->headers,
                'previewData' => $this->previewData,
            ]);
            return;
        }

        session([
            'chartType' => $this->selectedChart,
            'headers' => $this->headers,
            'previewData' => $this->previewData,
        ]);

        logger('ChartSelector: Proceeding with chart.', [
            'selectedChart' => $this->selectedChart,
            'headers' => $this->headers,
            'previewData' => $this->previewData,
        ]);

        $this->redirect(route('chart.customize', $this->selectedChart));
    }

    public function close()
    {
        $this->isOpen = false;
        logger('ChartSelector: Modal closed.', [
            'isOpen' => $this->isOpen,
        ]);
    }

    public function render()
    {
        logger('Rendering ChartSelector component.', [
            'isOpen' => $this->isOpen,
            'selectedChart' => $this->selectedChart,
        ]);

        return view('livewire.chart-selector');
    }
}
