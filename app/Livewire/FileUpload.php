<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\IOFactory;
use App\Models\FileRecord;
use App\Services\GoogleGeminiAgentService;


class FileUpload extends Component
{
    use WithFileUploads;

    public $file;
    public $filename;
    public $headers = [];
    public $previewData = [];
    public $isChartSelectorOpen = false;
    public $generatedFilePath; // Add this property to avoid the undefined property error
    public $prompt; // Add this to manage the input prompt

    protected $listeners = ['closeChartSelector' => 'closeChartSelector', 'openGeminiModal' => 'show'];
    public $isModalOpen = false; // To control modal visibility

    public function show()
    {
        $this->isModalOpen = true; // Open the modal
    }

    public function updatedFile()
    {
        logger('File updated', ['file' => $this->file]);

        $this->validate([
            'file' => 'required|mimes:csv,xlsx|max:10240',
        ]);

        $this->filename = $this->file->getClientOriginalName();
        logger('File validated and uploaded', ['filename' => $this->filename]);

        $path = $this->file->store('temp');
        logger('File stored temporarily', ['path' => $path]);

        $this->loadExcelData($path);

        // Save file metadata to the database
        FileRecord::create([
            'filename' => $this->filename,
            'path' => $path,
            'headers' => $this->headers,
            'preview_data' => $this->previewData,
        ]);

        logger('File metadata saved to database.');

        // Save recently used file in session
        session([
            'recently_used_file' => [
                'filename' => $this->filename,
                'headers' => $this->headers,
                'previewData' => $this->previewData,
            ],
        ]);

        logger('Recently used file saved to session.');
    }

        public function openModal()
    {
        $this->dispatch('openGeminiModal'); // Emit an event to open the modal
    }


    public function generateCsvFile()
    {
        try {
            logger('Generating CSV file via Gemini model.');

            $geminiService = new GoogleGeminiAgentService();

            // Ensure that $this->prompt is set
            if (empty($this->prompt)) {
                session()->flash('error', 'The prompt is required to generate a CSV file.');
                return;
            }

            // Pass the prompt to the service method
            $generatedData = $geminiService->generateCsvData($this->prompt);

            // Convert the data into CSV format
            $csvContent = fopen('php://temp', 'r+');
            foreach ($generatedData as $row) {
                fputcsv($csvContent, $row);
            }
            rewind($csvContent);

            $directoryPath = storage_path('app/generated');
            if (!is_dir($directoryPath)) {
                mkdir($directoryPath, 0755, true);
            }

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

    /**
     * Validate and process the uploaded file.
     */
    // public function updatedFile()
    // {
    //     logger('File updated', ['file' => $this->file]);

    //     $this->validate([
    //         'file' => 'required|mimes:csv,xlsx|max:10240',
    //     ]);

    //     $this->filename = $this->file->getClientOriginalName();
    //     logger('File validated and uploaded', ['filename' => $this->filename]);

    //     $path = $this->file->store('temp');
    //     logger('File stored temporarily', ['path' => $path]);

    //     $this->loadExcelData($path);
    // }

    /**
     * Extract headers and preview data from the uploaded file.
     */
    public function loadExcelData($path)
    {
        $fullPath = Storage::path($path);
        logger('Loading Excel data from path', ['fullPath' => $fullPath]);
    
        try {
            $spreadsheet = IOFactory::load($fullPath);
            $sheet = $spreadsheet->getActiveSheet();
            $data = $sheet->toArray(null, true, true, true);
    
            $this->headers = array_shift($data);
            $this->previewData = $data;
    
            logger('Headers and preview data extracted.', [
                'headers' => $this->headers,
                'previewData' => $this->previewData,
            ]);
        } catch (\Exception $e) {
            logger('Error loading Excel data.', ['message' => $e->getMessage()]);
            session()->flash('error', 'Failed to load the file. Please ensure it is a valid CSV or XLSX file.');
        } finally {
            // Cleanup temporary file
            Storage::delete($path);
            logger('Temporary file deleted after processing.', ['path' => $path]);
        }
    }
    


    public function uploadRecentlyUsedFile()
{
    // Check if a recently used file exists in the session
    if (session()->has('recently_used_file')) {
        $recentFile = session('recently_used_file');

        $this->filename = $recentFile['filename'];
        $this->headers = $recentFile['headers'];
        $this->previewData = $recentFile['previewData'];

        logger('Recently used file loaded successfully.', [
            'filename' => $this->filename,
            'headers' => $this->headers,
            'previewData' => $this->previewData,
        ]);

        session()->flash('success', 'Recently used file loaded successfully.');
    } else {
        logger('No recently used file found in the session.');
        session()->flash('error', 'No recently used file found.');
    }
}


    /**
     * Open the Chart Selector modal and pass data.
     */
    public function openChartSelector()
    {
        logger('openChartSelector triggered.', [
            'headers' => $this->headers,
            'previewData' => $this->previewData,
        ]);
    
        if (empty($this->headers) || empty($this->previewData)) {
            session()->flash('error', 'Upload a valid file before starting visualization.');
            logger('Failed to open Chart Selector: Missing headers or data.');
            return;
        }
    
        // Validate headers and previewData
        if (!is_array($this->headers) || !is_array($this->previewData)) {
            session()->flash('error', 'Invalid file structure.');
            logger('Invalid file structure: headers or previewData is not an array.');
            return;
        }
    
        $this->dispatch('open-chart-selector', headers: $this->headers, previewData: $this->previewData);
    
        logger('Chart Selector opened.', [
            'headers' => $this->headers,
            'previewData' => $this->previewData,
        ]);
    }
    

    /**
     * Close the Chart Selector modal.
     */
    public function closeChartSelector()
    {
        $this->isChartSelectorOpen = false;
        logger('Chart Selector closed');
    }

    public function render()
    {
        logger('Rendering FileUpload component', [
            'filename' => $this->filename,
            'isChartSelectorOpen' => $this->isChartSelectorOpen,
        ]);

        return view('livewire.file-upload');
    }
}
