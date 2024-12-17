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
    public $showImputation = false; // Controls the visibility of imputation dropdown
    public $imputationMethod = 'mean'; // Default method
    public $missingValuesSummary = []; // Holds the count of missing values for each column



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
            if (!$this->headers) {
                throw new \Exception("Headers are missing from the uploaded file.");
            }
    
            // Generate summaries and options
            $this->missingValuesSummary = $this->calculateMissingValuesSummary($data);
            $this->showImputation = array_sum($this->missingValuesSummary) > 0;
            $this->imputationOptions = $this->generateImputationOptions($data);
    
            $this->cleanedData = $this->cleanData($data);
    
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to process the file: ' . $e->getMessage());
        } finally {
            Storage::delete($path);
        }
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
    foreach ($this->headers as $index => $header) {
        if (!isset($data[0][$index])) {
            logger('Invalid column index in generateImputationOptions', ['index' => $index, 'header' => $header]);
            continue; // Skip invalid column indices
        }
        $columnValues = array_column($data, $index);
        $hasMissingValues = array_reduce($columnValues, function ($carry, $value) {
            return $carry || is_null($value) || trim((string) $value) === '' || $value === '-';
        }, false);
    
        if ($hasMissingValues) {
            $isNumeric = array_reduce($columnValues, function ($carry, $value) {
                return $carry && (is_numeric($value) || is_null($value) || trim($value) === '' || $value === '-');
            }, true);
    
            $options[$header] = $isNumeric
                ? ['mean', 'median', 'mode', 'standard_deviation']
                : ['mode', 'default_value'];
        }
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
            foreach ($row as $value) {
                if (!is_null($value) && trim((string) $value) !== '' && $value !== '-') {
                    $isNullRow = false;
                    break;
                }
            }
    
            if ($isNullRow) {
                $rowsRemovedDueToNulls++;
                continue; // Skip null rows
            }
    
            // Serialize the row to check for duplicates
            $normalizedRow = array_map(function ($value) {
                return is_string($value) ? strtolower(trim($value)) : $value;
            }, $row);
    
            $serializedRow = serialize($normalizedRow);
            if (in_array($serializedRow, $uniqueRows)) {
                $rowsRemovedDueToDuplicates++;
                continue; // Skip duplicate rows
            }
    
            // Handle missing values
            foreach ($row as $colKey => $value) {
                // Convert column header keys to indexes if needed
                $colIndex = is_int($colKey) ? $colKey : array_search($colKey, array_keys($this->headers));
                if ($colIndex === false) {
                    logger('Error during imputation', ['message' => "Column index $colKey does not exist.", 'colKey' => $colKey]);
                    continue; // Skip if column index is invalid
                }
                if (is_null($value) || trim((string) $value) === '' || $value === '-') {
                    $row[$colKey] = $this->getImputedValue($data, $colIndex);
                }
            }
            
    
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
    // Check if column index exists
    if (!isset($data[0][$colIndex])) {
        logger('Error during imputation', ['message' => "Column index $colIndex does not exist.", 'colIndex' => $colIndex]);
        return null; // Return null if column index is invalid
    }

    $columnValues = array_column($data, $colIndex);
    $nonNullValues = array_filter($columnValues, function ($value) {
        return !is_null($value) && trim((string) $value) !== '' && $value !== '-';
    });

    if (empty($nonNullValues)) {
        return null; // No values to base imputation on
    }

    if ($this->isColumnNumeric($nonNullValues)) {
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
        // Categorical (string) imputation: Use mode
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
            foreach ($this->cleanedData as &$row) {
                foreach ($row as $colIndex => &$value) {
                    if (is_null($value) || trim((string) $value) === '' || $value === '-') {
                        $value = $this->getImputedValue($this->cleanedData, (int)$colIndex);
                    }
                }
            }
            unset($row); // Unset reference to avoid side effects
    
            // Update the missing values summary and reassign cleaned data
            $this->missingValuesSummary = $this->calculateMissingValuesSummary($this->cleanedData);
            $this->imputationOptions = $this->generateImputationOptions($this->cleanedData);
    
            // Save the updated data to the session
            session([
                'cleaned_file' => [
                    'filename' => $this->filename,
                    'headers' => $this->headers,
                    'cleanedData' => $this->cleanedData,
                ],
            ]);
    
            session()->flash('success', 'Imputation applied successfully and data updated!');
        } else {
            session()->flash('error', 'No data available for imputation.');
        }
    
        $this->emitSelf('refreshComponent'); // Trigger frontend update
    }
    
    
    
    public function uploadRecentlyUsedFile()
    {
        if (session()->has('cleaned_file')) {
            $recentFile = session('cleaned_file');
            
            // Set the filename, headers, and cleanedData from the session
            $this->filename = $recentFile['filename'];
            $this->headers = $recentFile['headers'];
            $this->cleanedData = $recentFile['cleanedData'];
    
            // Recalculate missing values summary and imputation options
            $this->missingValuesSummary = $this->calculateMissingValuesSummary($this->cleanedData);
            $this->showImputation = array_sum($this->missingValuesSummary) > 0;
            $this->imputationOptions = $this->generateImputationOptions($this->cleanedData);
    
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
            'showImputation' => $this->showImputation,

        ]);
    }
}
