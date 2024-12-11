<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use OpenAI;

class AiInsightsController extends Controller
{
    public function generateInsights(Request $request)
    {
        $chartData = $request->input('chart_data');
        $chartType = $request->input('chart_type', 'Unknown');
        $chartTitle = $request->input('chart_title', 'Untitled Chart');

        if (empty($chartData) || !is_array($chartData)) {
            return response()->json(['error' => 'Invalid or missing chart data'], 400);
        }

        try {
            $client = OpenAI::client('');

            // Build the prompt
            $prompt = "Analyze the following chart data and provide meaningful insights. The data is formatted as follows:\n" .
                      "Chart Type: $chartType\n" .
                      "Chart Title: $chartTitle\n" .
                      "Data: " . json_encode($chartData) . "\n\n" .
                      "Please respond with insights in a clear and concise paragraph.";

            // Call OpenAI API
            $response = $client->completions()->create([
                'model' => 'text-davinci-003',  // You can use 'gpt-4' if available
                'prompt' => $prompt,
                'max_tokens' => 150,
                'temperature' => 0.7,
            ]);

            // Extract the generated insights
            $insights = $response['choices'][0]['text'] ?? 'No insights generated.';

            return response()->json(['insights' => trim($insights)], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
