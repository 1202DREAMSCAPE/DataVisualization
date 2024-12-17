<?php 
namespace App\Http\Controllers;

use App\Models\Report;
use Illuminate\Support\Facades\Auth;
use Barryvdh\Snappy\Facades\SnappyPdf;
use Illuminate\Http\Request;


class ReportController extends Controller
{
    public function showSavedReports()
    {
        // Fetch reports for the currently logged-in user
        $reports = Report::where('user_id', Auth::id())->get();

        // Pass the reports to the Blade view
        return view('savegenreports', compact('reports'));
    }

    public function deleteReport($id)
    {
        // Find the report by ID and ensure it belongs to the authenticated user
        $report = Report::where('id', $id)->where('user_id', Auth::id())->first();

        if (!$report) {
            return redirect()->back()->with('error', 'Report not found or unauthorized action.');
        }

        // Delete the file from storage
        if (file_exists(public_path($report->file_path))) {
            unlink(public_path($report->file_path));
        }

        // Delete the report record from the database
        $report->delete();

        return redirect()->back()->with('success', 'Report deleted successfully.');
    }

    public function generatePdf(Request $request)
    {
        // Validate input
        $validated = $request->validate([
            'chart_title' => 'required|string|max:255',
            'chart_remarks' => 'required|string',
            'filename' => 'required|string|max:255',
            'xAxis' => 'nullable|string',
            'yAxis' => 'nullable|string',
            'chart_image' => 'required|string',
        ]);
    
        // Decode the chart image
        $chartImagePath = storage_path('app/public/reports/chart_' . time() . '.png');
        $chartImageData = explode(',', $validated['chart_image'])[1]; // Remove Base64 prefix
        file_put_contents($chartImagePath, base64_decode($chartImageData));
    
        // Prepare the HTML content for the PDF
        $chartHtml = '<html>
            <head>
                <style>
                    body {
                        font-family: Arial, sans-serif;
                        margin: 0;
                        padding: 0;
                        color: #333;
                    }
                    .header {
                        text-align: center;
                        background-color: #FFFFFF;
                        padding: 20px;
                    }
                    .header img {
                        max-height: 80px;
                    }
                    .container {
                        padding: 20px;
                        margin: 0 auto;
                        max-width: 800px;
                        border: 1px solid #ddd;
                        border-radius: 10px;
                        background-color: #fff;
                    }
                    .title {
                        text-align: center;
                        font-size: 24px;
                        font-weight: bold;
                        margin-bottom: 20px;
                        color: #2c3e50;
                    }
                    .remarks, .metadata {
                        font-size: 16px;
                        color: #7f8c8d;
                        margin-top: 20px;
                        padding: 10px;
                        border-left: 4px solid #3498db;
                        background-color: #ecf6fc;
                    }
                    .metadata {
                        margin-top: 10px;
                        border-left-color: #e74c3c;
                    }
                    .chart img {
                        display: block;
                        margin: 0 auto;
                        max-width: 100%;
                    }
                </style>
            </head>
            <body>
                <div class="header">
                    <img src="' . public_path('images/VizOraLogo.png') . '" alt="VizOra Logo">
                </div>
                <div class="container">
                    <div class="title">' . htmlspecialchars($validated['chart_title']) . '</div>
                    <div class="chart"><img src="' . $chartImagePath . '" alt="Chart"></div>
                    <div class="metadata">
                        <p><strong>Filename:</strong> ' . htmlspecialchars($validated['filename']) . '</p>
                        <p><strong>X-Axis:</strong> ' . htmlspecialchars($validated['xAxis'] ?? 'Not specified') . '</p>
                        <p><strong>Y-Axis:</strong> ' . htmlspecialchars($validated['yAxis'] ?? 'Not specified') . '</p>
                    </div>
                    <div class="remarks">' . nl2br(htmlspecialchars($validated['chart_remarks'])) . '</div>
                </div>
            </body>
        </html>';
    
        // Generate PDF using Snappy
        $pdf = SnappyPdf::loadHTML($chartHtml)
            ->setOption('enable-local-file-access', true);
    
        // Define file name and path
        $fileName = 'VizOraReport_' . $validated['chart_title'] . '_' . now()->format('Y-m-d') . '.pdf';
        $filePath = storage_path('app/public/reports/' . $fileName);
    
        // Save PDF to storage
        $pdf->save($filePath);

        // Save report to the database
        $report = Report::create([
            'user_id' => Auth::id(),
            'report_name' => $validated['chart_title'],
            'file_path' => 'storage/reports/' . $fileName,
            'remarks' => $validated['chart_remarks'], // Ensure the database has this column
        ]);
    
        // Return the PDF for download
        return $pdf->download($fileName);
    }
    
    
}
