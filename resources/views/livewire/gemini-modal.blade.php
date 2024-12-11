<div>
    <div x-show="openModal" class="fixed inset-0 flex items-center justify-center bg-gray-900 bg-opacity-50">
        <div class="bg-white rounded-lg shadow p-6 w-full max-w-lg relative">
            <h3 class="text-lg font-bold">Generate CSV File</h3>
            <form wire:submit.prevent="generateCsvFile">
                <label class="block mt-4">
                    <span class="text-gray-700">Enter your prompt:</span>
                    <input
                        type="text"
                        wire:model="prompt"
                        class="block w-full mt-1 rounded-md border-gray-300 shadow-sm focus:ring focus:ring-opacity-50 focus:ring-blue-300"
                    />
                </label>
                <div class="mt-6 flex justify-end">
                    <button type="button" @click="openModal = false" class="bg-gray-100 px-4 py-2 rounded-lg shadow">
                        Cancel
                    </button>
                    <button type="submit" class="ml-3 bg-blue-500 text-white px-4 py-2 rounded-lg shadow">
                        Generate
                    </button>
                </div>
            </form>
            @if (session('success'))
                <div class="mt-4 text-green-500 font-semibold">
                    {{ session('success') }}
                </div>
                <div class="mt-2">
                    <a href="#" wire:click.prevent="downloadCsvFile" class="text-blue-500 underline">
                        Download Generated CSV File
                    </a>
                </div>
            @endif
            @if (session('error'))
                <div class="mt-4 text-red-500 font-semibold">
                    {{ session('error') }}
                </div>
            @endif
        </div>
    </div>
</div>
