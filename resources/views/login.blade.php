<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VizOra - LogIn </title>
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

                <!-- Session Status -->
                @if (session('status'))
                    <div class="text-green-500 text-center mb-4">{{ session('status') }}</div>
                @endif

                <form wire:submit.prevent="login">
                    <!-- Username -->
                    <div class="mb-2 flex items-center justify-center">
                        <input wire:model="form.username" id="username" type="text" placeholder="Username"
                            class="w-3/4 border-[1px] border-darkgray rounded-lg px-3 py-2 text-xs sm:text-sm focus:outline-none focus:ring focus:ring-orange-300" />
                        @error('form.username')
                            <span class="text-red-500 text-xs">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Password -->
                    <div class="mb-3 flex items-center justify-center"">
                        <input wire:model="form.password" id="password" type="password" placeholder="Password"
                            class="w-3/4  border-[1px] border-darkgray rounded-lg px-3 py-2 text-xs sm:text-sm focus:outline-none focus:ring focus:ring-orange-300" />
                        @error('form.password')
                            <span class="text-red-500 text-xs">{{ $message }}</span>
                        @enderror
                    </div>

                    
                    <div class = "w-full items-center justify-center ">
                        <!--
                        <div class="flex items-center justify-between px-12 mt-4 mb-6 text-xs sm:text-sm">
                            <label class="flex items-center">
                                <input wire:model="form.remember" id="remember" type="checkbox"
                                    class="form-checkbox h-4 w-4 text-orange-600">
                                <span class="ml-2 text-darkgray">Remember Me</span>
                            </label>
                            
                            <a href="{{ route('password.request') }}"
                                class="text-orange no-underline hover:underline">Forgot password?</a>
                        </div>-->
    
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
                    Don’t have an account yet? <a href="{{ route('register') }}"
                        class="text-darkgray italic font-bold hover:underline hover:text-pink-500">Sign Up Now!</a>
                </p>
            </div>
        </div>
    </div>

    @livewireScripts
</body>

</html>
