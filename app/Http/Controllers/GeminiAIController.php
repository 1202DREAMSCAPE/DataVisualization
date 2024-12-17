<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Gemini\Laravel\Facades\Gemini;
use Illuminate\Support\Facades\Log;

class GeminiAIController extends Controller
{
    public function respond(Request $request)
    {
        $request->validate([
            'input' => 'required|string|max:1000',
        ]);

        $userInput = $request->input('input');

        // Log user input
        Log::info('Gemini AI Request:', ['input' => $userInput]);

        try {
            $result = Gemini::geminiPro()->generateContent($userInput);
            $aiResponse = $result->text();

            // Log AI response
            Log::info('Gemini AI Response:', ['response' => $aiResponse]);

            return response()->json([
                'success' => true,
                'response' => $aiResponse,
            ]);
        } catch (\Exception $e) {
            // Log the error
            Log::error('Gemini AI Error:', ['error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while processing your request.',
            ], 500);
        }
    }
}
