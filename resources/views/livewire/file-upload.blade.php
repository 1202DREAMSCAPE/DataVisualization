<div x-data="{ isSticky: true }" class="p-6 bg-white rounded-lg shadow">
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
        <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
        @enderror
    @else
        <!-- File Name and Preview -->
        <div class="border border-gray-300 rounded-lg p-4">
            <!-- Sticky Header Toggle -->
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center space-x-2">
                    <label for="sticky-toggle" class="text-sm font-medium text-gray-700">
                        Sticky Headers
                    </label>
                    <input
                        id="sticky-toggle"
                        type="checkbox"
                        x-model="isSticky"
                        class="w-5 h-5 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                    />
                </div>
            </div>

            <!-- File Name Section -->
            <div class="flex items-center mb-4">
                <input
                    type="text"
                    value="{{ $filename }}"
                    readonly
                    class="w-full border border-gray-300 rounded-l-lg px-4 py-2 bg-gray-100 text-sm font-medium"
                />
            <button
                wire:click="proceedToCleaning"
                class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded-r-lg flex items-center"
            >
                <span>→</span>
            </button>

            </div>

            <!-- Scrollable Preview Section -->
            <div class="overflow-y-auto max-h-64 border border-gray-300 rounded-lg p-4 bg-gray-50">
                @if ($previewData)
                    <table class="w-full text-sm text-left">
                        <!-- Table Headers -->
                        <thead class="bg-gray-100">
                            <tr
                                class="text-gray-700 font-semibold"
                                :class="{ 'sticky top-0 bg-gray-100 z-10': isSticky }"
                            >
                                @foreach ($headers as $header)
                                    <th class="py-2 px-4">{{ $header }}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <!-- Table Rows -->
                        <tbody>
                            @foreach ($previewData as $row)
                                <tr class="border-b border-gray-200">
                                    @foreach ($row as $cell)
                                        <td class="py-2 px-4">{{ $cell }}</td>
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <p class="text-gray-500 text-sm">No preview available for this file.</p>
                @endif
            </div>
        </div>

        <!-- Upload New File Button -->
        <div class="mt-6 text-center">
            <button
                wire:click="resetComponent"
                class="bg-gray-100 hover:bg-gray-200 text-gray-800 px-6 py-2 rounded-lg border border-gray-300"
            >
                Upload a New File
            </button>
        </div>
    @endif
</div>
