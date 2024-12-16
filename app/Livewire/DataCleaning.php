<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;

class DataCleaning extends Component
{
    use WithFileUploads;

    public $file;
    public $filename;
    public $headers = [];
    public $previewData = [];
    public $cleanedData = [];
    public $isModalOpen = false;

    public function updatedFile()
    {
        $this->validate([
            'file' => 'required|mimes:csv,xlsx|max:10240',
        ]);

        $this->filename = $this->file->getClientOriginalName();
        $path = $this->file->store('temp');
        $this->loadFileData($path);
    }

    private function loadFileData($path)
    {
        $filePath = storage_path('app/' . $path);
    
        if (!file_exists($filePath)) {
            throw new \Exception("File not found: {$filePath}");
        }
    
        // Determine file type
        $extension = pathinfo($filePath, PATHINFO_EXTENSION);
    
        if ($extension === 'csv') {
            // Read CSV data
            $data = array_map('str_getcsv', file($filePath));
    
            if (empty($data)) {
                throw new \Exception("The CSV file is empty.");
            }
    
            $this->headers = array_shift($data); // Get headers
            $this->previewData = $data; // Store preview data
            $this->cleanedData = $data; // Initialize cleaned data
        } else {
            throw new \Exception("Unsupported file type: {$extension}");
        }
    }
    
    public function openModal()
    {
        $this->isModalOpen = true;
    }

    public function closeModal()
    {
        $this->isModalOpen = false;
    }

    public function deleteEmptyRows()
    {
        $this->cleanedData = array_filter($this->cleanedData, function ($row) {
            return !empty(array_filter($row, fn($value) => $value !== null && $value !== ''));
        });
    }
    

    public function resetData()
    {
        $this->cleanedData = $this->previewData; // Reset to original preview data
    }

    public function saveCleanedData()
    {
        $filename = 'cleaned_' . time() . '.csv';
        $filePath = storage_path('app/public/' . $filename);

        $file = fopen($filePath, 'w');
        fputcsv($file, $this->headers); // Write headers
        foreach ($this->cleanedData as $row) {
            fputcsv($file, $row);
        }
        fclose($file);

        $this->dispatch('fileSaved', asset('storage/' . $filename));
        $this->closeModal();
    }

    public function render()
    {
        return view('livewire.data-cleaning');
    }
}
