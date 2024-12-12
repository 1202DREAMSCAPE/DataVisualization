<?php

namespace App\Livewire;

use App\Models\SavedChart;
use Livewire\Component;

class SavedCharts extends Component
{
    public $savedCharts = [];
    public $currentHeaders = [];
    public $currentPreviewData = [];
    public $deletingChartId = null;
    public $remarks = []; // To store AI-generated remarks
    public $charts;

    protected $listeners = [
        'refreshCharts' => 'loadCharts',
        'confirmDelete' => 'handleDeleteConfirmation'
    ];

    public function mount()
    {
        $this->currentHeaders = session('headers', []);
        $this->currentPreviewData = session('previewData', []);
        $this->loadCharts();
        $this->charts = SavedChart::all();
    }

    public function loadCharts()
    {
        $this->savedCharts = SavedChart::where(function ($query) {
            $query->where('user_id', auth()->id())
                  ->orWhereNull('user_id');
        })
        ->orderBy('created_at', 'desc')
        ->get()
        ->map(function ($chart) {
            return [
                'id' => $chart->id,
                'title' => $chart->title,
                'data' => $chart->chart_data,
                'remarks'=>$chart->remarks
            ];
        })
        ->toArray();
    }

    public function deleteChart($chartId)
    {
        $chart = SavedChart::find($chartId);

        if ($chart) {
            $chart->delete();
            $this->emit('chartDeleted', $chartId);
        }
    }

    public function saveChart($chartData)
    {
        try {
            $chart = new SavedChart();
            $chart->title = $chartData['title'] ?? 'Untitled Chart';
            $chart->chart_data = json_encode($chartData['data']);
            $chart->user_id = auth()->id();
            $chart->file_record_id = $chartData['filename'];
            $chart->remarks=$chartData['remarks'] ?? 'No Remarks';
            $chart->save();

            $this->loadCharts();

            $this->dispatchBrowserEvent('chartSaved', ['success' => true]);

            session()->flash('message', 'Chart saved successfully.');
        } catch (\Exception $e) {
            \Log::error('Error saving chart: ' . $e->getMessage());
            $this->dispatchBrowserEvent('chartSaved', ['success' => false, 'error' => $e->getMessage()]);

            session()->flash('error', 'Failed to save the chart. Please try again.');
        }
    }

    // New Method: Generate Remarks
    public function generateRemarks($chartId)
    {
        $chart = collect($this->savedCharts)->firstWhere('id', $chartId);

        if ($chart) {
            // Mocked AI Insights (Replace with real AI or API integration)
            $this->remarks[$chartId] = "Insights for chart '{$chart['title']}' have been successfully generated.";
        } else {
            $this->remarks[$chartId] = 'No insights available for this chart.';
        }
    }

    public function createNewChart()
    {
        if (empty($this->currentHeaders) || empty($this->currentPreviewData)) {
            return redirect()->route('project');
        }

        $this->dispatch('open-chart-selector', 
            headers: $this->currentHeaders,
            previewData: $this->currentPreviewData
        );
    }

    public function render()
    {
        return view('livewire.saved-charts');
    }
}
