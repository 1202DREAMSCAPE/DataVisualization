<div class="p-8 rounded-lg text-center">
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6">
        <!-- Header and Button -->
        <h2 class="text-2xl font-extrabold mb-4 sm:mb-0">Generated Reports</h2>
        <a 
            href="{{ route('build-charts', ['file' => session('last_file')]) }}" 
            class="inline-block px-4 py-2 text-md font-dmSerif text-white bg-blue-500 rounded-md hover:bg-blue-600"
        >
            + Create New Report
        </a>
    </div>
    @if ($reports->isEmpty())
        <p class="text-lg text-gray-500">No reports found. Generate a report to get started!</p>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach ($reports as $report)
                <div class="relative flex flex-col items-start p-6 bg-gray-50 rounded-lg shadow hover:shadow-lg transition-shadow">
                    <!-- Delete "X" Button -->
                    <button 
                        type="button"
                        class="absolute top-2 right-2 text-gray-500 hover:text-red-500"
                        onclick="openDeleteModal({{ $report->id }}, '{{ $report->report_name }}')"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>

                    <div class="flex items-center mb-4">
                        <svg class="w-6 h-6 text-pink-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 21H5a2 2 0 01-2-2V5a2 2 0 012-2h7l2 2h5a2 2 0 012 2v12a2 2 0 01-2 2z"></path>
                        </svg>
                        <h3 class="ml-3 text-lg font-semibold text-gray-700">{{ $report->report_name }}</h3>
                    </div>
                    <p class="text-sm text-gray-500 mb-4">{{ $report->created_at->format('M d, Y') }}</p>
                    <a 
                        href="{{ asset($report->file_path) }}" 
                        target="_blank" 
                        class="inline-block px-4 py-2 mt-auto text-sm text-white bg-green-500 rounded-md hover:bg-green-600"
                    >
                        View Report
                    </a>
                </div>
            @endforeach
        </div>
    @endif

    <!-- Delete Confirmation Modal -->
    <div id="deleteModal" class="fixed inset-0 flex items-center justify-center bg-gray-900 bg-opacity-50 hidden">
        <div class="bg-white rounded-lg shadow-lg p-6 w-1/3">
            <h3 class="text-lg font-bold mb-4">Confirm Deletion</h3>
            <p class="text-gray-600 mb-6">Are you sure you want to delete <span id="reportName" class="font-semibold text-blue-600"></span>?</p>
            <form id="deleteForm" method="POST">
                @csrf
                @method('DELETE')
                <div class="flex justify-center">
                    <button 
                        type="button" 
                        class="px-4 py-2 text-sm text-gray-700 bg-gray-200 rounded-md hover:bg-gray-300 mr-2"
                        onclick="closeDeleteModal()"
                    >
                        Cancel
                    </button>
                    <button 
                        type="submit" 
                        class="px-4 py-2 text-sm text-white bg-red-500 rounded-md hover:bg-red-600"
                    >
                        Delete
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>


<script>
    function openDeleteModal(reportId, reportName) {
        // Show modal
        const modal = document.getElementById('deleteModal');
        modal.classList.remove('hidden');

        // Set the report name in the modal
        document.getElementById('reportName').textContent = reportName;

        // Update the form action with the report ID
        const form = document.getElementById('deleteForm');
        form.action = `/report/${reportId}`;
    }

    function closeDeleteModal() {
        // Hide modal
        const modal = document.getElementById('deleteModal');
        modal.classList.add('hidden');
    }
</script>
