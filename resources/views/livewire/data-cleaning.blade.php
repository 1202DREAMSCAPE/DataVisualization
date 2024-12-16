<div>
    <!-- File Upload Section -->
    @if (!$filename)
    
        <div class="border-2 border-dashed border-gray-400 rounded-lg p-6 flex items-center justify-center">
            <input type="file" wire:model="file" class="hidden" id="file-upload" />
            <label for="file-upload" class="text-center w-full cursor-pointer">
                <p class="text-lg font-bold text-gray-700">Click to Upload File Here</p>
                <p class="text-sm text-gray-500">CSV/XLSX files only</p>
            </label>
        </div>

        @error('file')
        <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
        @enderror
    @else
        <!-- File Preview -->
        <div class="mt-4">
            <h2 class="text-lg font-bold">Previewing File: <span class="text-blue-500">{{ $filename }}</span></h2>
            <button wire:click="openModal" class="bg-blue-500 text-white px-4 py-2 rounded-lg shadow mt-4">
                Start Data Cleaning
            </button>
        </div>
    @endif

    <!-- Modal -->
    @if ($isModalOpen)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white rounded-lg shadow-lg p-6 max-w-4xl w-full">
                <!-- Modal Header -->
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-lg font-bold">Data Cleaning</h2>
                    <button wire:click="closeModal" class="text-gray-500 hover:text-gray-700">&times;</button>
                </div>

                <!-- Modal Body -->
                <div>
                    <h3 class="text-md font-semibold mb-2">Preview Data</h3>
                    <div class="overflow-x-auto border border-gray-300 rounded-lg mb-4">
                        <table class="min-w-full text-sm">
                            <thead class="bg-gray-100">
                                <tr>
                                    @foreach ($headers as $header)
                                        <th class="py-2 px-4 text-gray-700 font-semibold">{{ $header }}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($cleanedData as $row)
                                    <tr>
                                        @foreach ($row as $cell)
                                            <td class="py-2 px-4 text-gray-600">{{ $cell }}</td>
                                        @endforeach
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Data Cleaning Actions -->
                    <div class="flex space-x-4">
                        <button wire:click="deleteEmptyRows" class="bg-red-500 text-white px-4 py-2 rounded-lg shadow">
                            Remove Empty Rows
                        </button>
                        <button wire:click="resetData" class="bg-gray-500 text-white px-4 py-2 rounded-lg shadow">
                            Reset Data
                        </button>
                        <button wire:click="saveCleanedData" class="bg-green-500 text-white px-4 py-2 rounded-lg shadow">
                            Save and Close
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
