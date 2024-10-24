<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    // Your existing code...

    /**
     * @param Request $request
     * @return Application|\Illuminate\Foundation\Application|RedirectResponse|Redirector
     */
    public function logout(Request $request): \Illuminate\Foundation\Application|Redirector|RedirectResponse|Application
    {
        Auth::logout();

        // Invalidate the session
        $request->session()->invalidate();

        // Regenerate the session token to prevent CSRF attacks
        $request->session()->regenerateToken();

        // Redirect to a custom route, for example, the homepage
        return redirect('/login'); // Change this to your desired URL
    }
}

