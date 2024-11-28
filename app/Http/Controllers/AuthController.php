<?php

namespace App\Http\Controllers;

use App\Mail\SendTwoFAMail;
use App\Models\User;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use PragmaRX\Google2FALaravel\Support\Authenticator;

class AuthController extends Controller
{
    /**
     * Handle user registration.
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function register(Request $request): RedirectResponse
    {
        $request->validate(['email' => 'required|email']);
        $email = $request->email;

        // Check if user already exists
        $user = User::where('email', $email)->first();
        if ($user) {
            // Redirect to dashboard if already registered
            return redirect()->route('dashboard', ['username' => $user->username])
                ->with('message', 'You are already registered.');
        }

        // Validate email domain
        $validDomains = config('app.validUserEmailDomains');
        $emailDomain = substr(strrchr($email, '@'), 1);
        if (!in_array($emailDomain, $validDomains)) {
            return back()->withErrors(['email' => 'Invalid email domain.']);
        }

        // Register the user
        $user = User::registerUser($email);

        // Send 2FA Secret Email
        Mail::to($user->email)->send(new SendTwoFAMail($user->two_fa_secret));

        return back()->with('message', 'Check your email for 2FA setup instructions.');
    }

    /**
     * Handle user login.
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function login(Request $request): RedirectResponse
    {
        $request->validate(['email' => 'required|email', 'otp_input' => 'required']);

        // Find the user by email
        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return back()->withErrors(['email' => 'User not found.']);
        }

        // Verify 2FA code
        $google2fa = app(Authenticator::class);
        $isValid = $google2fa->verifyGoogle2FA($user->two_fa_secret, $request->otp_input);

        if (!$isValid) {
            return back()->withErrors(['otp_input' => 'Invalid 2FA code.']);
        }

        // Log the user in
        Auth::login($user);

        // Redirect to dashboard
        return redirect()->route('dashboard', ['username' => $user->username]);
    }

    /**
     * Handle user logout.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function logout(Request $request)
    {
        Auth::logout(); // Log the user out

        // Invalidate the session to avoid reuse
        $request->session()->invalidate();

        // Regenerate the session token
        $request->session()->regenerateToken();

        // Redirect to the home page or login page
        return redirect()->route('home')->with('message', 'Logged out successfully.');
    }
}
