@extends('layouts.app')

@section('title', 'Laporan Posyandu Wilayah')
@section('page-title', 'Laporan Posyandu Wilayah')

@section('content')
<div class="page-heading">
    <div>
        <span class="eyebrow">DOKUMENTASI & REKAPITULASI WILAYAH</span>
        <h1>📄 Laporan Eksekutif Kesehatan Wilayah</h1>
        <p>
            Rekapitulasi data posyandu, balita, ibu hamil, capaian deteksi dini AI, dan status operasional seluruh Posyandu Tapos.
        </p>
    </div>
    <div class="date-card" style="display: flex; gap: 8px; align-items: center;">
        <button onclick="window.print()" class="button primary" style="font-size: 13px; padding: 8px 16px;">
            🖨️ Cetak / Unduh PDF
        </button>
    </div>
</div>

{{-- PARAMETER FILTER CARD --}}
<div class="dashboard-card" style="padding: 20px; margin-bottom: 24px;">
    <h3 style="font-size: 14px; font-weight: 700; margin-bottom: 14px; color: #1e293b;">
        ⚙️ Parameter Laporan
    </h3>
    <form method="GET" action="{{ route('admin.laporan') }}" style="display: flex; gap: 14px; flex-wrap: wrap; align-items: flex-end;">
        <div style="min-width: 220px; flex: 1;">
            <label style="display: block; font-size: 12px; font-weight: 600; color: #475569; margin-bottom: 6px;">Periode Evaluasi</label>
            <select name="periode" class="select-button" style="width: 100%; height: 38px; font-size: 13px; border-radius: 8px; border: 1px solid #cbd5e1; padding: 0 12px;">
                <option value="September 2026" {{ ($periode ?? '') === 'September 2026' ? 'selected' : '' }}>September 2026</option>
                <option value="Agustus 2026" {{ ($periode ?? '') === 'Agustus 2026' ? 'selected' : '' }}>Agustus 2026</option>
                <option value="Juli 2026" {{ ($periode ?? '') === 'Juli 2026' ? 'selected' : '' }}>Juli 2026</option>
                <option value="Tahun 2026" {{ ($periode ?? '') === 'Tahun 2026' ? 'selected' : '' }}>Rekapitulasi Tahun 2026</option>
            </select>
        </div>

        <div style="min-width: 220px; flex: 1;">
            <label style="display: block; font-size: 12px; font-weight: 600; color: #475569; margin-bottom: 6px;">Kategori Sasaran</label>
            <select name="jenis" class="select-button" style="width: 100%; height: 38px; font-size: 13px; border-radius: 8px; border: 1px solid #cbd5e1; padding: 0 12px;">
                <option value="semua" {{ ($jenis ?? '') === 'semua' ? 'selected' : '' }}>Semua Sasaran (Balita & Ibu Hamil)</option>
                <option value="balita" {{ ($jenis ?? '') === 'balita' ? 'selected' : '' }}>Khusus Balita</option>
                <option value="ibu_hamil" {{ ($jenis ?? '') === 'ibu_hamil' ? 'selected' : '' }}>Khusus Ibu Hamil</option>
            </select>
        </div>

        <button type="submit" class="button primary" style="height: 38px; padding: 0 20px;">
            🔍 Tampilkan Rekap
        </button>
    </form>
</div>

{{-- REPORT CONTENT CONTAINER --}}
<div class="dashboard-card" style="padding: 28px; margin-bottom: 24px;">
    {{-- KOP LAPORAN --}}
    <div style="border-bottom: 2px solid #087f5b; padding-bottom: 16px; margin-bottom: 24px; text-align: center;">
        <span class="eyebrow">PEMERINTAH KOTA / KABUPATEN — DINAS KESEHATAN</span>
        <h2 style="font-size: 20px; font-weight: 800; color: #172b26; margin-top: 4px;">
            LAPORAN REKAPITULASI PELAYANAN POSYANDU SMART
        </h2>
        <p style="font-size: 13px; color: #64748b; margin-top: 4px;">
            Wilayah Kerja Puskesmas Sukamaju — Periode: <strong>{{ $periode ?? 'September 2026' }}</strong>
        </p>
    </div>

    {{-- STATS GRID --}}
    <div class="stats-grid" style="grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; margin-bottom: 28px;">
        <div class="stat-card" style="border-left: 4px solid var(--primary);">
            <span class="stat-label">Total Balita Sasaran</span>
            <strong class="stat-number" style="color: var(--primary);">{{ $totalBalita }}</strong>
            <span class="stat-description">Balita terdaftar di wilayah</span>
        </div>

        <div class="stat-card" style="border-left: 4px solid #db2777;">
            <span class="stat-label">Total Ibu Hamil Sasaran</span>
            <strong class="stat-number" style="color: #db2777;">{{ $totalIbuHamil }}</strong>
            <span class="stat-description">Ibu hamil terpantau</span>
        </div>

        <div class="stat-card" style="border-left: 4px solid #ef4444;">
            <span class="stat-label">Balita Teridentifikasi Risiko</span>
            <strong class="stat-number" style="color: #dc2626;">{{ $balitaStuntingCount }}</strong>
            <span class="stat-description">Screening awal AI Stunting</span>
        </div>

        <div class="stat-card" style="border-left: 4px solid #e11d48;">
            <span class="stat-label">Ibu Hamil Risiko Tinggi</span>
            <strong class="stat-number" style="color: #be123c;">{{ $ibuHamilRiskCount }}</strong>
            <span class="stat-description">Screening awal Maternal AI</span>
        </div>
    </div>

    {{-- REKAPITULASI TAPOS TABLE --}}
    <div class="card-header" style="margin-bottom: 16px;">
        <div>
            <span class="card-eyebrow">REKAP WILAYAH KERJA</span>
            <h3 style="font-size: 16px; font-weight: 700;">Daftar Posyandu Tapos Terdaftar</h3>
        </div>
        <span class="badge secondary">{{ count($taposList) }} Posyandu</span>
    </div>

    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Kode Posyandu</th>
                    <th>Nama Posyandu Tapos</th>
                    <th>Ketua Posyandu</th>
                    <th>Kelurahan / Kecamatan</th>
                    <th>Status Operasional</th>
                </tr>
            </thead>
            <tbody>
                @forelse($taposList as $idx => $t)
                    <tr>
                        <td style="text-align: center; width: 50px;">{{ $idx + 1 }}</td>
                        <td><strong>{{ $t->kode }}</strong></td>
                        <td>
                            <strong>{{ $t->nama }}</strong>
                            <div style="font-size: 11px; color: #64748b;">{{ $t->alamat ?? 'Wilayah Kerja Puskesmas' }}</div>
                        </td>
                        <td>{{ $t->nama_ketua ?? '-' }}</td>
                        <td>{{ $t->kelurahan }} / {{ $t->kecamatan }}</td>
                        <td>
                            <span class="status-pill success">🟢 Aktif Melayani</span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center" style="padding: 24px; color: #64748b;">
                            Belum ada data posyandu tapos terdaftar.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- REPORT FOOTER --}}
    <div style="margin-top: 28px; padding-top: 20px; border-top: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: flex-end; font-size: 12px; color: #475569;">
        <div>
            <p><strong>Dicetak oleh:</strong> {{ $user->name }} (Admin Puskesmas)</p>
            <p><strong>Waktu Cetak:</strong> {{ now()->format('d F Y, H:i') }} WIB</p>
        </div>
        <div style="text-align: center;">
            <p>Mengetahui,</p>
            <p style="margin-top: 40px; font-weight: 700; text-decoration: underline;">Kepala Puskesmas</p>
            <p style="font-size: 11px; color: #64748b;">NIP. 19820415 200801 1 008</p>
        </div>
    </div>
</div>

<div style="margin-top: 16px; padding: 12px; border-radius: 10px; background: #fff; border: 1px solid #e2e8f0; font-size: 12px; color: #64748b; display: flex; align-items: center; gap: 8px;">
    <span>ℹ️</span>
    <span><strong>Catatan:</strong> Rekapitulasi ini mengintegrasikan data hasil screening AI dan supervisi tenaga kesehatan untuk perencanaan program intervensi gizi Puskesmas.</span>
</div>

@endsection

