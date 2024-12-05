<div class="p-6 bg-white rounded-lg shadow">
<!-- Generate Text Form -->
<form method="POST" action="/huggingface/generate">
        @csrf
        <div class="mb-4">
            <label for="prompt" class="block text-sm font-medium text-gray-700">Enter your prompt</label>
            <input
                type="text"
                name="prompt"
                id="prompt"
                placeholder="e.g., Generate a dataset with Name, Age, and Salary"
                class="w-full border border-gray-300 rounded px-4 py-2"
                required
            />
        </div>
        <div class="text-center mb-4">
            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                Generate Text
            </button>
        </div>
    </form>

    @if (!$filename && empty($generatedData))
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
        <!-- Data Preview Section -->
        <div class="border border-gray-300 rounded-lg p-4">
            <h2 class="text-lg font-bold mb-4">Data Preview</h2>

            <div class="overflow-y-auto max-h-96 border border-gray-300 rounded-lg p-4 bg-gray-50">
                <table class="w-full text-sm text-left">
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
    @endif
</div>
