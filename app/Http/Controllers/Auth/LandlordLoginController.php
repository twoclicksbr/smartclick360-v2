<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LandlordLoginController extends Controller
{
    /**
     * Exibe o formulário de login do admin (landlord).
     * Autentica contra o banco sc360_main.
     */
    public function showForm()
    {
        return view('auth.landlord-login');
    }

    /**
     * Processa a autenticação do admin.
     * Usa o guard 'web' para autenticar contra sc360_main.users.
     */
    public function authenticate(Request $request)
    {
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ]);

        $remember = $request->boolean('remember');

        if (Auth::guard('web')->attempt($credentials, $remember)) {
            $request->session()->regenerate();

            return redirect()->intended(route('landlord.dashboard'));
        }

        return back()->withErrors([
            'email' => 'E-mail ou senha incorretos.',
        ])->onlyInput('email');
    }

    /**
     * Faz logout do admin.
     */
    public function logout(Request $request)
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('landlord.login');
    }
}
