{{-- /views/clean-csv/cleaned.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cleaned File - CSV Data Cleaning</title>
    @vite('resources/css/app.css')
    @livewireStyles
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Tailwind CSS -->
    <script src="https://cdn.jsdelivr.net/npm/tailwindcss@3.0.24/dist/tailwind.min.css"></script>
    <style>
        body {
            background-color: #f8f9fa;
            font-family: Arial, sans-serif;
        }
        .background-blur {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: url('{{ asset('images/blobgif.gif') }}');
            background-size: cover;
            background-position: center;
            filter: blur(8px);
            -webkit-filter: blur(8px);
            z-index: -1;
        }
        .container {
            max-width: 900px;
        }
        .table-responsive {
            max-height: 400px;
            overflow-y: auto;
        }
        .table thead th {
            position: sticky;
            top: 0;
            background-color: rgba(255, 255, 255, 0.8);
            color: #343a40;
            z-index: 1;
        }
        .summary-list {
            list-style-type: none;
            padding-left: 0;
        }
        .summary-list li {
            margin-bottom: 10px;
            font-size: 1rem;
        }
        .summary-list .badge {
            font-size: 0.9rem;
            margin-left: 10px;
        }
        .badge-custom {
            background: linear-gradient(to right, #ee8090, #d44118);
        }
        .badge-info {
            background: linear-gradient(to right, #28a745, #4caf50);
        }
        .btn-group {
            display: flex;
            gap: 10px;
            justify-content: center;
        }
        footer {
            margin-top: 50px;
            text-align: center;
        }
        .summary-header {
            background: linear-gradient(to right, #4c51bf, #3b82f6, #10b981);
            color: white;
            font-weight: bold;
        }
        .card-header-gradient {
            background: linear-gradient(to right, #4c51bf, #3b82f6, #10b981);
            color: white;
            font-weight: bold;
        }
        .header-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        .gradient-header {
            background: linear-gradient(to right, #4c51bf, #3b82f6, #10b981);
            -webkit-background-clip: text;
            color: transparent;
            font-weight: 800;
            font-size: 1.3rem;
        }
        .gradient-btn {
            background: linear-gradient(to right, #4c51bf, #3b82f6, #10b981);
            color: white;
            font-weight: bold;
            transition: transform 0.2s ease-in-out;
            border: none;
        }
        .gradient-btn:hover {
            transform: scale(1.01);
            color: white; /* Ensures text remains white */
        }
        .back-btn {
            color: #495057;
            background-color: #f8f9fa;
            border: 1px solid #495057;
            font-weight: bold;
            padding: 5px 15px;
            text-decoration: none;
            border-radius: 5px;
            transition: transform 0.2s ease-in-out;
        }
        .back-btn:hover {
            transform: scale(1.01);
            border-color: #495057; /* Ensures border color stays intact */
        }
        .table tbody tr:hover {
            transform: scale(1.01);
            cursor: pointer;
        }
    </style>
</head>
<body>
<livewire:authenticated_navbar />
<div class="background-blur"></div>

<div class="container mt-10">
    <!-- Header with Gradient and Back Button -->
    <div class="header-container">
        <div class="div bg-white py-2 px-3 rounded-lg border-2 border-darkgray border-dashed">
            <h2 class="gradient-header">Cleaned File</h2>
        </div>
        <a href="{{ route('clean-csv.preview') }}" class="back-btn">Back</a>
    </div>

    <!-- Success Message -->
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <!-- Error Messages -->
    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Cleaned File Preview -->
    <div class="card mb-4 border-2 border-black">
        <div class="card-header card-header-gradient border-b border-2 border-black">
            Cleaned File Preview (First 5 Rows)
        </div>
        <div class="card-body table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        @foreach($cleanedPreview['headers'] as $header)
                            <th>{{ $header }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach($cleanedPreview['rows'] as $row)
                        <tr>
                            @foreach($row as $cell)
                                <td>{{ $cell }}</td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Summary of Changes -->
    <div class="card mb-4 border-2 border-black">
        <div class="card-header summary-header border-b border-2 border-black">
            Summary of Changes
        </div>
        <div class="card-body">
            <ul class="summary-list">
                <li>
                    <strong>Total Rows Removed:</strong> {{ $summary['total_rows_removed'] }}
                    <span class="badge badge-info">{{ $summary['percentage_rows_removed'] }}%</span>
                </li>
                <li>
                    <strong>Due to Nulls:</strong> {{ $summary['rows_removed_due_to_nulls'] }}
                    <span class="badge badge-custom">Null Entries</span>
                </li>
                <li>
                    <strong>Due to Non-ASCII Characters:</strong> {{ $summary['rows_removed_due_to_non_ascii'] }}
                    <span class="badge badge-custom">Encoding Issues</span>
                </li>
                <li>
                    <strong>Due to Duplicates:</strong> {{ $summary['rows_removed_due_to_duplicates'] }}
                    <span class="badge badge-custom">Duplicates</span>
                </li>
            </ul>
            <div class="btn-group mt-3">
                <a href="{{ route('clean-csv.download') }}" class="btn gradient-btn">Download Cleaned File</a>
                <a href="{{ route('project') }}" class="btn back-btn">Return to Project</a>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
