<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\HuggingFaceService;
use Livewire\Livewire;

class HuggingFaceController extends Controller
{
    protected $huggingFaceService;

    public function __construct(HuggingFaceService $huggingFaceService)
    {
        $this->huggingFaceService = $huggingFaceService;
    }

    public function generateText(Request $request)
{
    $request->validate([
        'prompt' => 'required|string|max:255',
    ]);

    try {
        $prompt = $request->input('prompt');
        $response = $this->huggingFaceService->generateText($prompt);

        $rows = explode("\n", trim($response[0]['generated_text'] ?? ''));
        $headers = str_getcsv(array_shift($rows)); // First row as headers
        $data = array_map('str_getcsv', $rows); // Remaining rows as table data

        return redirect()->route('generate-csv')
                         ->with('headers', $headers)
                         ->with('generatedData', $data);
    } catch (\Exception $e) {
        return back()->withErrors(['error' => $e->getMessage()]);
    }
}
}
