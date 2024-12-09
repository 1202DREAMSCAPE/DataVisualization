<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SavedChart;

class ChartCustomizeController extends Controller
{
    public function saveChart(Request $request)
{
    $validatedData = $request->validate([
        'title' => 'required|string|max:255',
        'chart_data' => 'required|array'
    ]);

    $chart = SavedChart::create([
        'title' => $validatedData['title'],
        'chart_data' => $validatedData['chart_data'],
        'user_id' => auth()->id()
    ]);

    return response()->json(['success' => true, 'message' => 'Chart saved successfully']);
}

    public function customize($type)
    {
        $chartType = session('chartType', $type);
        $headers = session('headers', []);
        $previewData = session('previewData', []);

        logger("ChartCustomizeController: Retrieved chartType - {$chartType}");
        logger("Headers:", $headers);
        logger("Preview Data:", $previewData);

        // Fetch saved charts (example: stored in a session or database)
        $savedCharts = session('savedCharts', []); // If using session

        // Example if fetching from a database:
        // $savedCharts = SavedChartModel::all()->toArray();

        if (empty($chartType) || empty($headers) || empty($previewData)) {
            logger("Error: Missing chart data for customization.");
            return redirect()->route('project')
                ->with('error', 'Missing data for chart customization. Please try again.');
        }

        return view('chart-customize', compact('chartType', 'headers', 'previewData', 'savedCharts'));
    }
}
