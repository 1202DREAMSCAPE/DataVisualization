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
}
