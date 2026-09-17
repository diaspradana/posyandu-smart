<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Tapos;
use App\Models\JadwalPosyandu;
use Carbon\Carbon;

class JadwalAdminController extends Controller
{
    /**
     * Tampilkan daftar seluruh jadwal Posyandu wilayah Puskesmas.
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $puskesmasId = $user->puskesmas_id;

        // Ambil semua Tapos milik Puskesmas ini
        $taposList = Tapos::where('puskesmas_id', $puskesmasId)->orderBy('nama')->get();
        $taposIds = $taposList->pluck('id');

        // Query Utama Jadwal
        $query = JadwalPosyandu::whereIn('tapos_id', $taposIds)->with('tapos');

        // Filter Tapos
        if ($request->filled('tapos_id') && $request->tapos_id !== 'semua') {
            $query->where('tapos_id', $request->tapos_id);
        }

        // Filter Bulan
        if ($request->filled('bulan') && $request->bulan !== 'semua') {
            $query->whereMonth('tanggal', $request->bulan);
        }

        // Filter Status / Konfirmasi
        if ($request->filled('status') && $request->status !== 'semua') {
            $status = $request->status;
            if (in_array($status, ['menunggu_konfirmasi', 'siap', 'usulan_perubahan', 'ditolak'])) {
                $query->where('status_konfirmasi', $status);
            } elseif (in_array($status, ['mendatang', 'selesai', 'dibatalkan'])) {
                $query->where('status', $status);
            }
        }

        // Search text jika ada
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama_kegiatan', 'like', "%{$search}%")
                  ->orWhere('jenis_kegiatan', 'like', "%{$search}%")
                  ->orWhere('lokasi', 'like', "%{$search}%")
                  ->orWhereHas('tapos', function ($tq) use ($search) {
                      $tq->where('nama', 'like', "%{$search}%");
                  });
            });
        }

        // Urutkan berdasarkan tanggal terdekat / terbaru
        $jadwals = $query->orderBy('tanggal', 'desc')->paginate(10)->withQueryString();

        // Statistik
        $totalJadwal = JadwalPosyandu::whereIn('tapos_id', $taposIds)->count();
        $siapCount = JadwalPosyandu::whereIn('tapos_id', $taposIds)->where('status_konfirmasi', 'siap')->count();
        $menungguCount = JadwalPosyandu::whereIn('tapos_id', $taposIds)->where('status_konfirmasi', 'menunggu_konfirmasi')->count();
        $usulanCount = JadwalPosyandu::whereIn('tapos_id', $taposIds)->where('status_konfirmasi', 'usulan_perubahan')->count();

        // Daftar Jadwal yang memerlukan review usulan perubahan
        $usulanPerubahanList = JadwalPosyandu::whereIn('tapos_id', $taposIds)
            ->where('status_konfirmasi', 'usulan_perubahan')
            ->with('tapos')
            ->orderBy('updated_at', 'desc')
            ->get();

        return view('admin.jadwal.index', compact(
            'user',
            'taposList',
            'jadwals',
            'totalJadwal',
            'siapCount',
            'menungguCount',
            'usulanCount',
            'usulanPerubahanList'
        ));
    }

    /**
     * Simpan jadwal baru oleh Admin Puskesmas.
     */
    public function store(Request $request)
    {
        $request->validate([
            'tapos_id' => 'required|exists:tapos,id',
            'jenis_kegiatan' => 'required|string|max:150',
            'tanggal' => 'required|date',
            'waktu_mulai' => 'required',
            'waktu_selesai' => 'required',
            'lokasi' => 'required|string|max:255',
            'catatan' => 'nullable|string|max:1000',
        ], [
            'tapos_id.required' => 'Pilih Tapos tujuan kegiatan.',
            'jenis_kegiatan.required' => 'Pilih atau isi jenis kegiatan posyandu.',
            'tanggal.required' => 'Tentukan tanggal pelaksanaan kegiatan.',
            'waktu_mulai.required' => 'Isi waktu mulai kegiatan.',
            'waktu_selesai.required' => 'Isi waktu selesai kegiatan.',
            'lokasi.required' => 'Isi lokasi pelaksanaan kegiatan.',
        ]);

        $tapos = Tapos::findOrFail($request->tapos_id);

        JadwalPosyandu::create([
            'tapos_id' => $tapos->id,
            'nama_kegiatan' => $request->jenis_kegiatan,
            'jenis_kegiatan' => $request->jenis_kegiatan,
            'tanggal' => $request->tanggal,
            'waktu_mulai' => $request->waktu_mulai,
            'waktu_selesai' => $request->waktu_selesai,
            'lokasi' => $request->lokasi,
            'catatan' => $request->catatan,
            'status' => 'mendatang',
            'status_konfirmasi' => 'menunggu_konfirmasi',
        ]);

        return redirect()->route('admin.jadwal.index')
            ->with('success', "Jadwal {$request->jenis_kegiatan} untuk {$tapos->nama} berhasil diterbitkan dan dikirim ke kader.");
    }

    /**
     * Update jadwal kegiatan oleh Admin Puskesmas.
     */
    public function update(Request $request, JadwalPosyandu $jadwal)
    {
        $request->validate([
            'tapos_id' => 'required|exists:tapos,id',
            'jenis_kegiatan' => 'required|string|max:150',
            'tanggal' => 'required|date',
            'waktu_mulai' => 'required',
            'waktu_selesai' => 'required',
            'lokasi' => 'required|string|max:255',
            'catatan' => 'nullable|string|max:1000',
            'status' => 'required|in:mendatang,selesai,dibatalkan',
        ]);

        $tapos = Tapos::findOrFail($request->tapos_id);

        $jadwal->update([
            'tapos_id' => $tapos->id,
            'nama_kegiatan' => $request->jenis_kegiatan,
            'jenis_kegiatan' => $request->jenis_kegiatan,
            'tanggal' => $request->tanggal,
            'waktu_mulai' => $request->waktu_mulai,
            'waktu_selesai' => $request->waktu_selesai,
            'lokasi' => $request->lokasi,
            'catatan' => $request->catatan,
            'status' => $request->status,
        ]);

        return redirect()->route('admin.jadwal.index')
            ->with('success', "Jadwal kegiatan {$jadwal->nama_kegiatan} berhasil diperbarui.");
    }

    /**
     * Hapus jadwal kegiatan.
     */
    public function destroy(JadwalPosyandu $jadwal)
    {
        $nama = $jadwal->nama_kegiatan;
        $taposNama = $jadwal->tapos->nama ?? 'Tapos';
        $jadwal->delete();

        return redirect()->route('admin.jadwal.index')
            ->with('success', "Jadwal {$nama} ({$taposNama}) berhasil dihapus.");
    }

    /**
     * Admin Menyetujui Usulan Perubahan Jadwal dari Kader.
     */
    public function setujuiPerubahan(Request $request, JadwalPosyandu $jadwal)
    {
        // Terapkan usulan kader ke jadwal utama
        if ($jadwal->usulan_tanggal) {
            $jadwal->tanggal = $jadwal->usulan_tanggal;
        }
        if ($jadwal->usulan_waktu_mulai) {
            $jadwal->waktu_mulai = $jadwal->usulan_waktu_mulai;
        }
        if ($jadwal->usulan_waktu_selesai) {
            $jadwal->waktu_selesai = $jadwal->usulan_waktu_selesai;
        }
        if ($jadwal->usulan_lokasi) {
            $jadwal->lokasi = $jadwal->usulan_lokasi;
        }

        // Set status konfirmasi menjadi siap (karena disepakati)
        $jadwal->status_konfirmasi = 'siap';
        $jadwal->alasan_penolakan = null;
        $jadwal->save();

        $taposNama = $jadwal->tapos->nama ?? 'Kader Tapos';

        return redirect()->route('admin.jadwal.index')
            ->with('success', "Usulan perubahan jadwal dari {$taposNama} disetujui. Jadwal resmi telah diperbarui menjadi tanggal {$jadwal->tanggal->format('d/m/Y')}.");
    }

    /**
     * Admin Menolak Usulan Perubahan Jadwal dari Kader.
     */
    public function tolakPerubahan(Request $request, JadwalPosyandu $jadwal)
    {
        $request->validate([
            'alasan_penolakan' => 'nullable|string|max:500',
        ]);

        $jadwal->status_konfirmasi = 'ditolak';
        $jadwal->alasan_penolakan = $request->alasan_penolakan ?? 'Usulan perubahan tidak dapat disetujui karena jadwal puskesmas telah terisi.';
        $jadwal->save();

        $taposNama = $jadwal->tapos->nama ?? 'Kader Tapos';

        return redirect()->route('admin.jadwal.index')
            ->with('info', "Usulan perubahan jadwal dari {$taposNama} telah ditolak. Jadwal tetap mengikuti tanggal semula.");
    }
}
