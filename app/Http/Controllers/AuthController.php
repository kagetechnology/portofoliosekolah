<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function showRegister(): RedirectResponse
    {
        return redirect()->route('login')->with('status', 'Akun siswa dibuat oleh admin sekolah.');
    }

    public function register(Request $request): RedirectResponse
    {
        abort(404);
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'login' => ['required', 'string', 'max:150'],
            'password' => ['required', 'string'],
        ]);

        $login = trim($credentials['login']);
        $user = ctype_digit($login)
            ? User::where('nisn', $login)->where('role', 'siswa')->first()
            : User::where('email', $login)->whereIn('role', ['admin', 'guru'])->first();

        // Constant-time-ish: always run Hash::check even if user not found (prevents email enumeration via timing)
        $dummyHash = '$2y$12$'.str_repeat('a', 53);
        $hash = $user?->password ?? $dummyHash;
        if (! $user || ! Hash::check($credentials['password'], $hash)) {
            return back()->withErrors(['login' => 'NISN/email atau password salah.'])
                ->withInput(['login' => $login]);
        }

        if ($user->status !== 'active') {
            return back()->withErrors(['login' => 'Akun belum aktif. Hubungi admin sekolah.'])
                ->withInput(['login' => $login]);
        }

        Auth::login($user, $request->boolean('remember'));
        $request->session()->regenerate();

        if ($user->must_change_password) {
            return redirect()->route('account.edit')->with('status', 'Ganti password awal sebelum melanjutkan.');
        }

        return redirect()->intended($this->redirectPathFor($user));
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }

    private function redirectPathFor(User $user): string
    {
        return match ($user->role) {
            'admin' => route('admin.dashboard'),
            'guru' => route('guru.dashboard'),
            'siswa' => route('siswa.dashboard'),

            // alias route removed - keep dashboard
            default => route('home'),
        };
    }
}
