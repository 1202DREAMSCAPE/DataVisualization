<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\IOFactory;
use App\Models\FileRecord;


class FileUpload extends Component
{
    use WithFileUploads;

    public $file;
    public $filename;
    public $headers = [];
    public $previewData = [];
    public $isChartSelectorOpen = false;

    protected $listeners = ['closeChartSelector' => 'closeChartSelector'];


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
    }
    

    /**
     * Validate and process the uploaded file.
    //  */
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
