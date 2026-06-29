<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\LdapAuthService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    protected LdapAuthService $ldapAuth;

    public function __construct(LdapAuthService $ldapAuth)
    {
        $this->ldapAuth = $ldapAuth;
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
                'password' => 'required|max:100'
            ],
            [],
            [
                'username' => 'Username atau Email',
                'password' => 'Password'
            ]
        );
        $isAuthenticated = $this->ldapAuth->authenticate($request->username, $request->password);

        if ($isAuthenticated) {
            // 3. Regenerate session untuk mencegah Session Fixation
            $request->session()->regenerate();

            // 4. Redirect ke halaman tujuan asli Anda
            return redirect()->route('kepegawaian');
        }

        // 5. Jika gagal (baik lokal maupun LDAP, atau pegawai tidak ditemukan)
        return back()->withErrors([
            'login' => 'Username atau Password salah, atau profil pegawai belum terdaftar di SIPENA.'
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