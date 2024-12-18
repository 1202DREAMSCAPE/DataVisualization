<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;


class ChartController extends Controller
{
//     public function customize($type)
//     {
//         $headers = session('headers', []);
//         $previewData = session('previewData', []);
//         $chartType = $type;
        
//         // Here, render a view that allows the user to map their data columns to the chart configuration
//         return view('chart-customize', compact('headers', 'previewData', 'chartType'));
//     }
    public function index(Request $request)
    {
        // Check if session data exists
        $cleanedData = session('cleaned_file', []);
        $headers = session('headers', []);
        $chartType = session('chartType', '');

            // Debugging: Log session data
    // logger('Session Data Frm Controller:', [
    //     'data' => $cleanedData,
    //     'headers' => $headers,
    //     'chartType' => $chartType,
    // ]);
    
        // Debugging: Check session data    
        return view('buildchart', compact( 'headers', 'cleanedData','chartType'));
    }
    
}