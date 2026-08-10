<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Pendaftaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:50|unique:users,username|regex:/^[a-zA-Z0-9_.]+$/',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'username' => $validated['username'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'pendaftar',
        ]);

        Auth::login($user);
        $this->restorePendingDraft($request);

        return redirect()->route('pendaftaran.create')->with('success', 'Register berhasil. Silakan lengkapi data pendaftaran Anda.');
    }

    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $user = User::where('username', $credentials['username'])
            ->orWhere('email', $credentials['username'])
            ->first();

        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            return back()->withErrors(['username' => 'Username atau password salah.'])->withInput();
        }

        Auth::login($user, $request->boolean('remember'));
        $this->restorePendingDraft($request);

        if ($user->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }

        if ($user->role === 'siswa') {
            return redirect('/');
        }

        return redirect()->route('pendaftaran.create');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }

    public function authStatus(Request $request)
    {
        if (!$request->user()) {
            return response()->json([
                'logged_in' => false,
                'role' => null,
                'name' => null,
                'has_pendaftaran' => false,
                'status' => null,
            ]);
        }

        $pendaftaran = Pendaftaran::where('user_id', $request->user()->id)->first();

        return response()->json([
            'logged_in' => true,
            'role' => $request->user()->role,
            'name' => $request->user()->name,
            'has_pendaftaran' => (bool) $pendaftaran,
            'status' => $pendaftaran?->status,
        ]);
    }

    private function restorePendingDraft(Request $request): void
    {
        $key = $request->cookie('pending_draft');

        if (!$key) {
            return;
        }

        $row = DB::table('pendaftaran_drafts')->where('key', $key)->first();

        if ($row) {
            $request->session()->put('pending_pendaftaran', json_decode($row->payload, true));
            DB::table('pendaftaran_drafts')->where('key', $key)->delete();
        }

        Cookie::queue(Cookie::forget('pending_draft'));
    }

    public function showForgotForm()
    {
        return view('auth.forgot-password');
    }

    public function sendResetLink(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
        ]);

        $user = User::where('username', $request->username)
            ->orWhere('email', $request->username)
            ->first();

        if (!$user) {
            return back()->withErrors(['username' => 'Akun tidak ditemukan. Periksa kembali username/email Anda.'])->withInput();
        }

        if ($user->role === 'admin') {
            return back()->withErrors(['username' => 'Reset kata sandi admin tidak tersedia. Hubungi pengelola sistem.'])->withInput();
        }

        $token = Password::broker()->createToken($user);
        $link = route('password.reset', $token);

        // ponytail: tanpa mailer, link tampil langsung di halaman (cukup utk demo/lokal)
        return back()->with('success', 'Link reset kata sandi Anda: ' . $link . ' (buka di tab baru)');
    }

    public function showResetForm(string $token)
    {
        return view('auth.reset-password', compact('token'));
    }

    public function showProfile()
    {
        $user = Auth::user();
        $pendaftaran = \App\Models\Pendaftaran::where('user_id', $user->id)->first();

        return view('auth.profile', compact('user', 'pendaftaran'));
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'token' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password) {
                $user->password = Hash::make($password);
                $user->save();
            }
        );

        return $status === Password::PASSWORD_RESET
            ? redirect()->route('login')->with('success', 'Kata sandi berhasil diubah. Silakan masuk dengan kata sandi baru.')
            : back()->withErrors(['email' => 'Token tidak valid atau email tidak cocok.']);
    }
}