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
    public $cleanedData = [];
    public $cleaningSummary = [];
    public $imputationOptions = [];
    public $selectedImputation = [];
    public $isChartSelectorOpen = false;
    public $showImputation = false; 
    public $imputationMethod = 'mean'; // Default method
    public $imputationComplete = false;

    public function updatedImputationMethod()
    {
        $this->applyImputation();
    }

    public function updatedFile()
    {
        $this->validate([
            'file' => 'required|mimes:csv,xlsx|max:10240',
        ]);

        $this->filename = $this->file->getClientOriginalName();
        $path = $this->file->store('temp');
        $this->loadAndCleanData($path);

        session([
            'cleaned_file' => [
                'filename' => $this->filename,
                'headers' => $this->headers,
                'cleanedData' => $this->cleanedData,
            ],
        ]);

        session()->flash('success', 'File uploaded and cleaned successfully!');
    }

    private function loadAndCleanData($path)
    {
        $fullPath = Storage::path($path);
    
        try {
            $spreadsheet = IOFactory::load($fullPath);
            $sheet = $spreadsheet->getActiveSheet();
            $data = $sheet->toArray(null, true, true, true);
    
            $this->headers = array_shift($data);
    
            // Generate imputation options and check if imputation is needed
            $this->imputationOptions = $this->generateImputationOptions($data);
    
            // Show imputation only if there are missing values
            $this->showImputation = array_reduce($data, function ($carry, $row) {
                foreach ($row as $value) {
                    if (is_null($value) || trim((string) $value) === '' || $value === '-') {
                        return true;
                    }
                }
                return $carry;
            }, false);
    
            $this->cleanedData = $this->cleanData($data);
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to process the file. Please ensure it is a valid CSV or XLSX file.');
        } finally {
            Storage::delete($path);
        }
    }
    

    private function generateImputationOptions(array $data)
    {
        $options = [];
        foreach ($this->headers as $index => $header) {
            $columnValues = array_column($data, $index);
            $isNumeric = array_reduce($columnValues, function ($carry, $value) {
                return $carry && (is_numeric($value) || is_null($value) || trim($value) === '');
            }, true);

            $options[$header] = $isNumeric
                ? ['mean', 'median', 'mode', 'standard_deviation']
                : ['mode', 'default_value'];
        }
        return $options;
    }

    private function cleanData(array $data)
{
    $cleanedData = [];
    $uniqueRows = [];
    $rowsRemovedDueToNulls = 0;
    $rowsRemovedDueToDuplicates = 0;

    foreach ($data as $rowIndex => $row) {
        // Check if the row contains only null, blank, or "-" values
        $isNullRow = true;
        foreach ($row as $colIndex => &$value) { // Ensure $colIndex is the integer index
            // Trim whitespaces and treat empty strings as null
            $value = is_string($value) ? trim($value) : $value;

            if (!is_null($value) && $value !== '' && $value !== '-') {
                $isNullRow = false;
            }
        }

        if ($isNullRow) {
            $rowsRemovedDueToNulls++;
            continue;
        }

        // Serialize the row to check for duplicates
        $serializedRow = serialize($row);
        if (in_array($serializedRow, $uniqueRows)) {
            $rowsRemovedDueToDuplicates++;
            continue;
        }

        // Impute missing values
        $row = array_map(function ($value, $colIndex) use ($data) {
            // Pass integer index to `getImputedValue`
            if (is_null($value) || $value === '' || $value === '-') {
                return $this->getImputedValue($data, (int)$colIndex);
            }
            return $value;
        }, $row, array_keys($row)); // Use array_keys to map numeric indices

        $uniqueRows[] = $serializedRow;
        $cleanedData[] = $row;
    }

    $this->cleaningSummary = [
        'total_rows' => count($data),
        'cleaned_rows' => count($cleanedData),
        'rows_removed_due_to_nulls' => $rowsRemovedDueToNulls,
        'rows_removed_due_to_duplicates' => $rowsRemovedDueToDuplicates,
    ];

    return $cleanedData;
}

    private function getImputedValue(array $data, int $colIndex)
{
    $columnValues = array_column($data, $colIndex);
    $nonNullValues = array_filter($columnValues, function ($value) {
        return !is_null($value) && trim((string) $value) !== '' && $value !== '-';
    });

    if (empty($nonNullValues)) {
        return $this->imputationMethod === 'default_value' ? 'N/A' : null; // Fallback
    }

    if ($this->isColumnNumeric($nonNullValues)) {
        // Numerical imputation logic
        switch ($this->imputationMethod) {
            case 'mean':
                return array_sum($nonNullValues) / count($nonNullValues);
            case 'median':
                sort($nonNullValues);
                $middle = floor(count($nonNullValues) / 2);
                return count($nonNullValues) % 2 === 0
                    ? ($nonNullValues[$middle - 1] + $nonNullValues[$middle]) / 2
                    : $nonNullValues[$middle];
            case 'mode':
                $valuesCount = array_count_values($nonNullValues);
                arsort($valuesCount);
                return array_key_first($valuesCount);
            default:
                return null;
        }
    } else {
        // Categorical imputation (mode)
        $valuesCount = array_count_values($nonNullValues);
        arsort($valuesCount);
        return array_key_first($valuesCount);
    }
}

    
    private function isColumnNumeric(array $values)
    {
        return array_reduce($values, function ($carry, $value) {
            return $carry && is_numeric($value);
        }, true);
    }
    
    public function applyImputation()
    {
        if (!empty($this->cleanedData)) {
            // Re-run cleaning with the new imputation method
            $this->cleanedData = $this->cleanData($this->cleanedData);
            $this->imputationComplete = true;
    
            // Save the updated data to the session
            session([
                'cleaned_file' => [
                    'filename' => $this->filename,
                    'headers' => $this->headers,
                    'cleanedData' => $this->cleanedData,
                ],
            ]);
    
            session()->flash('success', 'Imputation method updated successfully!');
        } else {
            session()->flash('error', 'No data available for imputation.');
        }
    }
    
    public function uploadRecentlyUsedFile()
    {
        if (session()->has('cleaned_file')) {
            $recentFile = session('cleaned_file');
            
            // Set the filename, headers, and cleanedData from the session
            $this->filename = $recentFile['filename'];
            $this->headers = $recentFile['headers'];
            $this->cleanedData = $recentFile['cleanedData'];

            // Check if imputation is needed
            $this->imputationOptions = $this->generateImputationOptions($this->cleanedData);
            $this->showImputation = !empty($this->imputationOptions);

            session()->flash('success', 'Recently used file loaded successfully!');
        } else {
            session()->flash('error', 'No recently uploaded file found.');
        }
    }

    public function render()
    {
        return view('livewire.file-upload', [
            'headers' => $this->headers,
            'cleanedData' => $this->cleanedData,
            'cleaningSummary' => $this->cleaningSummary,
            'imputationOptions' => $this->imputationOptions,
            'showImputation' => $this->showImputation && !$this->imputationComplete,
            'imputationComplete' => $this->imputationComplete,
        ]);
    }
}