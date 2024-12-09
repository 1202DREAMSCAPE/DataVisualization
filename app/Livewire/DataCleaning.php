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
        // Load CSV or Excel data
        $data = array_map('str_getcsv', file(storage_path('app/' . $path)));
        $this->headers = array_shift($data); // Get headers
        $this->previewData = $data; // Store preview data
        $this->cleanedData = $data; // Initialize cleaned data
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
            return count(array_filter($row)) > 0; // Remove rows with all empty cells
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
