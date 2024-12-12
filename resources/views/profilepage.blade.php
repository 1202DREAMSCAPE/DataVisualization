<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $user->name }}'s Profile</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.3/dist/cdn.min.js"></script>
    <!-- Livewire Styles -->
    @livewireStyles
    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">

    <style>
        /* Blurred background image */
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
        .bg-overlay {
            background-color: rgba(255, 255, 255, 0.95);
        }
        .profile-pic-container {
            position: relative;
            display: inline-block;
        }

        /* Edit button overlay for profile picture */
        .edit-button-overlay {
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            background: linear-gradient(to right, rgba(79,70,229), rgba(59,130,246), rgba(16,185,129));
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 0 0 0.5rem 0.5rem;
            cursor: pointer;
            opacity: 0;
            transition: opacity 0.3s;
            width: 100%;
            text-align: center;
        }
        .profile-pic-container:hover .edit-button-overlay {
            opacity: 1;
        }

        .hidden-file-input {
            display: none;
        }

        /* Make the main container rounded */
        .main-container {
            border-radius: 1rem;
        }

        /* ---------------------------------------------
           Custom Animations and Styles
        --------------------------------------------- */

        /* Keyframes for rotating/cycling gradient border */
        @keyframes gradient-border {
            0% {
                border-image-source: linear-gradient(to right, #4f46e5, #3b82f6, #10b981);
            }
            50% {
                border-image-source: linear-gradient(to right, #4f46e5, #3b82f6, #10b981);
            }
            100% {
                border-image-source: linear-gradient(to right, #4f46e5, #3b82f6, #10b981);
            }
        }

        /* Gradient border class */
        .gradient-border {
            border: 4px solid transparent;
            border-image-slice: 1;
            border-image-source: linear-gradient(to right, #4f46e5, #3b82f6, #10b981);
            animation: gradient-border 3s infinite linear;
        }

        /* Keyframes for glowing gradient text */
        @keyframes glowing-gradient-text {
            0%, 100% {
                background-position: 0% 50%;
            }
            50% {
                background-position: 100% 50%;
            }
        }

        /* Class for gradient text animation */
        .gradient-text {
            background: linear-gradient(to right, #4f46e5, #3b82f6, #10b981);
            background-clip: text;
            -webkit-background-clip: text;
            color: transparent;
            background-size: 200% 100%;
            animation: glowing-gradient-text 3s infinite linear;
        }

        /* Keyframes for glowing gradient line */
        @keyframes glowing-gradient-line {
            0% {
                background-position: 0% 50%;
            }
            100% {
                background-position: 100% 50%;
            }
        }

        /* Class for glowing gradient divider line */
        .gradient-line {
            height: 2px;
            background: linear-gradient(to right, #4f46e5, #3b82f6, #10b981);
            background-size: 200% 100%;
            animation: glowing-gradient-line 3s infinite linear;
            opacity: 0.5; 
        }

        /* Gradient background animation for button */
        @keyframes glowing-gradient-background {
            0%, 100% {
                background-position: 0% 50%;
            }
            50% {
                background-position: 100% 50%;
            }
        }

        /* Class for gradient button */
        .gradient-button {
            display: inline-block;
            padding: 0.75rem 1.5rem;
            font-weight: bold;
            border-radius: 9999px; /* fully rounded */
            color: white;
            text-decoration: none;
            background: linear-gradient(to right, #4f46e5, #3b82f6, #10b981);
            background-size: 200% 100%;
            animation: glowing-gradient-background 3s infinite linear;
            transition: transform 0.3s ease;
        }

        .gradient-button:hover {
            transform: scale(1.05);
        }

        /* Class for gradient icon button (edit icon) */
        .gradient-icon-button {
            padding: 0.5rem;
            border-radius: 9999px;
            background: linear-gradient(to right, #4f46e5, #3b82f6, #10b981);
            background-size: 200% 100%;
            animation: glowing-gradient-background 3s infinite linear;
            transition: transform 0.3s ease, opacity 0.3s ease;
            border: none;
            opacity: 0.9;
        }

        .gradient-icon-button:hover {
            transform: scale(1.1);
            opacity: 1;
        }

    </style>
</head>
<body class="h-screen relative">
    <!-- Blurred Background Image -->
    <div class="background-blur"></div>

    <!-- Navbar -->
    <livewire:authenticated_navbar/>

    <!-- Profile Container -->
    <div class="container mx-auto px-4 py-10 bg-overlay rounded-lg shadow-lg mt-10 main-container relative">

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

        <!-- Profile Title with Gradient Glow -->
        <h1 class="text-4xl font-bold mb-6 text-center gradient-text bg-transparent p-2">{{ $user->name }}'s Profile</h1>

        <!-- Glowing Gradient Divider Line -->
        <div class="mx-auto w-2/3 mt-6 mb-6 gradient-line "></div>

        <div class="flex flex-col lg:flex-row items-center justify-center">
            <!-- Profile Picture and Edit Button -->
            <div class="mb-6 lg:mb-0 lg:mr-10 text-center">
                <div class="profile-pic-container">
                    <!-- Apply gradient-border and p-1 to show border around image -->
                    <div class="relative w-48 h-48 mx-auto rounded-full gradient-border p-1">
                        @if($user->profile_picture)
                        <img src="{{ asset('storage/app/public/' . $user->profile_picture) }}" alt="{{ $user->name }}'s Profile Picture"
                            
                                 class=" w-full h-full object-cover">
                        @else
                            <img src="{{ asset('images/default.png') }}" alt="Default Profile Picture"
                                 class=" w-full h-full object-cover">
                        @endif
                    </div>
                    <!-- Edit Button Overlay -->
                    <label for="profile_picture_input" class="edit-button-overlay font-bold cursor-pointer">Edit</label>

                    <!-- Hidden File Input -->
                    <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data" class="hidden-file-input" id="profile_picture_form">
                        @csrf
                        @method('PATCH')
                        <input type="file" name="profile_picture" id="profile_picture_input" accept="image/*" onchange="document.getElementById('profile_picture_form').submit();">
                    </form>
                </div>
            </div>

            <!-- Profile Details -->
            <div class="w-full lg:w-2/3">
                <ul class="list-group">
                    @php
                        $editableFields = [
                            [
                                'label' => 'Name',
                                'name' => 'name',
                                'value' => $user->name,
                                'type' => 'text',
                                'required' => true,
                                'route' => route('profile.update'),
                            ],
                            [
                                'label' => 'Username',
                                'name' => 'username',
                                'value' => $user->username,
                                'type' => 'text',
                                'required' => true,
                                'route' => route('profile.update'),
                            ],
                            [
                                'label' => 'Email',
                                'name' => 'email',
                                'value' => $user->email,
                                'type' => 'email',
                                'required' => true,
                                'route' => route('profile.update'),
                            ],
                        ];
                    @endphp

                    @foreach($editableFields as $field)
                        <x-editable-field 
                            :label="$field['label']"
                            :name="$field['name']"
                            :value="$field['value']"
                            :type="$field['type']"
                            :required="$field['required']"
                            :route="$field['route']"
                        />
                    @endforeach

                    <!-- Password -->
                    <li class="list-group-item" x-data="{ editing: false }">
                        <div class="flex justify-between items-center">
                            <div class="flex-1">
                                <strong>Password:</strong> ********
                            </div>
                            <div>
                                <button @click="editing = true" class="gradient-icon-button">
                                    <x-edit-icon />
                                </button>
                            </div>
                        </div>
                        <div x-show="editing" x-transition class="mt-3">
                            <form action="{{ route('profile.update') }}" method="POST" class="space-y-2">
                                @csrf
                                @method('PATCH')
                                <div class="flex flex-col">
                                    <label for="current_password" class="block text-sm font-medium text-gray-700">Current Password</label>
                                    <input type="password" name="current_password" id="current_password" required
                                        class="form-control">
                                </div>
                                <div class="flex flex-col">
                                    <label for="new_password" class="block text-sm font-medium text-gray-700">New Password</label>
                                    <input type="password" name="new_password" id="new_password" required
                                        class="form-control">
                                </div>
                                <div class="flex flex-col">
                                    <label for="new_password_confirmation" class="block text-sm font-medium text-gray-700">Confirm New Password</label>
                                    <input type="password" name="new_password_confirmation" id="new_password_confirmation" required
                                        class="form-control">
                                </div>
                                <div class="flex items-center space-x-2">
                                    <button type="submit" class="gradient-button text-white rounded-sm py-2 px-3 btn font-bol btn-success btn-sm">Save</button>
                                    <button type="button" @click="editing = false" class="btn bg-darkgray bg-opacity-80 text-white font-bold rounded-sm py-2 px-3  btn-secondary btn-sm">Cancel</button>
                                </div>
                            </form>
                        </div>
                    </li>

                    <!-- Joined Date (No Edit Button) -->
                    <li class="list-group-item">
                        <strong>Joined:</strong> {{ $user->created_at->format('F d, Y') }}
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Centered Gradient Button at the Bottom -->
    <div class="text-center mt-4 pb-4">
        <a href="/project" class="gradient-button">return to project</a>
    </div>

    <!-- Livewire Scripts -->
    @livewireScripts
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
