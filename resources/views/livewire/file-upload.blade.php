<div class="p-6 bg-white rounded-lg shadow">
    @if (!$filename)
        <!-- File Upload Section -->
        <div
            class="border-2 border-dashed border-gray-400 rounded-lg p-6 flex items-center justify-center cursor-pointer"
            wire:loading.class="opacity-50"
        >
            <input type="file" wire:model="file" class="hidden" id="file-upload" />
            <label for="file-upload" class="text-center w-full">
                <p class="text-lg font-bold text-gray-700">Click to Upload File Here</p>
                <p class="text-sm text-gray-500">CSV/XLSX files only</p>
            </label>
        </div>

        @error('file')
        <p class="text-red-500 text-sm mt-2 text-center">{{ $message }}</p>
        @enderror

        <!-- Instructions Section -->
        <div class="mt-4 text-center">
            <h3 class="text-gray-700 font-semibold text-lg">How to Use:</h3>
            <ol class="text-sm text-gray-600 mt-2 space-y-2">
                <li>1. Click on the upload area to upload.</li>
                <li>2. Only CSV or XLSX files are supported for preview and visualization.</li>
                <li>3. After uploading, preview your data and start the visualization process.</li>
            </ol>
        </div>

      <!-- Buttons Section -->
<div class="mt-6 flex justify-center gap-4">
    <!-- Open Recently Used File Button -->
    <button
        wire:click="uploadRecentlyUsedFile"
        class="bg-pink-500 hover:bg-pink-600 text-white px-6 py-2 rounded-lg shadow font-semibold"
    >
        Open Recently Used File
    </button>
</div>

<!-- Success/Error Messages -->
<div class="mt-4 text-center">
    @if (session()->has('success'))
        <div class="text-green-500 font-semibold">
            {{ session('success') }}
        </div>
    @endif

    @if (session()->has('error'))
        <div class="text-red-500 font-semibold">
            {{ session('error') }}
        </div>
    @endif
</div>

    @else
        <!-- File Preview Section -->
        <div class="border border-gray-300 rounded-lg p-4">
            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3 mb-4">
                <h2 class="text-lg font-bold text-center sm:text-left">
                    Previewing File: <span class="text-blue-500">{{ $filename }}</span>
                </h2>
                <!-- Start Visualizing Button -->
                <button wire:click="openChartSelector"
                    class="w-full sm:w-auto bg-gradient-to-r from-red-400 via-yellow-400 to-blue-500 text-white px-4 py-2 rounded-lg shadow font-semibold">
                    Start Visualizing
                </button>
            </div>

            @if (!empty($headers) && !empty($previewData))
                <!-- Mobile View (Card Layout) -->
                <div class="block sm:hidden space-y-3">
                    @foreach ($previewData as $index => $row)
                        <div class="bg-white rounded-lg border border-gray-200 p-3">
                            <div class="font-semibold text-gray-700 mb-2 text-sm">Row {{ $loop->iteration }}</div>
                            @foreach ($headers as $headerIndex => $header)
                                <div class="flex justify-between py-1.5 border-b border-gray-100 last:border-0 text-sm">
                                    <span class="text-gray-600 font-medium">{{ $header }}:</span>
                                    <span class="text-gray-800 ml-4 break-words">{{ $row[$headerIndex] }}</span>
                                </div>
                            @endforeach
                        </div>
                    @endforeach
                </div>

                <!-- Desktop View (Original Table) -->
                <div class="hidden sm:block overflow-x-auto overflow-y-auto max-h-96 border border-gray-300 rounded-lg p-4 bg-gray-50">
                    <table class="min-w-full text-sm text-left">
                        <thead class="bg-gray-100">
    <tr>
        <!-- Removed the # column -->
        @foreach ($headers as $header)
            <th class="py-2 px-4 text-gray-700 font-semibold">{{ $header }}</th>
        @endforeach
    </tr>
</thead>
<tbody>
    @foreach ($previewData as $index => $row)
        <tr>
            <!-- Removed the # column -->
            @foreach ($row as $cell)
                <td class="py-2 px-4 text-gray-600">{{ $cell }}</td>
            @endforeach
        </tr>
    @endforeach
</tbody>

                    </table>
                </div>
            @else
                <!-- Fallback if no data is available -->
                <div class="overflow-y-auto max-h-96 border border-gray-300 rounded-lg p-4 bg-gray-50">
                    <p class="text-gray-700 text-sm text-center">
                        No preview data available. Please check the uploaded file.
                    </p>
                </div>
            @endif
        </div>

        <!-- Buttons Section -->
<div class="mt-6 flex flex-col sm:flex-row justify-center gap-3 sm:space-x-4 sm:gap-0">
    <!-- Upload New File Button -->
    <button
        wire:click="$set('filename', null)"
        class="w-full sm:w-auto bg-gray-100 hover:bg-gray-200 text-gray-800 px-6 py-2 rounded-lg border border-gray-300 shadow"
    >
        Upload a New File
    </button>
</div>

    @endif

    <!-- Chart Selector -->
    @if ($isChartSelectorOpen)
        <livewire:chart-selector :headers="$headers" :previewData="$previewData" />
    @endif
</div>