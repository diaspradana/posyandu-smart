<?php

namespace App\Http\Controllers;

use App\Models\Puskesmas;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function showRegisterAdmin()
    {
        $puskesmasList = Puskesmas::orderBy('nama')->get();
        return view('auth.register', compact('puskesmasList'));
    }

    public function registerAdmin(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'puskesmas_id' => ['nullable', 'exists:puskesmas,id'],
            'puskesmas_nama' => ['nullable', 'string', 'max:255'],
        ], [
            'name.required' => 'Nama lengkap wajib diisi.',
            'email.required' => 'Alamat email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email ini sudah terdaftar dalam sistem.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak sesuai.',
        ]);

        $puskesmasId = $validated['puskesmas_id'] ?? null;

        if (!empty($validated['puskesmas_nama'])) {
            $puskesmas = Puskesmas::create([
                'nama' => $validated['puskesmas_nama'],
                'alamat' => 'Alamat belum diatur',
                'kecamatan' => '-',
                'kabupaten' => '-',
            ]);
            $puskesmasId = $puskesmas->id;
        } elseif (!$puskesmasId) {
            $firstPuskesmas = Puskesmas::first();
            if ($firstPuskesmas) {
                $puskesmasId = $firstPuskesmas->id;
            } else {
                $newPuskesmas = Puskesmas::create([
                    'nama' => 'Puskesmas Induk',
                    'alamat' => 'Alamat belum diatur',
                    'kecamatan' => '-',
                    'kabupaten' => '-',
                ]);
                $puskesmasId = $newPuskesmas->id;
            }
        }

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => $validated['password'],
            'role' => 'admin',
            'puskesmas_id' => $puskesmasId,
        ]);

        Auth::login($user);
        $request->session()->regenerate();
        session(['admin_user_id' => $user->id, 'active_role' => 'admin']);

        return redirect()
            ->route('admin.dashboard')
            ->with('success', 'Registrasi Akun Admin Puskesmas berhasil! Selamat datang di Posyandu Smart.');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => [
                'required',
                'email',
            ],
            'password' => [
                'required',
            ],
        ]);

        $remember = $request->boolean('remember');

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();

            $user = Auth::user();

            if ($user->role === 'admin') {
                session(['admin_user_id' => $user->id, 'active_role' => 'admin']);
                return redirect()->route('admin.dashboard');
            }

            if ($user->role === 'kader') {
                session(['kader_user_id' => $user->id, 'active_role' => 'kader']);
                return redirect()->route('kader.dashboard');
            }

            Auth::logout();

            return back()->withErrors([
                'email' => 'Role pengguna tidak valid.',
            ]);
        }

        return back()
            ->withErrors([
                'email' => 'Email atau password salah.',
            ])
            ->withInput($request->only('email'));
    }

    /**
     * Quick Switch Role untuk pengujian / demonstrasi side-by-side
     */
    public function switchRole(Request $request, string $role)
    {
        if ($role === 'kader') {
            $kaderId = session('kader_user_id');
            $kader = $kaderId ? User::find($kaderId) : User::where('role', 'kader')->first();

            if ($kader) {
                Auth::setUser($kader);
                session(['kader_user_id' => $kader->id, 'active_role' => 'kader']);
                return redirect()->route('kader.dashboard')->with('success', "Beralih ke akun Kader: {$kader->name}");
            }
        } elseif ($role === 'admin') {
            $adminId = session('admin_user_id');
            $admin = $adminId ? User::find($adminId) : User::where('role', 'admin')->first();

            if ($admin) {
                Auth::setUser($admin);
                session(['admin_user_id' => $admin->id, 'active_role' => 'admin']);
                return redirect()->route('admin.dashboard')->with('success', "Beralih ke akun Admin: {$admin->name}");
            }
        }

        return redirect()->route('dashboard');
    }

    public function logout(Request $request)
    {
        $user = Auth::user();

        if ($user && $user->role === 'admin') {
            session()->forget('admin_user_id');
        } elseif ($user && $user->role === 'kader') {
            session()->forget('kader_user_id');
        }

        if (!session('admin_user_id') && !session('kader_user_id')) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        } else {
            if (session('admin_user_id')) {
                $admin = User::find(session('admin_user_id'));
                if ($admin) Auth::setUser($admin);
            } elseif (session('kader_user_id')) {
                $kader = User::find(session('kader_user_id'));
                if ($kader) Auth::setUser($kader);
            }
        }

        return redirect()->route('login');
    }
}