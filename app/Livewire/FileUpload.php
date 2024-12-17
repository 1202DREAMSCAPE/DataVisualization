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
    public $missingValuesSummary = [];
    
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

        // dd($this->headers, $this->cleanedData);


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
    
            // Extract headers and validate them
            $this->headers = array_shift($data);
            if (!$this->headers || empty(array_filter($this->headers))) {
                throw new \Exception("Headers are missing or invalid in the uploaded file.");
            }
    
            // Generate summaries
            $this->missingValuesSummary = $this->calculateMissingValuesSummary($data);
            $this->imputationOptions = $this->generateImputationOptions($data);
            $this->showImputation = !empty($this->imputationOptions) && array_sum($this->missingValuesSummary) > 0;

    
            // Clean data
            $this->cleanedData = $this->cleanData($data);
    
            // Store cleaned data in session for reuse
            session([
                'cleaned_file' => [
                    'filename' => $this->filename,
                    'headers' => $this->headers,
                    'cleanedData' => $this->cleanedData,
                    'cleaningSummary' => $this->cleaningSummary,
                ],
            ]);
    
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to process the file: ' . $e->getMessage());
        } finally {
            Storage::delete($path); // Always delete the uploaded file after processing
        }
    }
    
    private function cleanData(array $data): array
    {
        // Check if input is valid
        if (empty($data) || !is_array($data)) {
            return [];
        }
    
        $cleanedData = [];
        $uniqueRows = collect(); // Use Laravel collections for better handling
        $rowsRemovedDueToNulls = 0;
        $rowsRemovedDueToDuplicates = 0;
    
        foreach ($data as $row) {
            // Normalize row values
            $normalizedRow = array_map(fn($value) => $this->normalizeValue($value), $row);
    
            // Check if the row is completely null/empty
            $isRowEmpty = empty(array_filter($normalizedRow, fn($value) => $this->isValidValue($value)));
    
            if ($isRowEmpty) {
                if ($uniqueRows->contains($normalizedRow)) {
                    $rowsRemovedDueToDuplicates++;
                    continue;
                }
                $rowsRemovedDueToNulls++;
            } else {
                // Check for duplicates
                if ($uniqueRows->contains($normalizedRow)) {
                    $rowsRemovedDueToDuplicates++;
                    continue;
                }
            }
    
            // Impute missing values
            $row = $this->imputeMissingValues($normalizedRow, $data);
    
            // Add the row to the cleaned dataset and track uniqueness
            $uniqueRows->push($normalizedRow);
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
    
    private function normalizeValue($value): mixed
    {
        // Trim, lowercase, and collapse spaces for strings
        return is_string($value) ? preg_replace('/\s+/', ' ', trim(strtolower($value))) : $value;
    }
    
    private function isValidValue($value): bool
    {
        // Validates if the value is non-null, non-empty, and not a placeholder
        return $value !== null && $value !== '' && $value !== '-';
    }
    
    private function imputeMissingValues(array $row, array $data): array
    {
        // Replace missing values with imputed values
        return array_map(fn($value, $index) => $this->shouldImpute($value) 
            ? $this->getImputedValue($data, $index) 
            : $value, 
            $row, 
            array_keys($row)
        );
    }
    
    private function shouldImpute($value): bool
    {
        // Check if the value needs imputation
        return $value === null || $value === '' || $value === '-';
    }
    
    private function calculateMissingValuesSummary(array $data)
    {
        $summary = [];
        foreach ($this->headers as $index => $header) {
            $columnValues = array_column($data, $index);
            $missingCount = array_reduce($columnValues, function ($carry, $value) {
                return $carry + (is_null($value) || trim((string) $value) === '' || $value === '-');
            }, 0);
            $summary[$header] = $missingCount;
        }
        return $summary;
    }
    


private function hasMissingValues(array $data)
{
    foreach ($data as $row) {
        foreach ($row as $value) {
            if (is_null($value) || trim((string) $value) === '' || $value === '-') {
                return true; // Missing value found
            }
        }
    }
    return false; // No missing values
}

private function generateImputationOptions(array $data)
{
    $options = [];
    
    // Return empty array if no data
    if (empty($data)) {
        return $options;
    }

    foreach ($this->headers as $index => $header) {
        // Safely get column values, handling potential index issues
        $columnValues = array_column($data, $index);
        
        // Filter out truly empty or null values
        $nonEmptyValues = array_filter($columnValues, function ($value) {
            return $value !== null 
                && $value !== '' 
                && $value !== '-' 
                && trim((string)$value) !== '';
        });

        // Check if there are any truly missing values
        $hasMissingValues = count($columnValues) > count($nonEmptyValues);
    
        if ($hasMissingValues) {
            // More robust numeric check
            $isNumeric = !empty($nonEmptyValues) && array_reduce($nonEmptyValues, function ($carry, $value) {
                return $carry && is_numeric($value);
            }, true);
    
            $options[$header] = $isNumeric
                ? ['mean', 'median', 'mode']
                : ['mode', 'default_value'];
        }
    }
    
    return $options;
}

private function getImputedValue(array $data, $colIndex)
{
    // Convert string column index to numeric if needed
    if (is_string($colIndex)) {
        $colIndex = array_search($colIndex, array_keys($data[0]));
        if ($colIndex === false) {
            // If column not found, return null or a default value
            return null;
        }
    }

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
            // Apply imputation to a working copy of the cleaned data
            $imputedData = array_map(function ($row) {
                return $this->imputeMissingValues($row, $this->cleanedData);
            }, $this->cleanedData);
    
            // Store the imputed data separately
            $this->cleanedData = $imputedData;
            $this->imputationComplete = true;
    
            // Save the updated data to the session for future use
            session([
                'imputed_file' => [
                    'filename' => $this->filename,
                    'headers' => $this->headers,
                    'cleanedData' => $this->cleanedData,
                ],
            ]);
    
            session()->flash('success', 'Imputation method applied successfully!');
        } else {
            session()->flash('error', 'No data available for imputation.');
        }
    
        $this->dispatch('refreshComponent'); // Trigger frontend update
    }
    
    
    public function uploadRecentlyUsedFile()
    {
        if (session()->has('cleaned_file')) {
            $recentFile = session('cleaned_file');
            
            // Ensure session data is available and set the properties
            $this->filename = $recentFile['filename'] ?? '';
            $this->headers = $recentFile['headers'] ?? [];
            $this->cleanedData = $recentFile['cleanedData'] ?? [];
    
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
            'missingValuesSummary' => $this->missingValuesSummary,
            'imputationOptions' => $this->imputationOptions,
            'showImputation' => $this->showImputation && !empty($this->imputationOptions) && !$this->imputationComplete,
            'imputationComplete' => $this->imputationComplete,
        ]);
    }
}