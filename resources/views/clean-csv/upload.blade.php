{{-- /views/clean-csv/upload.blade.php --}}

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload File - CSV Data Cleaning</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    @vite('resources/css/app.css')
    @livewireStyles
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
            max-width: 700px;
        }
        .header-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }
        .gradient-header {
            background: linear-gradient(to right, #4c51bf, #3b82f6, #10b981);
            -webkit-background-clip: text;
            color: transparent;
            font-weight: 800;
            font-size: 1.3rem;
            text-align: center;
        }
        .gradient-btn {
            background: linear-gradient(to right, #4c51bf, #3b82f6, #10b981);
            color: white;
            font-weight: bold;
            transition: transform 0.2s ease-in-out;
            border: none;
            font-size: 1rem;
            padding: 10px;
        }
        .gradient-btn:hover {
            transform: scale(1.02);
            color: white;
        }
        .card-header-gradient {
            background: linear-gradient(to right, #4c51bf, #3b82f6, #10b981);
            color: white;
            font-weight: bold;
            font-size: 1.1rem;
        }
        .back-btn {
            color: #495057;
            background-color: #f8f9fa;
            border: 1px solid #495057;
            font-weight: bold;
            padding: 7px 20px;
            text-decoration: none;
            border-radius: 5px;
            transition: transform 0.2s ease-in-out;
            font-size: 1rem;
        }
        .back-btn:hover {
            transform: scale(1.02);
            border-color: #495057;
        }
    </style>
</head>
<body>
<livewire:authenticated_navbar />
<div class="background-blur"></div>

<div class="container mt-10 align-middle ">
    <!-- Header with Gradient and Back Button -->
    <div class="header-container ">
        <div class="div bg-white py-2 px-3 rounded-lg border-2 border-darkgray border-dashed">
            <h2 class="gradient-header">Upload CSV or XLSX File</h2>    
        </div>
        <a href="{{ route('project') }}" class="back-btn">Back</a>
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

    <!-- File Upload Form -->
    <div class="card border-2 border-black  ">
        <div class="card-header card-header-gradient border-b border-2 border-black">
            Upload File
        </div>
        <div class="card-body">
            <form action="{{ route('clean-csv.upload') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="mb-3">
                    <label for="file" class="form-label">Choose CSV or XLSX File</label>
                    <input type="file" name="file" id="file" class="form-control" accept=".csv,.txt,.xlsx,.xls" required>
                </div>
                <button type="submit" class="btn gradient-btn w-100">Upload File</button>
            </form>
        </div>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
