<?php

namespace App\Http\Controllers;

use App\Models\IbuHamil;
use App\Models\Tapos;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class IbuHamilController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();

        $query = IbuHamil::with('tapos');

        if ($user->isKader()) {
            $query->where('tapos_id', $user->tapos_id);
        } else {
            $query->whereHas(
                'tapos',
                fn ($q) =>
                $q->where(
                    'puskesmas_id',
                    $user->puskesmas_id
                )
            );
        }

        $query->when(
            $request->search,
            function ($q, $search) {
                $q->where(function ($query) use ($search) {
                    $query->where(
                        'nama',
                        'like',
                        "%{$search}%"
                    )->orWhere(
                        'nik',
                        'like',
                        "%{$search}%"
                    );
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

        $ibuHamil = $query
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $tapos = $this->getTapos();

        return view(
            'ibu_hamil.index',
            compact('ibuHamil', 'tapos')
        );
    }

    public function create()
    {
        $tapos = $this->getTapos();

        return view(
            'ibu_hamil.create',
            compact('tapos')
        );
    }

    public function store(Request $request)
    {
        $validated = $this->validateData($request);

        IbuHamil::create($validated);

        return redirect()
            ->route('ibu-hamil.index')
            ->with(
                'success',
                'Data ibu hamil berhasil ditambahkan.'
            );
    }

    public function show(IbuHamil $ibuHamil)
    {
        Gate::authorize('view', $ibuHamil);

        $ibuHamil->load('tapos');

        return view(
            'ibu_hamil.show',
            compact('ibuHamil')
        );
    }

    public function edit(IbuHamil $ibuHamil)
    {
        Gate::authorize('update', $ibuHamil);

        $tapos = $this->getTapos();

        return view(
            'ibu_hamil.edit',
            compact('ibuHamil', 'tapos')
        );
    }

    public function update(
        Request $request,
        IbuHamil $ibuHamil
    ) {
        Gate::authorize('update', $ibuHamil);

        $validated = $this->validateData(
            $request,
            $ibuHamil
        );

        $ibuHamil->update($validated);

        return redirect()
            ->route('ibu-hamil.index')
            ->with(
                'success',
                'Data ibu hamil berhasil diperbarui.'
            );
    }

    public function destroy(IbuHamil $ibuHamil)
    {
        Gate::authorize('delete', $ibuHamil);

        $ibuHamil->delete();

        return redirect()
            ->route('ibu-hamil.index')
            ->with(
                'success',
                'Data ibu hamil berhasil dihapus.'
            );
    }

    private function validateData(
        Request $request,
        ?IbuHamil $ibuHamil = null
    ) {
        $user = auth()->user();

        return $request->validate([
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
                Rule::unique('ibu_hamil', 'nik')
                    ->ignore($ibuHamil?->id),
            ],

            'nama' => [
                'required',
                'string',
                'max:255'
            ],

            'tanggal_lahir' => [
                'required',
                'date',
                'before_or_equal:today'
            ],

            'alamat' => [
                'nullable',
                'string'
            ],

            'no_hp' => [
                'nullable',
                'string',
                'max:20'
            ],

            'hari_pertama_haid_terakhir' => [
                'nullable',
                'date',
                'before_or_equal:today'
            ],

            'kehamilan_ke' => [
                'required',
                'integer',
                'min:1',
                'max:20'
            ],

            'usia_kehamilan_minggu' => [
                'nullable',
                'integer',
                'min:1',
                'max:45'
            ],

            'status' => [
                'required',
                Rule::in([
                    'aktif',
                    'melahirkan',
                    'pindah',
                    'meninggal'
                ])
            ],
        ]);
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