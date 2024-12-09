<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VizOra - LogIn</title>
    @vite('resources/css/app.css')
    @livewireStyles
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=DM+Serif+Display&display=swap" rel="stylesheet">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.3/dist/cdn.min.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>

<body>
    <!-- Single Root Element -->
    <div 
        class="min-h-screen flex flex-col bg-cover bg-center" 
        style="background-image: url('{{ asset('images/authgif.gif') }}');"
    >    
        <livewire:navbar />

        <!-- Main Content -->
        <div class="flex items-center justify-center flex-grow px-4">
            <div class="bg-white rounded-[40px] shadow-lg max-w-md w-full py-6 px-4 xl:py-12 xl:px-12">
                <h1 class="text-xl sm:text-2xl font-bold text-darkgray text-center mb-1 font-serif">Hello!</h1>
                <p class="text-center text-xs sm:text-sm text-darkgray mb-2 italic">Log In To Your Account</p>

                <!-- Global Error Message -->
                @if ($errors->has('username'))
                    <div class="bg-red-100 text-red-500 text-center rounded-md px-3 py-2 text-sm mb-4">
                        {{ $errors->first('username') }}
                    </div>
                @endif

                <!-- Session Status -->
                @if (session('status'))
                    <div class="text-green-500 text-center mb-4">{{ session('status') }}</div>
                @endif

                <form action="{{ route('login.store') }}" method="POST">
                    @csrf
                    <!-- Username -->
                    <div class="mb-2 flex flex-col items-center">
                        <input name="username" id="username" type="text" placeholder="Username"
                            class="w-3/4 border-[1px] border-darkgray rounded-lg px-3 py-2 text-xs sm:text-sm focus:outline-none focus:ring focus:ring-orange-300" required />

                    </div>

                    <!-- Password -->
                    <div class="mb-3 flex flex-col items-center">
                        <input name="password" id="password" type="password" placeholder="Password"
                            class="w-3/4 border-[1px] border-darkgray rounded-lg px-3 py-2 text-xs sm:text-sm focus:outline-none focus:ring focus:ring-orange-300" required />
                        @error('password')
                            <span class="text-red-500 text-xs mt-1 w-3/4 text-left">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Login Button -->
                    <div class="flex justify-center">
                        <button type="submit"
                            class="bg-warmyellow font-bold border-[1px] border-darkgray text-darkgray w-3/4 sm:w-1/2 py-2 rounded-full hover:bg-lightyellow hover:text-white hover:font-bold text-xs sm:text-sm">
                            Login
                        </button>
                    </div>
                </form>

                <p class="mt-6 text-center text-xs sm:text-sm">
                    Don’t have an account yet? <a href="{{ route('signup') }}"
                        class="text-darkgray italic font-bold hover:underline hover:text-pink-500">Sign Up Now!</a>
                </p>
            </div>
        </div>
    </div>

    @livewireScripts
</body>
</html>
