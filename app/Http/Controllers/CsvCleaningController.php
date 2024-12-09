<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Maatwebsite\Excel\Facades\Excel;

class CsvCleaningController extends Controller
{
    /**
     * Show the CSV/XLSX upload form.
     */
    public function showUploadForm()
    {
        return view('clean-csv.upload');
    }

    /**
     * Handle CSV/XLSX file upload.
     */
    public function uploadCsv(Request $request)
    {
        // Validate the request to accept CSV and XLSX files
        $request->validate([
            'file' => 'required|file|mimes:csv,xlsx,xls',
        ]);

        // Handle the uploaded file
        if ($request->hasFile('file')) {
            $file = $request->file('file');

            // Generate unique filename
            $uniqueId = Str::uuid();
            $extension = $file->getClientOriginalExtension();
            $originalFileName = 'original_' . $uniqueId . '.' . $extension;

            // Define storage path in public directory
            $storagePath = 'uploads/temp';

            // Ensure the directory exists
            if (!Storage::disk('public')->exists($storagePath)) {
                Storage::disk('public')->makeDirectory($storagePath);
            }

            // Store the original file in public/uploads/temp
            $filePath = $file->storeAs($storagePath, $originalFileName, 'public');

            // Check if file was stored successfully
            if (!$filePath) {
                return redirect()->back()->withErrors(['file' => 'Failed to upload the file. Please try again.']);
            }

            // Read a preview of the file (first 1000 rows)
            $previewData = $this->readFile($filePath, 1000, 'public');

            // Save the original file path and preview in session
            Session::put('original_file', $filePath);
            Session::put('file_preview_original', $previewData);
            // Clear any previous summary and cleaned file
            Session::forget('csv_summary');
            Session::forget('file_preview_cleaned');
            Session::forget('cleaned_file');

            return redirect()->route('clean-csv.preview')->with('success', 'File uploaded successfully!');
        }

        return redirect()->back()->withErrors(['file' => 'Please upload a valid CSV or XLSX file.']);
    }

    /**
     * Show the file preview and provide a clean button.
     */
    public function showPreview()
    {
        // Retrieve preview data from session
        $previewOriginal = Session::get('file_preview_original');

        if (!$previewOriginal) {
            return redirect()->route('clean-csv.upload.form')->withErrors(['file' => 'No file uploaded.']);
        }

        return view('clean-csv.preview', compact('previewOriginal'));
    }

    /**
     * Clean the uploaded CSV/XLSX file.
     */
    public function cleanCsv(Request $request)
    {
        // Check if original file is uploaded
        $originalFilePath = Session::get('original_file');

        if (!$originalFilePath || !Storage::disk('public')->exists($originalFilePath)) {
            return redirect()->route('clean-csv.upload.form')->withErrors(['file' => 'Original file not found. Please upload a file first.']);
        }

        try {
            // Read the original file as an array
            $originalData = $this->readFileAsArray($originalFilePath, 'public');

            // Keep track of original rows count
            $originalRowCount = count($originalData);

            // Clean the data
            $cleanedData = $this->cleanData($originalData, $originalRowCount);

            // Keep track of cleaned rows count
            $cleanedRowCount = count($cleanedData);

            // Generate cleaned file content
            $cleanedFileContent = $this->arrayToCsv($cleanedData);

            // Generate unique filename for cleaned file
            $uniqueId = Str::uuid();
            $extension = 'csv'; // Saving cleaned file as CSV
            $cleanedFileName = 'cleaned_' . $uniqueId . '.' . $extension;
            $cleanedFilePath = 'uploads/temp/' . $cleanedFileName;

            // Store the cleaned CSV in public directory
            Storage::disk('public')->put($cleanedFilePath, $cleanedFileContent);

            // Check if file was stored successfully
            if (!Storage::disk('public')->exists($cleanedFilePath)) {
                return redirect()->route('clean-csv.preview')->withErrors(['error' => 'Failed to clean the file. Please try again.']);
            }

            // Save cleaned file path in session
            Session::put('cleaned_file', $cleanedFilePath);

            // Generate summary of changes
            $summary = $this->generateSummary($originalData, $cleanedData, $originalRowCount, $cleanedRowCount);

            // Save summary in session
            Session::put('csv_summary', $summary);

            // Update the preview to show cleaned data
            $cleanedPreviewData = $this->readFile($cleanedFilePath, 1000, 'public');
            Session::put('file_preview_cleaned', $cleanedPreviewData);

            return redirect()->route('clean-csv.cleaned')->with('success', 'File cleaned successfully!');
        } catch (\Exception $e) {
            Log::error("Error cleaning file: " . $e->getMessage());
            return redirect()->route('clean-csv.preview')->withErrors(['error' => 'An error occurred while cleaning the file.']);
        }
    }

    /**
     * Show the cleaned file preview and summary.
     */
    public function showCleaned()
    {
        // Retrieve cleaned preview and summary from session
        $cleanedPreview = Session::get('file_preview_cleaned');
        $summary = Session::get('csv_summary');

        if (!$cleanedPreview || !$summary) {
            return redirect()->route('clean-csv.upload.form')->withErrors(['file' => 'No cleaned file data found. Please upload and clean a file first.']);
        }

        return view('clean-csv.cleaned', compact('cleanedPreview', 'summary'));
    }

    /**
     * Download the cleaned CSV file.
     */
    public function downloadCleanedCsv()
    {
        // Get the cleaned file path from session
        $cleanedFilePath = Session::get('cleaned_file');

        if (!$cleanedFilePath || !Storage::disk('public')->exists($cleanedFilePath)) {
            return redirect()->route('clean-csv.upload.form')->withErrors(['file' => 'Cleaned file not found. Please clean the file first.']);
        }

        // Get the full path
        $fullPath = Storage::disk('public')->path($cleanedFilePath);

        // Get the filename
        $fileName = basename($cleanedFilePath);

        // Return the file as a download using response()->download()
        return response()->download($fullPath, $fileName, [
            'Content-Type' => 'text/csv',
        ]);
    }

    /**
     * Read a file and return a preview (first $limit rows).
     */
    private function readFile($filePath, $limit = 1000, $disk = 'public')
    {
        $fullPath = Storage::disk($disk)->path($filePath);
        $preview = [];

        if (!file_exists($fullPath)) {
            Log::error("File not found for preview: {$fullPath}");
            return [
                'headers' => [],
                'rows' => [],
            ];
        }

        try {
            $extension = pathinfo($fullPath, PATHINFO_EXTENSION);

            if (in_array(strtolower($extension), ['csv', 'txt'])) {
                // Handle CSV/TXT files
                if (($handle = fopen($fullPath, 'r')) !== false) {
                    $headers = fgetcsv($handle, 0, ',');
                    if ($headers) {
                        while (($data = fgetcsv($handle, 0, ',')) !== false && count($preview) < $limit) {
                            if (empty(array_filter($data))) {
                                continue; // Skip blank lines
                            }
                            $preview[] = array_combine($headers, $data);
                        }
                    }
                    fclose($handle);
                } else {
                    Log::error("Failed to open file for preview: {$fullPath}");
                }
            } elseif (in_array(strtolower($extension), ['xlsx', 'xls'])) {
                // Handle XLSX/XLS files using Laravel-Excel
                $rows = Excel::toArray([], $fullPath);
                if (!empty($rows)) {
                    $sheet = $rows[0]; // Assuming data is in the first sheet
                    if (count($sheet) > 0) {
                        $headers = $sheet[0];
                        for ($i = 1; $i < count($sheet); $i++) {
                            $row = $sheet[$i];
                            if (empty(array_filter($row))) {
                                continue; // Skip blank lines
                            }
                            $assocRow = array_combine($headers, $row);
                            $preview[] = $assocRow;
                            if (count($preview) >= $limit) {
                                break;
                            }
                        }
                    }
                }
            } else {
                Log::error("Unsupported file extension for preview: {$extension}");
            }
        } catch (\Exception $e) {
            Log::error("Error reading file for preview: " . $e->getMessage());
        }

        return [
            'headers' => $headers ?? [],
            'rows' => $preview,
        ];
    }

    /**
     * Read a file as an associative array.
     */
    private function readFileAsArray($filePath, $disk = 'public')
    {
        $fullPath = Storage::disk($disk)->path($filePath);
        $data = [];

        if (!file_exists($fullPath)) {
            Log::error("File not found for associative read: {$fullPath}");
            return $data;
        }

        try {
            $extension = pathinfo($fullPath, PATHINFO_EXTENSION);

            if (in_array(strtolower($extension), ['csv', 'txt'])) {
                // Handle CSV/TXT files
                if (($handle = fopen($fullPath, 'r')) !== false) {
                    $headers = fgetcsv($handle, 0, ',');
                    if ($headers) {
                        while (($row = fgetcsv($handle, 0, ',')) !== false) {
                            if (empty(array_filter($row))) {
                                continue; // Skip blank lines
                            }
                            $data[] = array_combine($headers, $row);
                        }
                    }
                    fclose($handle);
                } else {
                    Log::error("Failed to open file for associative read: {$fullPath}");
                }
            } elseif (in_array(strtolower($extension), ['xlsx', 'xls'])) {
                // Handle XLSX/XLS files using Laravel-Excel
                $rows = Excel::toArray([], $fullPath);
                if (!empty($rows)) {
                    $sheet = $rows[0]; // Assuming data is in the first sheet
                    if (count($sheet) > 0) {
                        $headers = $sheet[0];
                        for ($i = 1; $i < count($sheet); $i++) {
                            $row = $sheet[$i];
                            if (empty(array_filter($row))) {
                                continue; // Skip blank lines
                            }
                            $assocRow = array_combine($headers, $row);
                            $data[] = $assocRow;
                        }
                    }
                }
            } else {
                Log::error("Unsupported file extension for associative read: {$extension}");
            }
        } catch (\Exception $e) {
            Log::error("Error reading file as array: " . $e->getMessage());
        }

        return $data;
    }

    /**
     * Clean the data by removing rows with nulls, non-ASCII characters, duplicates in non-numeric fields, and trimming whitespace.
     */
    private function cleanData(array $data, int $originalRowCount)
    {
        $cleanedData = [];
        $rowsRemovedDueToNulls = 0;
        $rowsRemovedDueToNonAscii = 0;
        $rowsRemovedDueToDuplicates = 0;
        $whitespaceTrimmed = 0;

        // To track unique combinations based on non-numeric fields
        $uniqueKeys = [];

        foreach ($data as $row) {
            // Check for null or empty values
            if (in_array(null, $row, true) || in_array('', $row, true)) {
                $rowsRemovedDueToNulls++;
                continue; // Remove the row
            }

            // Check for non-ASCII characters in any cell
            $hasNonAscii = false;
            foreach ($row as $cell) {
                if (is_string($cell) && preg_match('/[^\x00-\x7F]/', $cell)) {
                    $hasNonAscii = true;
                    break;
                }
            }

            if ($hasNonAscii) {
                $rowsRemovedDueToNonAscii++;
                continue; // Remove the row
            }

            // Trim whitespace from text columns
            foreach ($row as $key => $value) {
                if (is_string($value)) {
                    $trimmed = trim($value);
                    if ($trimmed !== $value) {
                        $row[$key] = $trimmed;
                        $whitespaceTrimmed++;
                    }
                }
            }

            // Generate a unique key based on non-numeric fields
            $nonNumericValues = [];
            foreach ($row as $key => $value) {
                if (!$this->isNumericColumn([$value])) {
                    $nonNumericValues[$key] = $value;
                }
            }

            $serializedKey = serialize($nonNumericValues);

            if (in_array($serializedKey, $uniqueKeys, true)) {
                $rowsRemovedDueToDuplicates++;
                continue; // Duplicate found, remove the row
            }

            $uniqueKeys[] = $serializedKey;
            $cleanedData[] = $row;
        }

        // Calculate modifications percentage
        $totalFields = $originalRowCount * (count($data[0] ?? []) ?: 1);
        $modificationsPercentage = $totalFields > 0 ? ($whitespaceTrimmed / $totalFields) * 100 : 0;

        // Store modification metrics
        $this->modificationMetrics = [
            'whitespace_trimmed' => $whitespaceTrimmed,
            'modifications_percentage' => round($modificationsPercentage, 2),
        ];

        // Calculate total rows removed
        $totalRowsRemoved = $rowsRemovedDueToNulls + $rowsRemovedDueToNonAscii + $rowsRemovedDueToDuplicates;

        // Summary is generated separately
        $this->cleaningSummary = [
            'rows_removed_due_to_nulls' => $rowsRemovedDueToNulls,
            'rows_removed_due_to_non_ascii' => $rowsRemovedDueToNonAscii,
            'rows_removed_due_to_duplicates' => $rowsRemovedDueToDuplicates,
            'total_rows_removed' => $totalRowsRemoved,
            'whitespace_trimmed' => $whitespaceTrimmed,
            'modifications_percentage' => $this->modificationMetrics['modifications_percentage'],
        ];

        return $cleanedData;
    }

    /**
     * Check if a value is numeric.
     */
    private function isNumericColumn(array $values)
    {
        foreach ($values as $value) {
            if (!is_numeric($value)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Convert an associative array to a CSV string.
     */
    private function arrayToCsv(array $data)
    {
        if (empty($data)) {
            return '';
        }

        $output = fopen('php://temp', 'r+');
        fputcsv($output, array_keys($data[0]));

        foreach ($data as $row) {
            fputcsv($output, $row);
        }

        rewind($output);
        $csv = stream_get_contents($output);
        fclose($output);

        return $csv;
    }

    /**
     * Generate a summary of changes between the original and cleaned data.
     */
    private function generateSummary(array $originalData, array $cleanedData, int $originalRowCount, int $cleanedRowCount)
    {
        $rowsRemovedDueToNulls = $this->cleaningSummary['rows_removed_due_to_nulls'] ?? 0;
        $rowsRemovedDueToNonAscii = $this->cleaningSummary['rows_removed_due_to_non_ascii'] ?? 0;
        $rowsRemovedDueToDuplicates = $this->cleaningSummary['rows_removed_due_to_duplicates'] ?? 0;
        $totalRowsRemoved = $this->cleaningSummary['total_rows_removed'] ?? 0;
        $whitespaceTrimmed = $this->cleaningSummary['whitespace_trimmed'] ?? 0;
        $modificationsPercentage = $this->cleaningSummary['modifications_percentage'] ?? 0;

        // Calculate non-ASCII characters removed (if needed)
        $nonAsciiRemoved = 0;
        foreach ($originalData as $row) {
            foreach ($row as $cell) {
                if (is_string($cell) && preg_match('/[^\x00-\x7F]/', $cell)) {
                    $nonAsciiRemoved++;
                }
            }
        }

        // Summary
        $summary = [
            'rows_removed_due_to_nulls' => $rowsRemovedDueToNulls,
            'rows_removed_due_to_non_ascii' => $rowsRemovedDueToNonAscii,
            'rows_removed_due_to_duplicates' => $rowsRemovedDueToDuplicates,
            'total_rows_removed' => $totalRowsRemoved,
            'percentage_rows_removed' => $originalRowCount > 0 ? round(($totalRowsRemoved / $originalRowCount) * 100, 2) : 0,
            'non_ascii_values_removed' => $nonAsciiRemoved,
            'whitespace_trimmed' => $whitespaceTrimmed,
            'modifications_percentage' => $modificationsPercentage,
        ];

        return $summary;
    }

    /**
     * Initialize modification metrics.
     */
    private $modificationMetrics = [];
    private $cleaningSummary = [];
}