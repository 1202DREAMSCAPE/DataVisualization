<?php

namespace App\Livewire\Forms;

use Illuminate\Support\Facades\Auth;

class UserLoginForm
{
    public string $username = '';
    public string $password = '';
    public bool $remember = false;

    /**
     * Authenticate the user.
     *
     * @return bool
     */
    public function authenticate(): bool
    {
        return Auth::attempt(
            ['username' => $this->username, 'password' => $this->password],
            $this->remember
        );
    }
}
