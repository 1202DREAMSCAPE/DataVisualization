<?php

namespace App\Services;

use Google\Cloud\AIPlatform\V1\Client\PredictionServiceClient;
use Google\Cloud\AIPlatform\V1\PredictRequest;
use Google\Cloud\AIPlatform\V1\Instance;
use Google\Auth\ApplicationDefaultCredentials;
use Google\Protobuf\Value;
use Google\Protobuf\Struct;


class GoogleGeminiAgentService
{
    protected $client;
    protected $projectId;
    protected $location;
    protected $agentId;


    public function __construct()
    {
        // Access the environment variables
        $this->projectId = env('GEMINI_PROJECT_ID'); // from .env file
        $this->location = env('GEMINI_LOCATION', 'global'); // default to 'global' if not set
        $this->agentId = env('GEMINI_AGENT_ID'); // from .env file

        // Initialize the PredictionServiceClient
        $this->client = new PredictionServiceClient([
            'credentialsConfig' => [
                'keyFilePath' => env('GOOGLE_APPLICATION_CREDENTIALS'), // path to your service account JSON key
            ],
        ]);
    }
    public function generateCsvData($prompt)
    {
        // Define the endpoint
        $endpoint = sprintf(
            'projects/%s/locations/%s/endpoints/%s',
            $this->projectId, // from env()
            $this->location,  // from env()
            $this->agentId    // from env()
        );

        // Create the input as a Protobuf Value
        $inputValue = new Value();
        $inputValue->setStructValue(
            (new Struct())->setFields([
                'input' => (new Value())->setStringValue($prompt),
            ])
        );

        // Create a PredictRequest
        $request = new PredictRequest();
        $request->setEndpoint($endpoint); // Set the correctly formatted endpoint
        $request->setInstances([$inputValue]); // Instances must be an array of Value objects

        try {
            // Call the predict method
            $response = $this->client->predict($request);
            $predictions = $response->getPredictions();

            // Parse predictions into an array
            $data = [];
            foreach ($predictions as $prediction) {
                $fields = $prediction->getStructValue()->getFields();
                $row = [];
                foreach ($fields as $key => $value) {
                    $row[$key] = $value->getStringValue();
                }
                $data[] = $row;
            }
            return $data;
        } catch (\Exception $e) {
            throw new \Exception('Prediction failed: ' . $e->getMessage());
        }
    }
    
    
}
