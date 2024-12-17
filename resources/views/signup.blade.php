<!DOCTYPE html>
<!-- resources/views/signup.blade.php -->
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
    >            
        <!-- Header with Alpine.js for responsive menu -->
        <livewire:navbar />

        <!-- Main Content -->
        <div class="flex items-center justify-center flex-grow p-6">
            <div class="bg-white rounded-[40px] shadow-lg max-w-md w-full py-6 px-4 xl:py-12 xl:p-24">
                <h1 class="text-xs sm:text-sm text-darkgray mb-1 text-left">Get Started</h1>
                <h2 class="text-2xl sm:text-3xl text-darkgray mb-1 font-serif font-bold text-left">
                    Sign Up for <span class="font-bold italic">Free.</span>
                </h2>
                <p class="text-left text-xs sm:text-sm text-darkgray mb-2">
                    Already have an account? 
                    <a href="{{ route('login') }}" class="text-black hover:text-green-600 font-bold hover:underline italic">Log in</a>
                </p>

                <form action="{{ route('signup.store') }}" method="POST">
                    @csrf
                    <!-- Name -->
                    <div class="mb-2">
                        <input name="name" id="name" type="text" placeholder="Name"
                            data-storage-key="signup_name"
                            class="w-full border-[1px] border-darkgray rounded-lg px-3 py-2 text-xs sm:text-sm focus:outline-none focus:ring focus:ring-orange-300" required />
                        @error('name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <!-- Email -->
                    <div class="mb-2">
                        <input name="email" id="email" type="email" placeholder="Email Address"
                            data-storage-key="signup_email"
                            class="w-full border-[1px] border-darkgray rounded-lg px-3 py-2 text-xs sm:text-sm focus:outline-none focus:ring focus:ring-orange-300" required />
                        @error('email') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <!-- Username -->
                    <div class="mb-2">
                        <input name="username" id="username" type="text" placeholder="Username"
                            data-storage-key="signup_username"
                            class="w-full border-[1px] border-darkgray rounded-lg px-3 py-2 text-xs sm:text-sm focus:outline-none focus:ring focus:ring-orange-300" required />
                        @error('username') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <!-- Password and Confirm Password -->
                    <div class="grid grid-cols-2 gap-3 mb-3">
                        <div>
                            <input name="password" id="password" type="password" placeholder="Password"
                                class="w-full border-[1px] border-darkgray rounded-lg px-3 py-2 text-xs sm:text-sm focus:outline-none focus:ring focus:ring-orange-300" required />
                        </div>

                        <div>
                            <input name="password_confirmation" id="password_confirmation" type="password" placeholder="Confirm Password"
                                class="w-full border-[1px] border-darkgray rounded-lg px-3 py-2 text-xs sm:text-sm focus:outline-none focus:ring focus:ring-orange-300" required />
                        </div>
                    </div>
                    @error('password') <span class="text-red-500 text-center text-xs">{{ $message }}</span> @enderror


                    <!-- Password Requirements -->
                    <p class="text-xs text-center text-orange-500 mb-3">Note: Password must be at least 8 characters long.</p>

                    <!-- Data Privacy Agreement -->
                    <div class="flex items-center mb-3">
                        <input id="acceptPrivacy" type="checkbox" class="form-checkbox disabled h-4 w-4 text-orange-600 mr-2" disabled />
                        <label for="acceptPrivacy" class="text-sm text-gray-700">
                            By signing up, you confirm that <a href="#" class="text-orange-500 hover:underline" onclick="showModal()">data privacy</a> secures your account by ensuring only authorized access to your data.
                        </label>
                    </div>
                    @error('acceptPrivacy') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror

                    <!-- Sign Up Button -->
                    <div class="flex justify-center">
                        <button type="submit"
                            class="bg-warmyellow font-bold border-[1px] border-darkgray text-darkgray w-3/4 sm:w-1/2 py-2 rounded-full hover:bg-lightyellow hover:text-white hover:font-bold text-xs sm:text-sm">
                            Sign Up
                        </button>
                    </div>
                </form>

                <!-- Data Privacy Modal -->
                <div id="dataPrivacyModal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 hidden z-50">
                    <div class="bg-white rounded-lg w-4/5 md:w-1/3 p-6">
                        <h2 class="text-xl md:lg text-center font-bold mb-2 font-dmSerif">Data Privacy Agreement</h2>
                        <div class="pb-4 pr-4 pl-4 pt-2 overflow-y-auto max-h-64 text-sm bg-lightgray text-gray-700 mb-6 rounded-lg">
                            <p>
                                We are committed to protecting your personal data and ensuring its secure use within VizOra. By signing up, you agree to the following:
                            </p>
                            <ul class="list-disc pl-4 mt-4">
                                <li><strong>Data Collection:</strong> We collect only the information necessary to personalize and enhance your data visualization experience, such as your name, email, and usage statistics. Your data will never be sold or shared with third parties without your explicit consent.</li>
                                <li class="mt-2"><strong>Data Usage:</strong> Your data is used solely for generating insights, improving application functionality, and offering tailored visualization features. Aggregated and anonymized data may be used to improve our services without compromising your privacy.</li>
                                <li class="mt-2"><strong>Data Security:</strong> Your data is encrypted and stored securely in compliance with global data protection standards. We implement safeguards to protect against unauthorized access, disclosure, or misuse of your data.</li>
                            </ul>
                        </div>
                        <div class="flex justify-end">
                            <button onclick="hideModal()" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 mr-2">
                                Close
                            </button>
                            <button onclick="agreeToPrivacy()" class="bg-green-700 text-white px-4 py-2 rounded-lg hover:bg-green-500">
                                Agree
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @livewireScripts

    <!-- Modal Script -->
    <script>
        function showModal() {
            document.getElementById('dataPrivacyModal').classList.remove('hidden');
        }

        function hideModal() {
            document.getElementById('dataPrivacyModal').classList.add('hidden');
        }

        function agreeToPrivacy() {
            hideModal();
            const checkbox = document.getElementById('acceptPrivacy');
            checkbox.disabled = true;
            checkbox.checked = true;
            // Disable further manual interaction
            checkbox.setAttribute('readonly', 'true');
        }
    </script>

    <!-- Form Persistence Script -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const form = document.querySelector('form');

            // Function to save input values to localStorage
            const saveInput = (event) => {
                const input = event.target;
                const storageKey = input.getAttribute('data-storage-key');
                if (storageKey) {
                    localStorage.setItem(storageKey, input.value);
                }
            };

            // Function to load input values from localStorage
            const loadInput = (input) => {
                const storageKey = input.getAttribute('data-storage-key');
                if (storageKey) {
                    const savedValue = localStorage.getItem(storageKey);
                    if (savedValue) {
                        input.value = savedValue;
                    }
                }
            };

            // Select all inputs with data-storage-key
            const inputs = form.querySelectorAll('input[data-storage-key]');

            // Load saved data for each input
            inputs.forEach(input => loadInput(input));

            // Add event listeners to save data on input
            inputs.forEach(input => {
                input.addEventListener('input', saveInput);
            });

            // Clear localStorage on form submit
            form.addEventListener('submit', () => {
                inputs.forEach(input => {
                    const storageKey = input.getAttribute('data-storage-key');
                    if (storageKey) {
                        localStorage.removeItem(storageKey);
                    }
                });
            });
        });
    </script>
</body>

</html>
