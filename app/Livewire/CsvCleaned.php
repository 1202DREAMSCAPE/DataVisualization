<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Session;

class CsvCleaned extends Component
{
    public $cleanedPreview;
    public $summary;

    public function mount()
    {
        $this->cleanedPreview = Session::get('file_preview_cleaned', []);
        $this->summary = Session::get('csv_summary', []);
    }

    public function render()
    {
        return view('livewire.csv-cleaned', [
            'cleanedPreview' => $this->cleanedPreview,
            'summary' => $this->summary,
        ]);
    }
}
