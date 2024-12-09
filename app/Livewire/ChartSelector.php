<?php

namespace App\Livewire;

use Livewire\Component;

class ChartSelector extends Component
{
    public $isOpen = false;
    public $selectedChart = null;

    public function mount()
    {
        logger('ChartSelector mounted on: ' . request()->url());
    $this->selectedChart = null;
    }

    public function loadChartData($data = [])
    {
        $this->isOpen = true;
        logger('Chart selector opened via Livewire dispatch');
    }

    public function selectChart($type)
    {
        $this->selectedChart = $type;
        logger('Chart selected: ' . $type);
    }

    public function proceed()
    {
        if (!$this->selectedChart) {
            return;
        }
        
        return redirect()->route('project')->with([
            'selectedChart' => $this->selectedChart
        ]);
    }

    public function close()
    {
        $this->isOpen = false;
        $this->selectedChart = null;
        logger('Modal closed');
    }

    public function render()
    {
        logger('Rendering with selected chart: ' . $this->selectedChart);
        return view('livewire.chart-selector');
    }
}