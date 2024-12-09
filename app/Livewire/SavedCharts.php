<?php

namespace App\Livewire;

use App\Models\SavedChart;
use Livewire\Component;
use Illuminate\Support\Facades\DB;

class SavedCharts extends Component
{
    public $savedCharts = [];
    public $currentHeaders = [];
    public $currentPreviewData = [];
    public $deletingChartId = null;

    public $charts;


    protected $listeners = [
        'refreshCharts' => 'loadCharts',
        'confirmDelete' => 'handleDeleteConfirmation'
    ];

    public function index()
{
    $charts = SavedChart::all(); // Fetch all saved charts
    return view('your-view-name', compact('charts')); // Replace 'your-view-name' with your Blade file name
}

    public function mount()
    {
        // Get the headers and preview data from session if available
        $this->currentHeaders = session('headers', []);
        $this->currentPreviewData = session('previewData', []);
        $this->loadCharts();
        $this->charts = SavedChart::all(); // Fetch all charts from the database

    }

    public function deleteChart($chartId)
    {
        $chart = SavedChart::find($chartId);
    
        if ($chart) {
            $chart->delete();
            $this->emit('chartDeleted', $chartId); // Optional: Emit an event if needed
        }
    }
    

    public function loadCharts()
    {
        // Get both user's charts and null user_id charts
        $this->savedCharts = SavedChart::where(function($query) {
            $query->where('user_id', auth()->id())
                  ->orWhereNull('user_id');
        })
        ->orderBy('created_at', 'desc')
        ->get()
        ->map(function($chart) {
            return [
                'id' => $chart->id,
                'title' => $chart->title,
                'data' => $chart->chart_data
            ];
        })
        ->toArray();
    }

    public function saveChart($chartData)
{
    try {
        // Create a new chart entry
        $chart = new SavedChart();
        $chart->title = $chartData['title'] ?? 'Untitled Chart';
        $chart->chart_data = json_encode($chartData['data']);
        $chart->user_id = auth()->id(); // Optional if user authentication is implemented
        $chart->save();

        // Reload charts to include the newly saved chart
        $this->loadCharts();

        // Notify the frontend that the chart was saved
        $this->dispatchBrowserEvent('chartSaved', ['success' => true]);

        session()->flash('message', 'Chart saved successfully.');
    } catch (\Exception $e) {
        \Log::error('Error saving chart: ' . $e->getMessage());
        $this->dispatchBrowserEvent('chartSaved', ['success' => false, 'error' => $e->getMessage()]);

        session()->flash('error', 'Failed to save the chart. Please try again.');
    }
}



    
    public function createNewChart()
    {
        if (empty($this->currentHeaders) || empty($this->currentPreviewData)) {
            // If no data in session, redirect to upload page
            return redirect()->route('project');
        }

        // Dispatch event to open chart selector with current data
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