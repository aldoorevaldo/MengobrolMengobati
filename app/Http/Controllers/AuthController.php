<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AuthController extends Controller
{
    /**
     * Show login form
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Handle login
     */
    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            $user = auth()->user();

            // ADMIN
            if ($user->role === 'admin') {
                return redirect()->route('admin.dashboard');
            }

            // PSIKIATER
            if (Schema::hasTable('psikiaters')) {
                try {
                    if (DB::table('psikiaters')->where('user_id', $user->id)->exists()) {
                        return redirect()->route('psikiater.dashboard');
                    }
                } catch (\Throwable $e) {
                    \Log::warning('Auth login psikiater check failed: '.$e->getMessage());
                }
            }

            // PSIKOLOG
            if (Schema::hasTable('psikologs')) {
                try {
                    if (DB::table('psikologs')->where('user_id', $user->id)->exists()) {
                        return redirect()->route('psikolog.dashboard');
                    }
                } catch (\Throwable $e) {
                    \Log::warning('Auth login psikolog check failed: '.$e->getMessage());
                }
            }

            // USER BIASA → HOME
            return redirect()->route('home');
        }

        return back()
            ->withErrors(['email' => 'Email atau password salah.'])
            ->withInput();
    }

    public function showRegisterForm()
    {
        return view('auth.register');
    }

    /**
     * Handle register
     */
    public function register(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|confirmed|min:6',
        ]);

        $user = \App\Models\User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => bcrypt($request->password),
            'role'     => 'user',
        ]);

        Auth::login($user);

        return redirect()->route('home');
    }

    /**
     * Logout user
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }
}
