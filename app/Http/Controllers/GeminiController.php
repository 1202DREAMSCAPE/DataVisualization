<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Gemini\Laravel\Facades\Gemini;
use Illuminate\Support\Facades\Log;

class GeminiController extends Controller
{
    public function respond(Request $request)
    {
        // Validate the input
        $request->validate([
            'input' => 'required|string',
            'context' => 'nullable|array', // Optional history/context parameter
        ]);

        $userInput = $request->input('input');
        $context = $request->input('context', []); // Context/history for advanced chat interactions

        try {
            Log::info("User input: $userInput");
            
            // If context is provided, use chat API; otherwise, use content generation
            if (!empty($context)) {
                // Example of using Gemini's chat features
                $chat = Gemini::chat()->startChat(history: $context);

                $response = $chat->sendMessage($userInput);

                $output = $response->text();
            } else {
                // Example of using Gemini's content generation feature
                $result = Gemini::geminiPro()->generateContent($userInput);

                $output = $result->text();
            }

            Log::info("Gemini response: $output");

            // Return response to the frontend
            return response()->json([
                'success' => true,
                'response' => $output,
            ]);
        } catch (\Exception $e) {
            Log::error("Gemini API error: {$e->getMessage()}");

            return response()->json([
                'success' => false,
                'message' => 'Unable to fetch a response from Gemini AI.',
            ], 500);
        }
    }
}
