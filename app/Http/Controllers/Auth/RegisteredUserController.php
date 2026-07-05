<?php

namespace App\Http\Controllers\Auth;

use App\Enums\AccountType;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Inertia\Inertia;
use Inertia\Response;

class RegisteredUserController extends Controller
{
    public function create(): Response
    {
        return Inertia::render('Auth/Register');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')],
            'password' => ['required', 'confirmed', Password::defaults()],
            'account_type' => ['required', Rule::enum(AccountType::class)],
            // Required for company accounts (see business rules). NIP is required
            // but not verified against a registry (deferred — see docs §6).
            'company_name' => ['nullable', 'required_if:account_type,company', 'string', 'max:160'],
            'tax_id' => ['nullable', 'required_if:account_type,company', 'string', 'max:20'],
            'city' => ['nullable', 'string', 'max:120'],
            'address_line' => ['nullable', 'string', 'max:160'],
        ]);

        $accountType = AccountType::from($data['account_type']);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'account_type' => $accountType,
        ]);

        if ($accountType === AccountType::Company) {
            $user->companyProfile()->create([
                'company_name' => $data['company_name'],
                'tax_id' => $data['tax_id'] ?? null,
                'city' => $data['city'] ?? null,
                'address_line' => $data['address_line'] ?? null,
            ]);
        }

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('panel.overview');
    }
}
