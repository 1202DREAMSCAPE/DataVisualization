<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Session;

class CsvPreview extends Component
{
    public $previewData;

    public function mount()
    {
        $this->previewData = Session::get('file_preview_original', []);
    }

    public function cleanFile()
    {
        // Add logic to clean file
        return redirect()->route('csv-cleaned');
    }

    public function render()
    {
        return view('livewire.csv-preview', ['preview' => $this->previewData]);
    }
}
