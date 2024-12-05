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
    public $headerRowIndex = 1;

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

        $this->originalData = $data;
        $this->headers = array_shift($data); // Default headers
        $this->previewData = $data;
    }

    public function setHeaderRow($rowIndex)
    {
        $this->headerRowIndex = $rowIndex;
        $this->headers = $this->originalData[$rowIndex - 1];
        $this->previewData = array_slice($this->originalData, $rowIndex);
    } 

    public function deleteColumn($columnKey)
    {
        foreach ($this->previewData as &$row) {
            unset($row[$columnKey]);
        }
        unset($this->headers[$columnKey]);
    }

    public function render()
    {
        return view('livewire.file-upload');
    }
}
