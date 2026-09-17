<?php

namespace App\Http\Controllers;

use App\Models\Balita;
use App\Models\Tapos;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class BalitaController extends Controller

{
    public function index(Request $request)
    {
        $user = auth()->user();

        $query = Balita::with('tapos');

        if ($user->isKader()) {
            $query->where('tapos_id', $user->tapos_id);
        } else {
            $query->whereHas('tapos', function ($q) use ($user) {
                $q->where('puskesmas_id', $user->puskesmas_id);
            });
        }

        $query->when(
            $request->search,
            function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('nama', 'like', "%{$search}%")
                        ->orWhere('nik', 'like', "%{$search}%")
                        ->orWhere('nama_ibu', 'like', "%{$search}%");
                });
            }
        );

        $query->when(
            $request->tapos_id && $user->isAdmin(),
            fn ($q) =>
                $q->where('tapos_id', $request->tapos_id)
        );

        $query->when(
            $request->status,
            fn ($q, $status) =>
                $q->where('status', $status)
        );

        $balita = $query
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $tapos = $this->getTapos();

        return view(
            'balita.index',
            compact('balita', 'tapos')
        );
    }


    public function create()
    {
        $tapos = $this->getTapos();

        return view(
            'balita.create',
            compact('tapos')
        );
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'tapos_id' => [
                'required',
                Rule::exists('tapos', 'id')
                    ->where(
                        fn ($q) =>
                        $q->where(
                            'puskesmas_id',
                            auth()->user()->puskesmas_id
                        )
                    ),
            ],

            'nik' => [
                'required',
                'digits:16',
                'unique:balita,nik',
            ],

            'nama' => [
                'required',
                'string',
                'max:255'
            ],

            'jenis_kelamin' => [
                'required',
                Rule::in(['L', 'P'])
            ],

            'tanggal_lahir' => [
                'required',
                'date',
                'before_or_equal:today'
            ],

            'nama_ibu' => [
                'nullable',
                'string',
                'max:255'
            ],

            'nama_ayah' => [
                'nullable',
                'string',
                'max:255'
            ],

            'no_hp_orang_tua' => [
                'nullable',
                'string',
                'max:20'
            ],

            'status' => [
                'required',
                Rule::in([
                    'aktif',
                    'pindah',
                    'meninggal'
                ])
            ],
        ]);

        Balita::create($validated);

        return redirect()
            ->route('balita.index')
            ->with(
                'success',
                'Data balita berhasil ditambahkan.'
            );
    }

    public function show(Balita $balita)
    {
        Gate::authorize('view', $balita);

        $balita->load('tapos');

        return view(
            'balita.show',
            compact('balita')
        );
    }

    public function edit(Balita $balita)
    {
        Gate::authorize('update', $balita);

        $tapos = $this->getTapos();

        return view(
            'balita.edit',
            compact('balita', 'tapos')
        );
    }

    public function update(
        Request $request,
        Balita $balita
    ) {
        Gate::authorize('update', $balita);

        $user = auth()->user();

        $validated = $request->validate([
            'tapos_id' => [
                'required',
                Rule::exists('tapos', 'id')
                    ->where(
                        fn ($q) =>
                        $user->isKader()
                            ? $q->where('id', $user->tapos_id)
                            : $q->where('puskesmas_id', $user->puskesmas_id)
                    ),
            ],

            'nik' => [
                'required',
                'digits:16',
                Rule::unique('balita', 'nik')
                    ->ignore($balita->id),
            ],

            'nama' => [
                'required',
                'string',
                'max:255'
            ],

            'jenis_kelamin' => [
                'required',
                Rule::in(['L', 'P'])
            ],

            'tanggal_lahir' => [
                'required',
                'date',
                'before_or_equal:today'
            ],

            'nama_ibu' => [
                'nullable',
                'string',
                'max:255'
            ],

            'nama_ayah' => [
                'nullable',
                'string',
                'max:255'
            ],

            'no_hp_orang_tua' => [
                'nullable',
                'string',
                'max:20'
            ],

            'status' => [
                'required',
                Rule::in([
                    'aktif',
                    'pindah',
                    'meninggal'
                ])
            ],
        ]);

        $balita->update($validated);

        return redirect()
            ->route('balita.index')
            ->with(
                'success',
                'Data balita berhasil diperbarui.'
            );
    }

    public function destroy(Balita $balita)
    {
        Gate::authorize('delete', $balita);

        $balita->delete();

        return redirect()
            ->route('balita.index')
            ->with(
                'success',
                'Data balita berhasil dihapus.'
            );
    }

    private function getTapos()
    {
        $user = auth()->user();

        if ($user->isKader()) {
            return Tapos::where('id', $user->tapos_id)->get();
        }

        return Tapos::where(
            'puskesmas_id',
            $user->puskesmas_id
        )
            ->where('status', 'aktif')
            ->orderBy('nama')
            ->get();
    }
}