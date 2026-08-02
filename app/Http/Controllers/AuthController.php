<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\Rule;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'min:3', 'max:100'],
            'email' => ['required', 'email', 'max:150', 'unique:users,email'],
            'password' => ['required', 'confirmed', 'min:8'],
            'school_class' => ['nullable', 'string', 'max:50'],
            'phone' => ['nullable', 'string', 'max:30'],
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => 'siswa',
            'status' => 'pending',
            'school_class' => $data['school_class'] ?? null,
            'phone' => $data['phone'] ?? null,
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('login')->with('status',
            'Registrasi berhasil. Tunggu admin sekolah mengaktifkan akun Anda sebelum login.');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $user = User::where('email', $credentials['email'])->first();

        // Constant-time-ish: always run Hash::check even if user not found (prevents email enumeration via timing)
        $dummyHash = '$2y$12$'.str_repeat('a', 53);
        $hash = $user?->password ?? $dummyHash;
        if (! $user || ! Hash::check($credentials['password'], $hash)) {
            return back()->withErrors(['email' => 'Email atau password salah.'])
                ->withInput(['email' => $credentials['email']]);
        }

        if ($user->status !== 'active') {
            return back()->withErrors(['email' => 'Akun belum aktif. Hubungi admin sekolah.'])
                ->withInput(['email' => $credentials['email']]);
        }

        Auth::login($user, $request->boolean('remember'));
        $request->session()->regenerate();

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
            'siswa' => route('siswa.dashboard'),

    // alias route removed - keep dashboard
            default => route('home'),
        };
    }
}
