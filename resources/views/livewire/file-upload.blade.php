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
    @else
        <!-- File Preview Section -->
        <div class="border border-gray-300 rounded-lg p-4">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg font-bold">
                    Previewing File: <span class="text-blue-500">{{ $filename }}</span>
                </h2>
                <!-- Start Visualizing Button -->
                <button wire:click="openChartSelector"
                    class="bg-gradient-to-r from-red-400 via-yellow-400 to-blue-500 text-white px-4 py-2 rounded-lg shadow font-semibold">
                Start Visualizing
            </button>

            </div>

            @if (!empty($headers) && !empty($previewData))
                <!-- Table Preview with Horizontal and Vertical Scroll -->
                <div class="overflow-x-auto overflow-y-auto max-h-96 border border-gray-300 rounded-lg p-4 bg-gray-50">
                    <table class="min-w-full text-sm text-left">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="py-2 px-4 text-gray-700 font-semibold text-center">#</th>
                                @foreach ($headers as $header)
                                    <th class="py-2 px-4 text-gray-700 font-semibold">{{ $header }}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($previewData as $index => $row)
                                <tr>
                                    <td class="py-2 px-4 text-gray-600 text-center">{{ $loop->iteration }}</td>
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
        <div class="mt-6 flex justify-center space-x-4">
            <button
                wire:click="startDataCleaning"
                class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-2 rounded-lg shadow"
            >
                Start Data Cleaning
            </button>
            <button
                wire:click="$set('filename', null)"
                class="bg-gray-100 hover:bg-gray-200 text-gray-800 px-6 py-2 rounded-lg border border-gray-300 shadow"
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
