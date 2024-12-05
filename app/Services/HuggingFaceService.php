<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class HuggingFaceService
{
    private $apiUrl = 'https://api-inference.huggingface.co/models';

    public function generateText(string $prompt, string $model = 'gpt2')
    {
        // Make the API request to Hugging Face
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . env('HUGGINGFACE_API_KEY'),
        ])->post("{$this->apiUrl}/{$model}", [
            'inputs' => $prompt,
            'parameters' => [
                'max_length' => 200, // Limit the length of the generated text
                'temperature' => 0.7, // Adjust temperature for randomness
            ],
        ]);

        if ($response->failed()) {
            throw new \Exception('Hugging Face API request failed: ' . $response->body());
        }

        // Return the generated response
        return $response->json();
    }
}
