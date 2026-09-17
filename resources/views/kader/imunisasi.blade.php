@extends('layouts.app')

@section('title', 'Rekap Imunisasi - Kader')
@section('page-title', 'Imunisasi Balita')

@section('content')

{{-- PAGE HEADER --}}
<div class="page-heading">
    <div>
        <span class="eyebrow">CAKUPAN VAKSINASI BALITA</span>
        <h1>💉 Status Imunisasi Balita — {{ $tapos->nama }}</h1>
        <p>Pemantauan status kelengkapan vaksinasi dasar dan imunisasi lanjutan balita.</p>
    </div>

    <div style="display: flex; gap: 8px;">
        <a href="{{ route('kader.kegiatan.pemeriksaan', ['tab' => 'balita']) }}" class="btn-primary">
            <span>🩺</span>
            <span>Catat Imunisasi Baru</span>
        </a>
    </div>
</div>

{{-- 1. SUMMARY CARDS --}}
<div class="stats-grid" style="grid-template-columns: repeat(3, 1fr); margin-bottom: 24px;">
    <div class="stat-card" style="border-left: 4px solid #dc2626;">
        <span class="stat-label">🔴 Tertunda</span>
        <strong class="stat-number" style="color: #dc2626;">{{ $tertundaCount }}</strong>
        <span class="stat-description">Perlu imunisasi kejar</span>
    </div>

    <div class="stat-card" style="border-left: 4px solid #eab308;">
        <span class="stat-label">🟡 Mendatang</span>
        <strong class="stat-number" style="color: #ca8a04;">{{ $mendatangCount }}</strong>
        <span class="stat-description">Jadwal bulan ini/depan</span>
    </div>

    <div class="stat-card" style="border-left: 4px solid #16a34a;">
        <span class="stat-label">🟢 Lengkap Sesuai Usia</span>
        <strong class="stat-number" style="color: #16a34a;">{{ $lengkapCount }}</strong>
        <span class="stat-description">Vaksinasi telah terpenuhi</span>
    </div>
</div>

{{-- 2. TABLE IMUNISASI --}}
<div class="dashboard-card" style="padding: 0; overflow: hidden; margin-bottom: 24px;">
    <div style="padding: 16px 20px; border-bottom: 1px solid #f1f5f9;">
        <h2 style="font-size: 15px; font-weight: 700; color: #1e293b;">
            Daftar Imunisasi Balita {{ $tapos->nama }}
        </h2>
    </div>

    <div class="table-responsive">
        <table class="custom-table">
            <thead>
                <tr>
                    <th>Nama Balita</th>
                    <th>Jenis Imunisasi</th>
                    <th>Jadwal / Tanggal</th>
                    <th style="text-align: center;">Status</th>
                    <th style="text-align: right;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($imunisasiItems as $row)
                    <tr>
                        <td>
                            <strong>{{ $row['balita']->nama }}</strong>
                            <div style="font-size: 11px; color: #64748b;">NIK: {{ $row['balita']->nik }} &bull; {{ $row['balita']->umur_jk }}</div>
                        </td>
                        <td>
                            <strong style="color: #1e293b;">{{ $row['imunisasi'] }}</strong>
                        </td>
                        <td>
                            <span>{{ $row['jadwal'] }}</span>
                        </td>
                        <td style="text-align: center;">
                            @if($row['status_pill'] === 'tertunda')
                                <span class="badge-status tidak_aktif">🔴 Tertunda</span>
                            @elseif($row['status_pill'] === 'mendatang')
                                <span class="badge-status pindah" style="background: #fef9c3; color: #854d0e; border: 1px solid #fef08a;">🟡 Mendatang</span>
                            @else
                                <span class="badge-status aktif">🟢 Lengkap</span>
                            @endif
                        </td>
                        <td style="text-align: right;">
                            <a href="{{ route('kader.kegiatan.pemeriksaan', ['tab' => 'balita', 'balita_id' => $row['balita']->id]) }}" class="btn-action edit">
                                📝 Update
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" style="text-align: center; padding: 32px 16px; color: #94a3b8;">
                            Belum ada catatan imunisasi balita.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection
