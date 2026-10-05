<?php

namespace App\Http\Controllers;

use App\Models\Tapos;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class TaposController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();

        $query = Tapos::with('puskesmas')
            ->withCount([
                'balita',
                'ibuHamil',
                'kaders'
            ]);

        if ($user->isKader()) {
            $query->where('id', $user->tapos_id);
        } elseif ($user->isAdmin()) {
            $query->where('puskesmas_id', $user->puskesmas_id);
        }

        $query->when(
            $request->search,
            function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('nama', 'like', "%{$search}%")
                        ->orWhere('kode', 'like', "%{$search}%")
                        ->orWhere('kelurahan', 'like', "%{$search}%");
                });
            }
        );

        $query->when(
            $request->status,
            fn ($q, $status) => $q->where('status', $status)
        );

        $tapos = $query
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('tapos.index', compact('tapos'));
    }

    public function create()
    {
        Gate::authorize('create', Tapos::class);

        return view('tapos.create');
    }

    public function store(Request $request)
    {
        Gate::authorize('create', Tapos::class);

        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:255'],

            'kode' => [
                'required',
                'string',
                'max:50',
                'unique:tapos,kode',
            ],

            'alamat' => ['nullable', 'string'],

            'kelurahan' => [
                'nullable',
                'string',
                'max:100'
            ],

            'kecamatan' => [
                'nullable',
                'string',
                'max:100'
            ],

            'nama_ketua' => [
                'nullable',
                'string',
                'max:255'
            ],

            'no_hp' => [
                'nullable',
                'string',
                'max:20'
            ],

            'latitude' => [
                'nullable',
                'numeric',
                'between:-90,90'
            ],

            'longitude' => [
                'nullable',
                'numeric',
                'between:-180,180'
            ],

            'status' => [
                'required',
                Rule::in([
                    'aktif',
                    'tidak_aktif'
                ])
            ],
        ]);

        $validated['puskesmas_id'] = auth()->user()->puskesmas_id;

        Tapos::create($validated);

        return redirect()
            ->route('tapos.index')
            ->with('success', 'Data Tapos berhasil ditambahkan.');
    }

    public function show(Tapos $tapo)
    {
        Gate::authorize('view', $tapo);

        $tapo->loadCount([
            'balita',
            'ibuHamil',
            'kaders'
        ]);

        $kaders = $tapo->kaders()->latest()->get();

        return view('tapos.show', compact('tapo', 'kaders'));
    }

    public function edit(Tapos $tapo)
    {
        Gate::authorize('update', $tapo);

        return view('tapos.edit', compact('tapo'));
    }

    public function update(Request $request, Tapos $tapo)
    {
        Gate::authorize('update', $tapo);

        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:255'],

            'kode' => [
                'required',
                'string',
                'max:50',
                Rule::unique('tapos', 'kode')
                    ->ignore($tapo->id),
            ],

            'alamat' => ['nullable', 'string'],

            'kelurahan' => [
                'nullable',
                'string',
                'max:100'
            ],

            'kecamatan' => [
                'nullable',
                'string',
                'max:100'
            ],

            'nama_ketua' => [
                'nullable',
                'string',
                'max:255'
            ],

            'no_hp' => [
                'nullable',
                'string',
                'max:20'
            ],

            'latitude' => [
                'nullable',
                'numeric',
                'between:-90,90'
            ],

            'longitude' => [
                'nullable',
                'numeric',
                'between:-180,180'
            ],

            'status' => [
                'required',
                Rule::in([
                    'aktif',
                    'tidak_aktif'
                ])
            ],
        ]);

        $tapo->update($validated);

        return redirect()
            ->route('tapos.index')
            ->with('success', 'Data Tapos berhasil diperbarui.');
    }

    public function destroy(Tapos $tapo)
    {
        Gate::authorize('delete', $tapo);

        $tapo->delete();

        return redirect()
            ->route('tapos.index')
            ->with('success', 'Data Tapos berhasil dihapus.');
    }

    public function storeKader(Request $request, Tapos $tapo)
    {
        Gate::authorize('update', $tapo);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ], [
            'name.required' => 'Nama lengkap kader wajib diisi.',
            'email.required' => 'Email kader wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email ini sudah terdaftar dalam sistem.',
            'password.required' => 'Password kader wajib diisi.',
            'password.min' => 'Password minimal 6 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => $validated['password'],
            'role' => 'kader',
            'puskesmas_id' => $tapo->puskesmas_id,
            'tapos_id' => $tapo->id,
        ]);

        return redirect()
            ->route('tapos.show', $tapo)
            ->with('success', "Akun Petugas/Kader '{$validated['name']}' berhasil didaftarkan untuk {$tapo->nama}.");
    }

    public function updateKader(Request $request, Tapos $tapo, User $user)
    {
        Gate::authorize('update', $tapo);

        if ((int)$user->tapos_id !== (int)$tapo->id || $user->role !== 'kader') {
            abort(404, 'Data kader tidak ditemukan pada Tapos ini.');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'password' => ['nullable', 'string', 'min:6'],
        ], [
            'name.required' => 'Nama lengkap kader wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email ini sudah terdaftar oleh pengguna lain.',
            'password.min' => 'Password baru minimal 6 karakter.',
        ]);

        $data = [
            'name' => $validated['name'],
            'email' => $validated['email'],
        ];

        if (!empty($validated['password'])) {
            $data['password'] = $validated['password'];
        }

        $user->update($data);

        return redirect()
            ->route('tapos.show', $tapo)
            ->with('success', "Data Akun Kader '{$user->name}' berhasil diperbarui.");
    }

    public function destroyKader(Tapos $tapo, User $user)
    {
        Gate::authorize('update', $tapo);

        if ((int)$user->tapos_id !== (int)$tapo->id || $user->role !== 'kader') {
            abort(404, 'Data kader tidak ditemukan pada Tapos ini.');
        }

        $userName = $user->name;
        $user->delete();

        return redirect()
            ->route('tapos.show', $tapo)
            ->with('success', "Akun Kader '{$userName}' berhasil dihapus dari {$tapo->nama}.");
    }
}