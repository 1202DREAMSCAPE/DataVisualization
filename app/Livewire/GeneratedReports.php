<?php

namespace App\Livewire;

use App\Models\Report;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class GeneratedReports extends Component
{
    public $reports;

    public function mount()
    {
        // Fetch reports for the currently logged-in user
        $this->reports = Report::where('user_id', Auth::id())->get();
    }

    public function render()
    {
        return view('livewire.generated-reports', [
            'reports' => $this->reports,
        ]);
    }
}
