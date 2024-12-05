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
        <!-- File Preview Section -->
        <div class="border border-gray-300 rounded-lg p-4">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg font-bold">
                    Previewing File: <span class="text-blue-500">{{ $filename }}</span>
                </h2>
                <div x-data="{ open: false }">
                    <!-- Start Visualizing Button -->
                    <button
                        @click="open = true"
                        class="bg-gradient-to-r from-red-400 via-yellow-400 to-blue-500 text-white px-4 py-2 rounded-lg shadow font-semibold animate-rainbow"
                    >
                        Start Visualizing
                    </button>

                    <!-- Modal -->
                    <div
                        x-show="open"
                        class="fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center z-50 p-4"
                        style="display: none;"
                    >
                        <div class="bg-white rounded-lg p-6 max-w-xl w-full relative">
                            <!-- Close Button -->
                            <button @click="open = false" class="absolute top-2 right-2 text-gray-500 hover:text-gray-800">
                                ✖
                            </button>
                            <h2 class="text-xl font-bold text-center mb-4">What chart do you want?</h2>

                            <!-- Chart Options -->
                            <div class="grid grid-cols-2 sm:grid-cols-3 gap-6">
                                <div class="flex flex-col items-center">
                                    <img src="/path-to-bar-chart.jpg" alt="Bar Chart" class="w-24 h-24 rounded shadow" />
                                    <p class="mt-2 font-semibold text-sm md:text-base text-center">Bar Chart</p>
                                </div>
                                <div class="flex flex-col items-center">
                                    <img src="/path-to-pie-chart.jpg" alt="Pie Chart" class="w-24 h-24 rounded shadow" />
                                    <p class="mt-2 font-semibold text-sm md:text-base text-center">Pie Chart</p>
                                </div>
                                <div class="flex flex-col items-center">
                                    <img src="/path-to-word-cloud.jpg" alt="Word Cloud" class="w-24 h-24 rounded shadow" />
                                    <p class="mt-2 font-semibold text-sm md:text-base text-center">Word Cloud</p>
                                </div>
                                <div class="flex flex-col items-center">
                                    <img src="/path-to-gauge-chart.jpg" alt="Gauge Chart" class="w-24 h-24 rounded shadow" />
                                    <p class="mt-2 font-semibold text-sm md:text-base text-center">Gauge Chart</p>
                                </div>
                                <div class="flex flex-col items-center">
                                    <img src="/path-to-radar-chart.jpg" alt="Radar Chart" class="w-24 h-24 rounded shadow" />
                                    <p class="mt-2 font-semibold text-sm md:text-base text-center">Radar Chart</p>
                                </div>
                            </div>

                            <!-- Save Button -->
                            <div class="text-right mt-6">
                                <button
                                    @click="open = false"
                                    class="bg-yellow-500 hover:bg-yellow-600 text-white px-6 py-2 rounded-lg"
                                >
                                    Proceed!
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @if (!empty($headers) && !empty($previewData))
                <!-- Table Preview -->
                <div class="overflow-y-auto max-h-96 border border-gray-300 rounded-lg p-4 bg-gray-50">
                    <table class="w-full text-sm text-left">
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
</div>
