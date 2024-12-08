<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ChartCustomizeController extends Controller
{
    public function customize($type)
    {
        $chartType = session('chartType', $type);
        $headers = session('headers', []);
        $previewData = session('previewData', []);

        logger("ChartCustomizeController: Retrieved chartType - {$chartType}");
        logger("Headers:", $headers);
        logger("Preview Data:", $previewData);

        if (empty($chartType) || empty($headers) || empty($previewData)) {
            logger("Error: Missing chart data for customization.");
            return redirect()->route('project')
                ->with('error', 'Missing data for chart customization. Please try again.');
        }

        return view('chart-customize', compact('chartType', 'headers', 'previewData'));
    }
}
