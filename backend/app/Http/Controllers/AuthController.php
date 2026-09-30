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

        return redirect(frontendAuthUrl());
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

        if ($user->role === 'admin') {
            return back()->withErrors([
                'username' => 'Akun admin dikelola di panel admin terpisah: spmb-admin.vercel.app',
            ])->withInput();
        }

        Auth::login($user, $request->boolean('remember'));
        $this->restorePendingDraft($request);

        if ($user->role === 'kasir') {
            return redirect()->route('spp.kasir');
        }

        if ($user->role === 'guru') {
            return redirect()->route('spp.rekap');
        }

        return redirect(frontendAuthUrl());
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect(frontendAuthUrl());
    }

    public function csrfToken()
    {
        return response()->json(['csrf_token' => csrf_token()]);
    }

    public function authStatus(Request $request)
    {
        if (!$request->user()) {
            return response()->json([
                'logged_in' => false,
                'role' => null,
                'name' => null,
                'avatar' => null,
                'has_pendaftaran' => false,
                'status' => null,
                'csrf_token' => csrf_token(),
            ]);
        }

        $pendaftaran = Pendaftaran::where('user_id', $request->user()->id)->first();

        // Role "pendaftar" dengan pendaftaran diterima dianggap siswa (mis. data lama yang tidak lewat updateStatus)
        $role = $request->user()->role;
        if ($role === 'pendaftar' && $pendaftaran?->status === 'diterima') {
            $role = 'siswa';
        }

        return response()->json([
            'logged_in' => true,
            'role' => $role,
            'name' => $request->user()->name,
            'email' => $request->user()->email,
            'avatar' => $request->user()->avatar,
            'nisn' => $pendaftaran?->nisn,
            'jurusan' => $pendaftaran?->jurusan_pilihan,
            'has_pendaftaran' => (bool) $pendaftaran,
            'status' => $pendaftaran?->status,
            'csrf_token' => csrf_token(),
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

        // Tanpa mailer, link harus tampil di halaman. Di production link dicatat ke log agar
        // tidak terekspos murni di halaman (bisa terlihat dari belakang layar / shared computer).
        if (app()->isLocal()) {
            return back()->with('success', 'Link reset kata sandi Anda: ' . $link . ' (buka di tab baru)');
        }

        logger('Password reset link untuk ' . $user->username . ': ' . $link);

        return back()->with('success', 'Link reset kata sandi telah dikirim. Cek log sistem atau hubungi admin untuk mendapatkan link-nya.');
    }

    public function showResetForm(string $token)
    {
        return view('auth.reset-password', compact('token'));
    }

    public function showProfile()
    {
        $user = Auth::user();
        $pendaftaran = Pendaftaran::where('user_id', $user->id)->first();

        // Aturan bisnis: siswa hanya bisa edit form bila status "baru" dan belum lewat 3 hari
        $canEdit = (bool) $pendaftaran
            && $pendaftaran->status === 'baru'
            && now()->lt($pendaftaran->created_at->copy()->addDays(3));

        return view('auth.profile', compact('user', 'pendaftaran', 'canEdit'));
    }

    public function updateAvatar(Request $request)
    {
        $request->validate([
            'avatar' => 'required|image|mimes:jpg,jpeg,png,webp|max:512',
        ], [
            'avatar.required' => 'Pilih foto terlebih dahulu.',
            'avatar.image' => 'File harus berupa gambar.',
            'avatar.mimes' => 'Format foto harus JPG, PNG, atau WebP.',
            'avatar.max' => 'Ukuran foto maksimal 512 KB.',
        ]);

        $file = $request->file('avatar');
        $avatar = $this->avatarToDataUri($file->get());

        if ($avatar === null) {
            return back()->with('error', 'Foto gagal diproses. Coba gunakan foto lain.');
        }

        $request->user()->forceFill(['avatar' => $avatar])->save();

        return redirect()->route('profil')->with('success', 'Foto profil berhasil diperbarui.');
    }

    public function destroyAvatar(Request $request)
    {
        $request->user()->forceFill(['avatar' => null])->save();

        return redirect()->route('profil')->with('success', 'Foto profil telah dihapus.');
    }

    /**
     * Ubah gambar menjadi data URI base64: resize maksimal 256px + kompres JPEG.
     * Return null bila file bukan gambar valid atau hasilnya tetap terlalu besar.
     * Fallback ke file asli (base64) hanya dipakai bila ekstensi GD tidak tersedia.
     */
    private function avatarToDataUri(string $bytes): ?string
    {
        $maxBase64Length = 350000;

        if (function_exists('imagecreatefromstring') && function_exists('imagejpeg')) {
            $image = @imagecreatefromstring($bytes);

            // File rusak / bukan gambar → jangan pernah disimpan
            if ($image === false) {
                return null;
            }

            $width = imagesx($image);
            $height = imagesy($image);
            $result = null;

            // Turunkan ukuran bertahap bila hasil kompresi masih terlalu besar
            foreach ([256, 192, 160, 128] as $maxSide) {
                $scale = min(1, $maxSide / max($width, $height));
                $newWidth = max(1, (int) round($width * $scale));
                $newHeight = max(1, (int) round($height * $scale));

                $canvas = imagecreatetruecolor($newWidth, $newHeight);

                // Latar putih supaya PNG transparan tidak jadi hitam setelah disimpan sebagai JPEG
                imagefill($canvas, 0, 0, imagecolorallocate($canvas, 255, 255, 255));
                imagecopyresampled($canvas, $image, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

                ob_start();
                imagejpeg($canvas, null, 82);
                $encoded = ob_get_clean();

                imagedestroy($canvas);

                $candidate = 'data:image/jpeg;base64,' . base64_encode($encoded);

                if (strlen($candidate) <= $maxBase64Length) {
                    $result = $candidate;
                    break;
                }
            }

            imagedestroy($image);

            return $result;
        }

        // Tanpa GD: simpan file asli apa adanya (masih divalidasi max 512 KB di atas)
        $mime = $this->detectMimeFromBytes($bytes);

        if ($mime === null) {
            return null;
        }

        $fallback = 'data:' . $mime . ';base64,' . base64_encode($bytes);

        return strlen($fallback) <= $maxBase64Length ? $fallback : null;
    }

    private function detectMimeFromBytes(string $bytes): ?string
    {
        $info = @getimagesizefromstring($bytes);

        return $info['mime'] ?? null;
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