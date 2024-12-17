<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Chart;
use App\Models\SavedChart;

class ChartRemarksController extends Controller
{
    public function show($chartId)
    {
        $chart = SavedChart::findOrFail($chartId);
       // dd($chart); // This will dump the chart data and stop execution
        return view('remarks', compact('chart'));
    }
}