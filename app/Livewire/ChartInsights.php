<?php

namespace App\Livewire;

use Livewire\Component;

class ChartInsights extends Component
{
    public $chart;
    public $insights = 'No insights generated yet.';

    public function generateInsights()
    {
        $response = Http::post('/generate-insights', [
            'chart_data' => $this->chart['data'], 
            'chart_type' => $this->chart['type'],
            'chart_title' => $this->chart['title'],
        ]);

        if ($response->successful()) {
            $this->insights = $response->json()['insights'];
        } else {
            $this->insights = 'Failed to generate insights.';
        }
    }

    public function render()
    {
        return view('livewire.chart-insights');
    }
}
