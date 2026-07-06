<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class AuthenticatedSessionController extends Controller
{
    public function create(): Response
    {
        return Inertia::render('Auth/Login');
    }

    public function store(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            throw ValidationException::withMessages([
                'email' => 'Nie znaleźliśmy konta z tym e-mailem i hasłem.',
            ]);
        }

        if ($request->user()->isBlocked()) {
            Auth::guard('web')->logout();

            throw ValidationException::withMessages([
                'email' => 'To konto zostało zablokowane.',
            ]);
        }

        $request->session()->regenerate();

        return redirect()->intended(route('panel.overview'))->with('success', 'Zalogowano.');
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('success', 'Wylogowano.');
    }
}
