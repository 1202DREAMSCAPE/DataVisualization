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
    public $headers = [];
    public $previewData = [];
    public $originalData = [];
    public $isChartSelectorOpen = false;

    public function updatedFile()
    {
        $this->validate([
            'file' => 'required|mimes:csv,xlsx|max:10240',
        ]);

        $this->filename = $this->file->getClientOriginalName();
        $path = $this->file->store('temp');

        $this->loadExcelData($path);
    }

    private function loadExcelData($path)
{
    $fullPath = Storage::path($path);
    $spreadsheet = IOFactory::load($fullPath);
    $sheet = $spreadsheet->getActiveSheet();
    $data = $sheet->toArray(null, true, true, true);

    // Debug the extracted data
    logger('Original Data: ', $data);

    $this->originalData = $data;
    $this->headers = array_shift($data); // First row as headers
    $this->previewData = $data; // Remaining rows as preview data

    // Log extracted headers and preview data
    logger('Headers: ', $this->headers);
    logger('Preview Data: ', $this->previewData);
}


    public function openChartSelector()
{
    logger('Opening Chart Selector with Headers: ', $this->headers);
    logger('Opening Chart Selector with Preview Data: ', $this->previewData);

    $this->isChartSelectorOpen = true;
}


    public function closeChartSelector()
    {
        $this->isChartSelectorOpen = false;
    }

    public function render()
    {
        return view('livewire.file-upload');
    }
}
