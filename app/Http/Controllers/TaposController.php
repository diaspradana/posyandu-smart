<?php

namespace App\Http\Controllers;

use App\Models\Tapos;
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
                'ibuHamil'
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
            'ibuHamil'
        ]);

        return view('tapos.show', compact('tapo'));
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
}