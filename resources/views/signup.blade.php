<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VizOra Sign Up</title>
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
    >            <!-- Header with Alpine.js for responsive menu -->
       <livewire:navbar />

        <!-- Main Content -->
        <div class="flex items-center justify-center flex-grow p-6">
            <div class="bg-white rounded-[40px] shadow-lg max-w-md w-full py-6 px-4 xl:py-12 xl:p-24">
                <h1 class="text-xs sm:text-sm  text-darkgray mb-1  text-left">Get Started</h1>
                <h2 class="text-2xl sm:text-3xl text-darkgray mb-1 font-serif font-bold text-left">Sign Up for <span
                        class="font-bold italic">Free.</span></h2>
                <p class="text-left text-xs sm:text-sm text-darkgray mb-2 ">
                    Already have an account? <a href="{{ route('login') }}"
                        class="text-green-600 font-bold hover:underline italic">Log in</a>
                </p>

                <form wire:submit.prevent="register">
                    <!-- Name -->
                    <div class="mb-2">
                        <input wire:model="form.name" id="name" type="text" placeholder="Name"
                            class="w-full border-[1px] border-darkgray rounded-lg px-3 py-2 text-xs sm:text-sm focus:outline-none focus:ring focus:ring-orange-300" />
                        @error('form.name')
                            <span class="text-red-500 text-xs">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Email -->
                    <div class="mb-2">
                        <input wire:model="form.email" id="email" type="email"
                            placeholder="Email Address (e.g., user@example.com)"
                            class="w-full border-[1px] border-darkgray rounded-lg px-3 py-2 text-xs sm:text-sm focus:outline-none focus:ring focus:ring-orange-300" />
                        @error('form.email')
                            <span class="text-red-500 text-xs">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Username -->
                    <div class="mb-2">
                        <input wire:model="form.username" id="username" type="text" placeholder="Username"
                            class="w-full border-[1px] border-darkgray rounded-lg px-3 py-2 text-xs sm:text-sm focus:outline-none focus:ring focus:ring-orange-300" />
                        @error('form.username')
                            <span class="text-red-500 text-xs">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Password and Confirm Password -->
                    <div class="mb-3 grid grid-cols-2 gap-3">
                        <input wire:model="form.password" id="password" type="password" placeholder="Password"
                            class="border-[1px] border-darkgray rounded-lg px-3 py-2 text-xs sm:text-sm focus:outline-none focus:ring focus:ring-orange-300" />
                        <input wire:model="form.password_confirmation" id="password_confirmation" type="password"
                            placeholder="Confirm Password"
                            class="border-[1px] border-darkgray rounded-lg px-3 py-2 text-xs sm:text-sm focus:outline-none focus:ring focus:ring-orange-300" />
                        @error('form.password')
                            <span class="text-red-500 text-xs col-span-2">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Data Privacy Notice -->
                    <div class="flex items-center text-xs sm:text-xs text-darkgray mb-3 text-opacity-80">
                        <input wire:model="form.acceptPrivacy" id="acceptPrivacy" type="checkbox"
                            class="form-checkbox h-4 w-4 text-orange-600 mr-2" />
                        <label for="acceptPrivacy" class="flex-1">
                            By signing up, you confirm in <a href="#" class="text-orange-500 hover:underline">data
                                privacy</a> secures your
                            account by ensuring only authorized access to your data.
                        </label>
                    </div>

                    <!-- Sign Up Button -->
                    <div class="flex justify-center">
                        <button type="submit"
                            class="bg-warmyellow duration-150 font-bold border-[1px] border-darkgray text-darkgray w-1/2 py-2 rounded-full hover:bg-lightyellow hover:font-bold text-xs sm:text-sm">
                            Enter DataBar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @livewireScripts
</body>

</html>
