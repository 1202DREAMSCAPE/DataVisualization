<div class="p-4 bg-white rounded-lg shadow">
    @if (empty($cleanedData))
        <!-- File Upload Section -->
        <div class="border-2 border-dashed border-gray-400 rounded-md p-4 flex items-center justify-center cursor-pointer"
            wire:loading.class="opacity-50">
            <input type="file" wire:model="file" class="hidden" id="file-upload" />
            <label for="file-upload" class="text-center w-full">
                <p class="text-sm font-semibold text-gray-700">Click to Upload File</p>
                <p class="text-xs text-gray-500">CSV/XLSX files only</p>
            </label>
        </div>

        <!-- Instructions Section -->
        <div class="mt-2 text-center">
            <h3 class="text-gray-700 font-medium text-sm">How to Use:</h3>
            <ol class="text-xs text-gray-600 mt-1 space-y-1">
                <li>1. Click on the upload area to upload a CSV or XLSX file.</li>
                <li>2. Handle missing and duplicate values if prompted.</li>
                <li>3. Preview your cleaned data.</li>
                <li>4. Proceed to visualize your data using charts.</li>
            </ol>
        </div>

        <!-- Buttons Section -->
        <div class="mt-4 flex justify-center gap-2">
            <button
                wire:click="uploadRecentlyUsedFile"
                class="bg-pink-500 hover:bg-pink-600 text-white px-4 py-1 rounded-md shadow text-xs font-semibold"
            >
                Open Recently Used File
            </button>
        </div>
        @error('file')
            <p class="text-red-500 text-xs mt-2 text-center">{{ $message }}</p>
        @enderror
    @else
        <!-- File Details -->
        <h2 class="text-sm font-medium mb-4">File: <span class="text-blue-500">{{ $filename }}</span></h2>

        <!-- Cleaned Data Preview -->
        <div class="overflow-x-auto max-h-48 border border-gray-300 rounded-md p-2 bg-gray-50">
            <table class="min-w-full text-xs text-left">
                <thead class="bg-gray-100">
                    <tr>
                        @foreach ($headers as $header)
                            <th class="py-1 px-2 text-gray-700 font-medium">{{ $header }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @forelse ($cleanedData as $row)
                        <tr>
                            @foreach ($row as $cell)
                                <td class="py-1 px-2 text-gray-600">
                                    {{ $cell ?? '-' }}
                                </td>
                            @endforeach
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ count($headers) }}" class="py-2 px-4 text-center text-gray-500">
                                No data available
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Cleaning Summary -->
        <h4 class="text-sm font-semibold mt-4">Cleaning Summary</h4>
        <ul class="text-xs text-gray-600 space-y-1">
            <li><strong>Total Rows:</strong> {{ $cleaningSummary['total_rows'] ?? '0' }}</li>
            <li><strong>Cleaned Rows:</strong> {{ $cleaningSummary['cleaned_rows'] ?? '0' }}</li>
            <li><strong>Rows Removed (Nulls):</strong> {{ $cleaningSummary['rows_removed_due_to_nulls'] ?? '0' }}</li>
            <li><strong>Rows Removed (Duplicates):</strong> {{ $cleaningSummary['rows_removed_due_to_duplicates'] ?? '0' }}</li>
        </ul>

        @if ($showImputation && !empty($imputationOptions))
        <div class="mb-4 mt-4">
            <label for="imputationMethod" class="block text-sm font-medium text-gray-700">Handle Missing Values:</label>
            <select id="imputationMethod" wire:model="imputationMethod" wire:change="applyImputation"
                class="mt-1 block w-full border-gray-300 rounded-md">
                <option value="mean">Mean</option>
                <option value="median">Median</option>
                <option value="mode">Mode</option>
            </select>
            <p class="mt-1 text-xs text-gray-500">
                Select a method to handle missing values.
            </p>
        </div>
    @endif

        <!-- Proceed to Build Charts -->
        <div class="mt-4 flex justify-end">
            <a href="{{ route('build-charts') }}"
                class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-md shadow text-sm font-semibold">
                Proceed to Visualizing
            </a>
        </div>
    @endif

    <!-- Imputation Dropdown -->
   
</div>