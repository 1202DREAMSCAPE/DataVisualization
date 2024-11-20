<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\IOFactory;

class FileUpload extends Component
{
    use WithFileUploads;

    public $file;
    public $filename;
    public $headers;
    public $previewData;
    public $cleaningData; // Holds data for the cleaning process
    public $isCleaning = false;

    public function updatedFile()
    {
        $this->validate([
            'file' => 'required|mimes:csv,xlsx|max:10240', // Max 10MB
        ]);

        $this->filename = $this->file->getClientOriginalName();
        $path = $this->file->store('temp');

        $this->loadPreviewData($path);
    }

    private function loadPreviewData($path)
    {
        $fullPath = Storage::path($path);
        $spreadsheet = IOFactory::load($fullPath);
        $sheet = $spreadsheet->getActiveSheet();
        $data = $sheet->toArray(null, true, true, true);

        $this->headers = array_shift($data); // Extract headers from the first row
        $this->previewData = array_slice($data, 0, 20); // Limit preview to first 20 rows
        $this->cleaningData = $data; // Store full data for cleaning
    }

    public function proceedToCleaning()
    {
        $this->isCleaning = true;
    }

    public function render()
    {
        return view('livewire.file-upload');
    }
}