@extends('layouts.app')

@section('title', 'Detail Ibu Hamil: ' . $ibuHamil->nama)
@section('page-title', 'Detail Ibu Hamil')

@section('content')
<div class="page-heading">
    <div>
        <span class="eyebrow">DETAIL REKAM MEDIS</span>
        <h1>{{ $ibuHamil->nama }}</h1>
        <p>Informasi profil lengkap dan riwayat pemeriksaan kehamilan berkala.</p>
    </div>

    <div style="display: flex; gap: 8px;">
        <a href="{{ route('ibu-hamil.edit', $ibuHamil) }}" class="btn-primary" style="background: #eab308; color: #713f12;">
            ✏️ Edit Data
        </a>
        <a href="{{ route('ibu-hamil.index') }}" class="btn-secondary">
            &larr; Kembali
        </a>
    </div>
</div>

<div class="dashboard-grid" style="grid-template-columns: 1fr 2fr; align-items: flex-start; gap: 20px;">
    {{-- PROFILE CARD --}}
    <div class="dashboard-card" style="padding: 24px;">
        <h2 style="font-size: 16px; font-weight: 700; margin-bottom: 16px; color: #172b26; border-bottom: 1px solid #f1f5f9; padding-bottom: 10px;">
            Data Diri Ibu Hamil
        </h2>

        <div style="display: flex; flex-direction: column; gap: 12px; font-size: 13px;">
            <div>
                <span style="font-size: 11px; color: #64748b; font-weight: 600; text-transform: uppercase;">NIK</span>
                <div style="font-weight: 700; color: #1e293b;">{{ $ibuHamil->nik }}</div>
            </div>

            <div>
                <span style="font-size: 11px; color: #64748b; font-weight: 600; text-transform: uppercase;">Tanggal Lahir / Usia</span>
                <div style="font-weight: 600;">
                    {{ \Carbon\Carbon::parse($ibuHamil->tanggal_lahir)->format('d F Y') }} ({{ \Carbon\Carbon::parse($ibuHamil->tanggal_lahir)->age }} Tahun)
                </div>
            </div>

            <div>
                <span style="font-size: 11px; color: #64748b; font-weight: 600; text-transform: uppercase;">Hari Pertama Haid Terakhir (HPHT)</span>
                <div style="font-weight: 600;">
                    {{ $ibuHamil->hari_pertama_haid_terakhir ? \Carbon\Carbon::parse($ibuHamil->hari_pertama_haid_terakhir)->format('d F Y') : '-' }}
                </div>
            </div>

            <div>
                <span style="font-size: 11px; color: #64748b; font-weight: 600; text-transform: uppercase;">Kehamilan Ke-</span>
                <div style="font-weight: 700; color: var(--primary);">Gravida (G{{ $ibuHamil->kehamilan_ke }})</div>
            </div>

            <div>
                <span style="font-size: 11px; color: #64748b; font-weight: 600; text-transform: uppercase;">Usia Kehamilan Terakhir</span>
                <div style="font-weight: 600;">{{ $ibuHamil->usia_kehamilan_minggu ?? '-' }} Minggu</div>
            </div>

            <div>
                <span style="font-size: 11px; color: #64748b; font-weight: 600; text-transform: uppercase;">Alamat Domisili</span>
                <div style="font-weight: 600;">{{ $ibuHamil->alamat ?? '-' }}</div>
            </div>

            <div>
                <span style="font-size: 11px; color: #64748b; font-weight: 600; text-transform: uppercase;">No. Kontak / HP</span>
                <div style="font-weight: 600;">{{ $ibuHamil->no_hp ?? '-' }}</div>
            </div>

            <div>
                <span style="font-size: 11px; color: #64748b; font-weight: 600; text-transform: uppercase;">Posyandu / Tapos</span>
                <div style="font-weight: 700; color: var(--primary);">{{ $ibuHamil->tapos?->nama ?? '-' }}</div>
            </div>

            <div>
                <span style="font-size: 11px; color: #64748b; font-weight: 600; text-transform: uppercase;">Status Ibu Hamil</span>
                <div style="margin-top: 4px;">
                    <span class="badge-status {{ $ibuHamil->status === 'aktif' ? 'aktif' : 'tidak_aktif' }}">
                        {{ ucfirst($ibuHamil->status) }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    {{-- EXAMINATION HISTORY CARD --}}
    <div class="dashboard-card" style="padding: 0; overflow: hidden;">
        <div style="padding: 18px 20px; border-bottom: 1px solid #f1f5f9;">
            <h2 style="font-size: 16px; font-weight: 700; color: #172b26;">
                Riwayat Pemeriksaan Kehamilan
            </h2>
            <p style="font-size: 12px; color: #64748b; margin-top: 2px;">Catatan berat badan, tensi darah, dan usia kehamilan berkala</p>
        </div>

        <div class="table-responsive">
            <table class="custom-table">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Berat Badan</th>
                        <th>Tekanan Darah</th>
                        <th>Usia Kehamilan</th>
                        <th>Status Pemeriksaan</th>
                        <th style="text-align: center;">Kehadiran</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($ibuHamil->pemeriksaan()->orderBy('tanggal_pemeriksaan', 'desc')->get() as $p)
                        <tr>
                            <td>
                                <strong>{{ $p->tanggal_pemeriksaan?->format('d/m/Y') }}</strong>
                            </td>
                            <td>{{ $p->kehadiran ? $p->berat_badan . ' kg' : '-' }}</td>
                            <td>{{ $p->kehadiran ? $p->tekanan_darah : '-' }}</td>
                            <td>{{ $p->kehadiran ? $p->usia_kehamilan_minggu . ' minggu' : '-' }}</td>
                            <td>
                                @if($p->kehadiran && $p->status_pemeriksaan === 'diperiksa')
                                    <span class="badge-stunting normal">🟢 Diperiksa</span>
                                @else
                                    <span class="badge-stunting risiko">🔴 Belum Diperiksa</span>
                                @endif
                            </td>
                            <td style="text-align: center;">
                                <span class="badge-stunting {{ $p->kehadiran ? 'normal' : 'risiko' }}">
                                    {{ $p->kehadiran ? 'Hadir' : 'Tidak Hadir' }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align: center; padding: 24px; color: #94a3b8;">
                                Belum ada catatan riwayat pemeriksaan kehamilan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
