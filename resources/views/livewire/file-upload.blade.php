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
                <li>1. Click on the upload area to upload.</li>
                <li>2. Only CSV or XLSX files are supported.</li>
                <li>3. Handle missing values if prompted.</li>
                <li>4. Preview your cleaned data and start visualizing.</li>
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
                    @foreach ($cleanedData as $row)
                        <tr>
                            @foreach ($row as $cell)
                                <td class="py-1 px-2 text-gray-600">{{ $cell ?? '-' }}</td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Cleaning Summary -->
        <h4 class="text-sm font-semibold mt-4">Cleaning Summary</h4>
        <ul class="text-xs text-gray-600">
            <li>Total Rows: {{ $cleaningSummary['total_rows'] ?? '0' }}</li>
            <li>Cleaned Rows: {{ $cleaningSummary['cleaned_rows'] ?? '0' }}</li>
            <li>Rows Removed (Nulls): {{ $cleaningSummary['rows_removed_due_to_nulls'] ?? '0' }}</li>
            <li>Rows Removed (Duplicates): {{ $cleaningSummary['rows_removed_due_to_duplicates'] ?? '0' }}</li>
        </ul>
        <h4 class="text-sm font-medium mt-4">Missing Values Summary</h4>
<ul class="text-xs text-gray-600">
    @if (!empty($missingValuesSummary))
        @foreach ($missingValuesSummary as $column => $count)
            <li>{{ $column }}: {{ $count }} missing values</li>
        @endforeach
    @else
        <li>No missing values found in any column.</li>
    @endif
</ul>

    @endif


    @if ($showImputation && !empty($missingValuesSummary))
    <div class="mt-4 mb-4">
        <label for="imputationMethod" class="block text-sm font-semibold text-gray-700">Handle Missing Values:</label>
        <select id="imputationMethod" wire:model.defer="imputationMethod" wire:change="applyImputation"
        class="mt-1 block w-full border-gray-300 rounded-md">
   <option value="mean">Mean</option>
    <option value="median">Median</option>
    <option value="mode">Mode</option>
    <option value="standard_deviation">Standard Deviation</option>
</select>


        <p class="mt-1 text-xs text-gray-500">
            Select a method to handle missing values. Data preview will update automatically.
        </p>
    </div>
@endif



    <!-- Chart Selector -->
    @if ($isChartSelectorOpen)
        <livewire:chart-selector :headers="$headers" :previewData="$previewData" />
    @endif
</div>

<script>
    window.addEventListener('updated', event => {
        console.log(event.detail.message);
        alert(event.detail.message);
    });
</script>
