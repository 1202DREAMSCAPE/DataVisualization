<?php
// views/livewire/pages/auth/register.blade.php
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Livewire\Volt\Component;

new class extends Component
{
    public string $name = '';
    public string $email = '';
    public string $username = '';
    public string $password = '';
    public string $password_confirmation = '';
    public bool $acceptPrivacy = false;


    /**
     * Handle an incoming registration request.
     */
    public function register(): void
{
    try {
        // Validate the form inputs
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'username' => ['required', 'string', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
            'acceptPrivacy' => ['accepted'], // Validate that the DPA checkbox is checked
        ]);

        // Hash the password before saving
        $validated['password'] = Hash::make($validated['password']);

        // Create the user and dispatch the registered event
        event(new Registered($user = User::create($validated)));

        // Log the user in
        Auth::login($user);

        // Redirect to the intended page only if everything is successful
        $this->redirect(route('project', absolute: false), navigate: true);
    } catch (\Illuminate\Validation\ValidationException $e) {
        // Catch validation errors and prevent redirection
        $this->addError('form_error', 'There are validation errors. Please correct them and try again.');
    } catch (\Exception $e) {
        // Catch unexpected errors
        $this->addError('form_error', 'An unexpected error occurred. Please try again.');
    }
}


    /**
     * Render the component view.
     */
     public function render(): mixed
    {
        return view('signup');
    }
};
