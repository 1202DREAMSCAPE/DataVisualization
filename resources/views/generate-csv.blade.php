<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Generate AI CSV</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 p-8">
    <div class="max-w-lg mx-auto bg-white shadow-lg rounded-lg p-6">
        <h1 class="text-2xl font-bold mb-4 text-center">Generate AI CSV File</h1>
        <form method="POST" action="{{ route('generate-text') }}">
            @csrf
            <div class="mb-4">
                <label for="prompt" class="block text-sm font-medium text-gray-700">Enter Your Prompt</label>
                <input
                    type="text"
                    name="prompt"
                    id="prompt"
                    placeholder="e.g., Generate a dataset with Name, Age, and Salary"
                    class="w-full border border-gray-300 rounded px-4 py-2"
                    required
                />
            </div>
            <div class="text-center">
                <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                    Generate CSV
                </button>
            </div>
        </form>
        @if(session('generatedData'))
            <!-- Display Generated Data -->
            <div class="mt-6">
                <h2 class="text-lg font-bold mb-4">Generated Data Preview</h2>
                <div class="overflow-y-auto max-h-96 border border-gray-300 rounded-lg p-4 bg-gray-50">
                    <table class="w-full text-sm text-left">
                        <!-- Headers -->
                        <thead class="bg-gray-100">
                            <tr class="text-gray-700 font-semibold">
                                @foreach (session('headers') as $header)
                                    <th class="py-2 px-4">{{ $header }}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <!-- Data Rows -->
                        <tbody>
                            @foreach (session('generatedData') as $row)
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
</body>
</html>
