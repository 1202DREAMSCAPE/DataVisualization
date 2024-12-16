<?php 

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Session;

class CsvUpload extends Component
{
    use WithFileUploads;

    public $file;

    public function upload()
    {
        $this->validate([
            'file' => 'required|file|mimes:csv,xlsx,xls',
        ]);

        $uniqueId = Str::uuid();
        $extension = $this->file->getClientOriginalExtension();
        $originalFileName = 'original_' . $uniqueId . '.' . $extension;
        $storagePath = 'uploads/temp';

        if (!Storage::disk('public')->exists($storagePath)) {
            Storage::disk('public')->makeDirectory($storagePath);
        }

        $filePath = $this->file->storeAs($storagePath, $originalFileName, 'public');

        if (!$filePath) {
            session()->flash('error', 'Failed to upload the file. Please try again.');
            return;
        }

        // Read file preview and save to session
        $previewData = $this->readFilePreview(Storage::disk('public')->path($filePath));
        Session::put('original_file', $filePath);
        Session::put('file_preview_original', $previewData);

        // Redirect to preview
        return redirect()->route('csv-preview');
    }

    private function readFilePreview($filePath, $limit = 100)
    {
        $preview = [];
        if (($handle = fopen($filePath, 'r')) !== false) {
            $headers = fgetcsv($handle, 0, ',');
            while (($data = fgetcsv($handle, 0, ',')) !== false && count($preview) < $limit) {
                $preview[] = array_combine($headers, $data);
            }
            fclose($handle);
        }
        return ['headers' => $headers, 'rows' => $preview];
    }

    public function render()
    {
        return view('livewire.csv-upload');
    }
}
