<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\GoogleSheetsService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class GoogleSheetsController extends Controller
{
    private $sheetsService;

    public function __construct(GoogleSheetsService $sheetsService)
    {
        $this->sheetsService = $sheetsService;
    }

    public function generateAndAppendData(Request $request)
    {
        $request->validate([
            'prompt' => 'required|string|max:255',
        ]);

        $prompt = $request->input('prompt');

        // Call OpenAI API
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . env('OPENAI_API_KEY'),
        ])->post('https://api.openai.com/v1/chat/completions', [
            'model' => 'gpt-3.5-turbo',
            'messages' => [
                ['role' => 'system', 'content' => 'You are a helpful assistant.'],
                ['role' => 'user', 'content' => $prompt],
            ],
        ]);

        $responseData = json_decode($response->body(), true);
        $csvData = explode("\n", trim($responseData['choices'][0]['message']['content']));

        $headers = str_getcsv(array_shift($csvData)); // Extract headers
        $rows = array_map('str_getcsv', $csvData); // Extract rows

        // Save to a CSV file
        $filePath = 'generated_data.csv';
        Storage::disk('local')->put($filePath, $this->convertToCSV($headers, $rows));

        return view('your-view-name', [
            'headers' => $headers,
            'generatedData' => $rows,
        ]);
    }

    public function downloadGeneratedData()
    {
        $filePath = 'generated_data.csv';

        if (Storage::disk('local')->exists($filePath)) {
            return response()->download(storage_path("app/{$filePath}"))->deleteFileAfterSend();
        }

        return redirect()->back()->withErrors(['file' => 'No generated data to download.']);
    }

    private function convertToCSV(array $headers, array $rows)
    {
        $output = fopen('php://temp', 'r+');
        fputcsv($output, $headers);
        foreach ($rows as $row) {
            fputcsv($output, $row);
        }
        rewind($output);

        return stream_get_contents($output);
    }
}
