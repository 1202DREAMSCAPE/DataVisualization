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
    public $isChartSelectorOpen = false;

    public function openChartSelector()
    {
        $this->isChartSelectorOpen = true;
    }

    public function closeChartSelector()
    {
        $this->isChartSelectorOpen = false;
    }

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
        $this->headers = array_shift($data);
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

    public function startDataCleaning()
{
    // Example: Log to confirm the method is triggered
    logger('Start Data Cleaning button clicked');

    // Perform data cleaning (e.g., remove rows with empty cells)
    $this->previewData = array_filter($this->previewData, function ($row) {
        return count(array_filter($row)) > 0; // Remove rows with all empty cells
    });

    session()->flash('message', 'Data cleaning completed!');
}


    public function render()
    {
        return view('livewire.file-upload');
    }
}
