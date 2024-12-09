{{-- /views/clean-csv/preview.blade.php --}}

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Preview File - CSV Data Cleaning</title>
    @vite('resources/css/app.css')
    @livewireStyles
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
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
        .table td {
            background-color: rgba(255, 255, 255, 0.5);
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
    </style>
</head>
<body>
<livewire:authenticated_navbar />
<div class="background-blur"></div>

<div class="container mt-10 ">
    <!-- Header with Gradient and Back Button -->
    <div class="header-container">
        <div class="div bg-white py-2 px-3 rounded-lg border-2 border-darkgray border-dashed">
            <h2 class="gradient-header">Preview File</h2>
        </div>
        <a href="{{ route('clean-csv.upload') }}" class="back-btn">Back</a>
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

    <!-- Original File Preview -->
    <div class="card mb-4 border-2 border-black">
        <div class="card-header border-b border-2 border-black" style="background: linear-gradient(to right, #4c51bf, #3b82f6, #10b981); color: white; font-weight: bold;">
            File Preview
        </div>
        <div class="card-body table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        @foreach($previewOriginal['headers'] as $header)
                            <th>{{ $header }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach($previewOriginal['rows'] as $row)
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

    <!-- Clean File Button -->
    <div class="d-flex justify-content-center mb-5 ">
        <form action="{{ route('clean-csv.clean') }}" method="POST">
            @csrf
            <button type="submit" class="btn gradient-btn">Clean File</button>
        </form>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
