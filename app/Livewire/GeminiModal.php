<?php 
namespace App\Livewire;

use Livewire\Component;
use App\Services\GoogleGeminiAgentService;
use Illuminate\Support\Facades\Storage;
use Google\Protobuf\Struct;
use Google\Protobuf\Value;
use Google\Cloud\AIPlatform\V1\PredictRequest;

class GeminiModal extends Component
{
    public $prompt;
    public $generatedFilePath;
    protected $listeners = ['openGeminiModal' => 'show'];
    public $isModalOpen = false;

    public function show()
    {
        $this->isModalOpen = true;
    }

    public function close()
    {
        $this->isModalOpen = false;
    }

    public function generateCsvFile()
    {
        try {
            $service = new GoogleGeminiAgentService();

            if (empty($this->prompt)) {
                session()->flash('error', 'The prompt is required to generate a CSV file.');
                return;
            }

            $data = $service->generateCsvData($this->prompt);

            $csvContent = fopen('php://temp', 'r+');
            foreach ($data as $row) {
                fputcsv($csvContent, $row);
            }
            rewind($csvContent);

            $csvFileName = 'generated_file_' . time() . '.csv';
            $filePath = 'generated/' . $csvFileName;

            Storage::disk('local')->put($filePath, stream_get_contents($csvContent));
            fclose($csvContent);

            $this->generatedFilePath = $filePath;

            session()->flash('success', "CSV file generated successfully! Click below to download: $csvFileName");
        } catch (\Exception $e) {
            session()->flash('error', 'Error generating CSV file: ' . $e->getMessage());
        }
    }

    public function downloadCsvFile()
    {
        if ($this->generatedFilePath && Storage::exists($this->generatedFilePath)) {
            return Storage::download($this->generatedFilePath);
        }
        session()->flash('error', 'File not found or already deleted.');
    }

    public function render()
    {
        return view('livewire.gemini-modal');
    }
}
