<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Livewire\Actions\Logout;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class LogoutController extends Controller
{
    /**
     * Log the user out of the application.
     */
    public function __invoke(Request $request, Logout $logout): RedirectResponse
    {
        // 2. Call the injected Logout action
        $logout();
        
        // 3. The controller is responsible for the redirect
        return redirect()->route('welcome');
    }
}