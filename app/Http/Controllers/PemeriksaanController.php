<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PemeriksaanBalita;
use App\Models\PemeriksaanIbuHamil;
use App\Models\Tapos;
use App\Models\Balita;
use App\Models\IbuHamil;

class PemeriksaanController extends Controller
{
    public function balita(Request $request)
    {
        $user = $request->user();
        $puskesmasId = $user->puskesmas_id;
        $taposList = Tapos::where('puskesmas_id', $puskesmasId)->get();
        $taposIds = $taposList->pluck('id');

        $query = PemeriksaanBalita::with('balita.tapos')
            ->whereHas('balita', function ($q) use ($taposIds) {
                $q->whereIn('tapos_id', $taposIds);
            });

        if ($request->filled('tapos_id')) {
            $query->whereHas('balita', function ($q) use ($request) {
                $q->where('tapos_id', $request->tapos_id);
            });
        }

        if ($request->filled('status_stunting')) {
            $query->where('status_stunting', $request->status_stunting);
        }

        if ($request->filled('status_imunisasi')) {
            $query->where('status_imunisasi', $request->status_imunisasi);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('balita', function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('nik', 'like', "%{$search}%");
            });
        }

        $pemeriksaan = $query->orderBy('tanggal_pemeriksaan', 'desc')->paginate(15)->withQueryString();

        // Summary counts
        $totalPemeriksaan = PemeriksaanBalita::whereHas('balita', function ($q) use ($taposIds) {
            $q->whereIn('tapos_id', $taposIds);
        })->count();

        $risikoCount = PemeriksaanBalita::whereHas('balita', function ($q) use ($taposIds) {
            $q->whereIn('tapos_id', $taposIds);
        })->where('status_stunting', 'risiko_stunting')->count();

        $pemantauanCount = PemeriksaanBalita::whereHas('balita', function ($q) use ($taposIds) {
            $q->whereIn('tapos_id', $taposIds);
        })->where('status_stunting', 'pemantauan')->count();

        $normalCount = PemeriksaanBalita::whereHas('balita', function ($q) use ($taposIds) {
            $q->whereIn('tapos_id', $taposIds);
        })->where('status_stunting', 'normal')->count();

        return view('pemeriksaan.balita', compact(
            'pemeriksaan',
            'taposList',
            'totalPemeriksaan',
            'risikoCount',
            'pemantauanCount',
            'normalCount'
        ));
    }

    public function ibuHamil(Request $request)
    {
        $user = $request->user();
        $puskesmasId = $user->puskesmas_id;
        $taposList = Tapos::where('puskesmas_id', $puskesmasId)->get();
        $taposIds = $taposList->pluck('id');

        $query = PemeriksaanIbuHamil::with('ibuHamil.tapos')
            ->whereHas('ibuHamil', function ($q) use ($taposIds) {
                $q->whereIn('tapos_id', $taposIds);
            });

        if ($request->filled('tapos_id')) {
            $query->whereHas('ibuHamil', function ($q) use ($request) {
                $q->where('tapos_id', $request->tapos_id);
            });
        }

        if ($request->filled('status_pemeriksaan')) {
            $query->where('status_pemeriksaan', $request->status_pemeriksaan);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('ibuHamil', function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('nik', 'like', "%{$search}%");
            });
        }

        $pemeriksaan = $query->orderBy('tanggal_pemeriksaan', 'desc')->paginate(15)->withQueryString();

        $totalPemeriksaan = PemeriksaanIbuHamil::whereHas('ibuHamil', function ($q) use ($taposIds) {
            $q->whereIn('tapos_id', $taposIds);
        })->count();

        $diperiksaCount = PemeriksaanIbuHamil::whereHas('ibuHamil', function ($q) use ($taposIds) {
            $q->whereIn('tapos_id', $taposIds);
        })->where('status_pemeriksaan', 'diperiksa')->count();

        $belumCount = PemeriksaanIbuHamil::whereHas('ibuHamil', function ($q) use ($taposIds) {
            $q->whereIn('tapos_id', $taposIds);
        })->where('status_pemeriksaan', 'belum_diperiksa')->count();

        return view('pemeriksaan.ibu_hamil', compact(
            'pemeriksaan',
            'taposList',
            'totalPemeriksaan',
            'diperiksaCount',
            'belumCount'
        ));
    }
}
