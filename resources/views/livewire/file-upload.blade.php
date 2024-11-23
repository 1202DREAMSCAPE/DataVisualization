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
        <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
        @enderror
    @else
        <!-- Data Cleaning Section -->
        <div class="border border-gray-300 rounded-lg p-4">
            <h2 class="text-lg font-bold mb-4">Data Cleaning</h2>

            <!-- Header Row Selection -->
            <div class="mb-4">
                <label class="block mb-2 text-sm font-medium text-gray-700">
                    Select Header Row
                </label>
                <select
                    wire:change="setHeaderRow($event.target.value)"
                    class="w-full border border-gray-300 rounded px-4 py-2"
                >
                    @foreach ($originalData as $index => $row)
                        <option value="{{ $index + 1 }}"
                            {{ $index + 1 == $headerRowIndex ? 'selected' : '' }}
                        >
                            Row {{ $index + 1 }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Excel Preview -->
            <div class="overflow-y-auto max-h-96 border border-gray-300 rounded-lg p-4 bg-gray-50">
                <table class="w-full text-sm text-left">
                    <!-- Headers -->
                    <thead class="bg-gray-100">
                        <tr class="text-gray-700 font-semibold">
                            @foreach ($headers as $key => $header)
                                <th class="py-2 px-4">
                                    {{ $header }}
                                    <button
                                        wire:click="deleteColumn('{{ $key }}')"
                                        class="ml-2 text-red-500 hover:text-red-700"
                                        title="Delete Column"
                                    >
                                        ✖
                                    </button>
                                </th>
                            @endforeach
                        </tr>
                    </thead>
                    <!-- Data Rows -->
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
            </div>
        </div>

        <!-- Upload New File Button -->
        <div class="mt-6 text-center">
            <button
                wire:click="$set('filename', null)"
                class="bg-gray-100 hover:bg-gray-200 text-gray-800 px-6 py-2 rounded-lg border border-gray-300"
            >
                Upload a New File
            </button>
        </div>
    @endif
</div>
