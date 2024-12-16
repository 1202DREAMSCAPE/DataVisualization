<?php
// app/Http/Controllers/SavedChartsController.php
namespace App\Http\Controllers;

use App\Models\SavedChart;
use Illuminate\Http\Request;

use    public function index()
    {
        // Ensure the user is authenticated
        $this->authorize('viewAny', SavedChart::class);

        // Fetch charts for the authenticated user
        $savedCharts = SavedChart::where('user_id', auth()->id())->get();

        return view('saved-charts.index', compact('savedCharts'));
    }

    public function store(Request $request)
    {
        $this->authorize('create', SavedChart::class);

        $request->validate([
            'title' => 'required|string|max:255',
            'remarks' => 'nullable|string',
            'data' => 'required|array',
        ]);

        SavedChart::create([
            'user_id' => auth()->id(),
            'title' => $request->title,
            'remarks' => $request->remarks,
            'data' => $request->data,
        ]);

        return redirect()->route('saved-charts.index')->with('success', 'Chart saved successfully.');
    }

    public function destroy(SavedChart $savedChart)
    {
        $this->authorize('delete', $savedChart);

        $savedChart->delete();

        return redirect()->route('saved-charts.index')->with('success', 'Chart deleted successfully.');
    }
}

