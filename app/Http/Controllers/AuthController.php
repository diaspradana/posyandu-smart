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

        // If custom puskesmas name is provided, create a new Puskesmas record
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
                return redirect()->route('admin.dashboard');
            }

            if ($user->role === 'kader') {
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

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}