<?php

namespace App\Livewire\Auth;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Login extends Component
{
    public string $email = '';
    public string $password = '';
    public bool $remember = false;

    public function login()
    {
        $validated = $this->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (!Auth::attempt($validated, $this->remember)) {
            $this->addError('email', 'The provided credentials do not match our records.');
            return;
        }

        session()->regenerate();

        return $this->redirect(route('decks.index'), navigate: true);
    }

    public function render()
    {
        return view('livewire.auth.login')->layout('components.layouts.guest');
    }
}