<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ChartController extends Controller
{
    public function customize($type)
    {
        $headers = session('headers', []);
        $previewData = session('previewData', []);
        $chartType = $type;
        
        // Here, render a view that allows the user to map their data columns to the chart configuration
        return view('chart-customize', compact('headers', 'previewData', 'chartType'));
    }
    public function index(Request $request)
    {
        // Pass cleaned data and headers from session or other storage
        $cleanedData = session('cleanedData', []);
        $headers = session('headers', []);
        $chartType = session('chartType', 'bar'); // Default to 'bar' if not set in the session

        return view('buildchart', compact('cleanedData', 'headers'));
    }
}
