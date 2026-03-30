<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class AuthController extends Controller
{
    public function showLoginForm(){
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
        $field = filter_var($request->username, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
        $user = User::where($field, $request->username)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return back()->withErrors([
                'login' => 'Username atau Password salah'
            ])->withInput();
        }

        Auth::login($user, $request->has('remember'));
        $request->session()->regenerate();
        return redirect()->route('kepegawaian');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }
}