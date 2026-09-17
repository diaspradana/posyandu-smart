<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PemeriksaanBalita;
use App\Models\PemeriksaanIbuHamil;
use App\Models\Tapos;

class ValidasiPemeriksaanController extends Controller
{
    /**
     * Tampilkan antrean validasi data pemeriksaan
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $puskesmasId = $user->puskesmas_id;
        $taposList = Tapos::where('puskesmas_id', $puskesmasId)->get();
        $taposIds = $taposList->pluck('id');

        $statusFilter = $request->get('status', 'pending');
        $tab = $request->get('tab', 'balita');

        // Query Balita Inspections
        $balitaQuery = PemeriksaanBalita::whereHas('balita', function ($q) use ($taposIds) {
            $q->whereIn('tapos_id', $taposIds);
        })->with(['balita.tapos']);

        if ($statusFilter !== 'semua') {
            $balitaQuery->where('status_validasi', $statusFilter);
        }

        $balitaList = $balitaQuery->orderBy('tanggal_pemeriksaan', 'desc')->paginate(10, ['*'], 'balita_page')->withQueryString();

        // Query Ibu Hamil Inspections
        $bumilQuery = PemeriksaanIbuHamil::whereHas('ibuHamil', function ($q) use ($taposIds) {
            $q->whereIn('tapos_id', $taposIds);
        })->with(['ibuHamil.tapos']);

        if ($statusFilter !== 'semua') {
            $bumilQuery->where('status_validasi', $statusFilter);
        }

        $ibuHamilList = $bumilQuery->orderBy('tanggal_pemeriksaan', 'desc')->paginate(10, ['*'], 'bumil_page')->withQueryString();

        // Counts
        $pendingBalitaCount = PemeriksaanBalita::whereHas('balita', fn($q) => $q->whereIn('tapos_id', $taposIds))->where('status_validasi', 'pending')->count();
        $pendingBumilCount = PemeriksaanIbuHamil::whereHas('ibuHamil', fn($q) => $q->whereIn('tapos_id', $taposIds))->where('status_validasi', 'pending')->count();
        $totalPending = $pendingBalitaCount + $pendingBumilCount;

        return view('admin.validasi.index', compact(
            'user',
            'taposList',
            'tab',
            'statusFilter',
            'balitaList',
            'ibuHamilList',
            'pendingBalitaCount',
            'pendingBumilCount',
            'totalPending'
        ));
    }

    /**
     * Validasi (Approve) Pemeriksaan Balita
     */
    public function validasiBalita(Request $request, PemeriksaanBalita $pemeriksaan)
    {
        $request->validate([
            'catatan_validasi' => 'nullable|string|max:500',
        ]);

        $pemeriksaan->update([
            'status_validasi' => 'validated',
            'catatan_validasi' => $request->catatan_validasi ?? 'Data telah divalidasi oleh Tenaga Kesehatan Puskesmas.',
        ]);

        return redirect()->back()->with('success', "Pemeriksaan Balita {$pemeriksaan->balita->nama} berhasil divalidasi sebagai data resmi.");
    }

    /**
     * Tolak (Reject) Pemeriksaan Balita
     */
    public function tolakBalita(Request $request, PemeriksaanBalita $pemeriksaan)
    {
        $request->validate([
            'catatan_validasi' => 'required|string|min:3|max:500',
        ], [
            'catatan_validasi.required' => 'Wajib menyertakan alasan penolakan data pemeriksaan.',
        ]);

        $pemeriksaan->update([
            'status_validasi' => 'rejected',
            'catatan_validasi' => $request->catatan_validasi,
        ]);

        return redirect()->back()->with('success', "Pemeriksaan Balita {$pemeriksaan->balita->nama} telah ditolak dengan catatan evaluasi.");
    }

    /**
     * Validasi (Approve) Pemeriksaan Ibu Hamil
     */
    public function validasiIbuHamil(Request $request, PemeriksaanIbuHamil $pemeriksaan)
    {
        $request->validate([
            'catatan_validasi' => 'nullable|string|max:500',
        ]);

        $pemeriksaan->update([
            'status_validasi' => 'validated',
            'catatan_validasi' => $request->catatan_validasi ?? 'Data telah divalidasi oleh Bidan/Tenaga Kesehatan Puskesmas.',
        ]);

        return redirect()->back()->with('success', "Pemeriksaan Ibu Hamil {$pemeriksaan->ibuHamil->nama} berhasil divalidasi.");
    }

    /**
     * Tolak (Reject) Pemeriksaan Ibu Hamil
     */
    public function tolakIbuHamil(Request $request, PemeriksaanIbuHamil $pemeriksaan)
    {
        $request->validate([
            'catatan_validasi' => 'required|string|min:3|max:500',
        ], [
            'catatan_validasi.required' => 'Wajib menyertakan alasan penolakan data pemeriksaan.',
        ]);

        $pemeriksaan->update([
            'status_validasi' => 'rejected',
            'catatan_validasi' => $request->catatan_validasi,
        ]);

        return redirect()->back()->with('success', "Pemeriksaan Ibu Hamil {$pemeriksaan->ibuHamil->nama} telah ditolak.");
    }
}
