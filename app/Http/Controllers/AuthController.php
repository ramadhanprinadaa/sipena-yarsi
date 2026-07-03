<?php

namespace App\Http\Controllers;

use App\Services\LdapAuthService;
use App\Services\LocalAuthService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    protected LdapAuthService $ldapAuth;
    protected LocalAuthService $localAuth;

    public function __construct(LdapAuthService $ldapAuth, LocalAuthService $localAuth)
    {
        $this->ldapAuth = $ldapAuth;
        $this->localAuth = $localAuth;
    }

    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate(
            [
                'username' => 'required|string|max:100',
                'password' => 'required|max:100',
            ],
            [],
            [
                'username' => 'Username atau Email',
                'password' => 'Password',
            ]
        );

        $identifier = $request->username;
        $password = $request->password;

        // Jika identifier terdaftar sebagai user lokal (auth_type = 'local'),
        // autentikasi HANYA lewat jalur lokal.
        if ($this->localAuth->isLocalUser($identifier)) {
            $user = $this->localAuth->authenticate($identifier, $password);
        } else {
            // Bukan user lokal yang dikenal -> coba jalur LDAP (termasuk
            // kemungkinan user baru yang belum pernah login sebelumnya).
            $user = $this->ldapAuth->authenticate($identifier, $password);
        }

        if ($user) {
            Auth::login($user, $request->boolean('remember'));

            $request->session()->regenerate();

            return redirect()->intended(route('kepegawaian'));
        }

        return back()->withErrors([
            'login' => 'Username atau Password salah.',
        ])->withInput($request->except('password'));
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }
}