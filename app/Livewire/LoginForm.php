<?php

namespace App\Http\Livewire;

use App\Livewire\Forms\UserLoginForm; // Updated import
use Illuminate\Support\Facades\Session;
use Livewire\Component;

class LoginForm extends Component
{
    public UserLoginForm $form;

    protected $rules = [
        'form.username' => 'required|string',
        'form.password' => 'required|string',
        'form.remember' => 'boolean',
    ];

    /**
     * Mount the component with initial data.
     */
    public function mount()
    {
        $this->form = new UserLoginForm();
    }

    /**
     * Handle the login action.
     */
    public function login()
    {
        $this->validate();

        if ($this->form->authenticate()) { // Authenticate the user
            Session::regenerate();
            return redirect()->intended(route('dashboard'));
        }

        $this->addError('form.username', 'The provided credentials do not match our records.');
    }

    /**
     * Render the component view.
     */
    public function render()
    {
        return view('livewire.login-form');
    }
}
