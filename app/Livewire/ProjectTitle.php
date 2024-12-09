<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Project;

class ProjectTitle extends Component
{
    public $project;

    public function mount($projectId)
    {
        $this->project = Project::find($projectId);
    }

    public function updateTitle($newTitle)
    {
        $this->project->update(['title' => $newTitle]);
    }

    public function render()
    {
        return view('livewire.project-title');
    }
}
