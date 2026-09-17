@extends('layouts.app')

@section('title', 'Detail Balita: ' . $balita->nama)
@section('page-title', 'Detail Balita')

@section('content')
<div class="page-heading">
    <div>
        <span class="eyebrow">DETAIL REKAM MEDIS</span>
        <h1>{{ $balita->nama }}</h1>
        <p>Informasi profil lengkap dan riwayat pemeriksaan pertumbuhan balita.</p>
    </div>

    <div style="display: flex; gap: 8px;">
        <a href="{{ route('balita.edit', $balita) }}" class="btn-primary" style="background: #eab308; color: #713f12;">
            ✏️ Edit Data
        </a>
        <a href="{{ route('balita.index') }}" class="btn-secondary">
            &larr; Kembali
        </a>
    </div>
</div>

<div class="dashboard-grid" style="grid-template-columns: 1fr 2fr; align-items: flex-start; gap: 20px;">
    {{-- PROFILE CARD --}}
    <div class="dashboard-card" style="padding: 24px;">
        <h2 style="font-size: 16px; font-weight: 700; margin-bottom: 16px; color: #172b26; border-bottom: 1px solid #f1f5f9; padding-bottom: 10px;">
            Data Diri Balita
        </h2>

        <div style="display: flex; flex-direction: column; gap: 12px; font-size: 13px;">
            <div>
                <span style="font-size: 11px; color: #64748b; font-weight: 600; text-transform: uppercase;">NIK</span>
                <div style="font-weight: 700; color: #1e293b;">{{ $balita->nik }}</div>
            </div>

            <div>
                <span style="font-size: 11px; color: #64748b; font-weight: 600; text-transform: uppercase;">Jenis Kelamin</span>
                <div style="font-weight: 600;">{{ $balita->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan' }}</div>
            </div>

            <div>
                <span style="font-size: 11px; color: #64748b; font-weight: 600; text-transform: uppercase;">Tanggal Lahir / Umur</span>
                <div style="font-weight: 600;">
                    {{ $balita->tanggal_lahir?->format('d F Y') }} ({{ $balita->umur_jk }})
                </div>
            </div>

            <div>
                <span style="font-size: 11px; color: #64748b; font-weight: 600; text-transform: uppercase;">Nama Ibu</span>
                <div style="font-weight: 600;">{{ $balita->nama_ibu ?? '-' }}</div>
            </div>

            <div>
                <span style="font-size: 11px; color: #64748b; font-weight: 600; text-transform: uppercase;">Nama Ayah</span>
                <div style="font-weight: 600;">{{ $balita->nama_ayah ?? '-' }}</div>
            </div>

            <div>
                <span style="font-size: 11px; color: #64748b; font-weight: 600; text-transform: uppercase;">No. HP Orang Tua</span>
                <div style="font-weight: 600;">{{ $balita->no_hp_orang_tua ?? '-' }}</div>
            </div>

            <div>
                <span style="font-size: 11px; color: #64748b; font-weight: 600; text-transform: uppercase;">Posyandu / Tapos</span>
                <div style="font-weight: 700; color: var(--primary);">{{ $balita->tapos?->nama ?? '-' }}</div>
            </div>

            <div>
                <span style="font-size: 11px; color: #64748b; font-weight: 600; text-transform: uppercase;">Status Balita</span>
                <div style="margin-top: 4px;">
                    <span class="badge-status {{ $balita->status === 'aktif' ? 'aktif' : 'tidak_aktif' }}">
                        {{ ucfirst($balita->status) }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    {{-- EXAMINATION HISTORY CARD --}}
    <div class="dashboard-card" style="padding: 0; overflow: hidden;">
        <div style="padding: 18px 20px; border-bottom: 1px solid #f1f5f9;">
            <h2 style="font-size: 16px; font-weight: 700; color: #172b26;">
                Riwayat Pemeriksaan Pertumbuhan
            </h2>
            <p style="font-size: 12px; color: #64748b; margin-top: 2px;">Catatan penimbangan berat dan tinggi badan bulanan</p>
        </div>

        <div class="table-responsive">
            <table class="custom-table">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Berat</th>
                        <th>Tinggi</th>
                        <th>Status Stunting</th>
                        <th>Imunisasi</th>
                        <th style="text-align: center;">Kehadiran</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($balita->pemeriksaan()->orderBy('tanggal_pemeriksaan', 'desc')->get() as $p)
                        <tr>
                            <td>
                                <strong>{{ $p->tanggal_pemeriksaan?->format('d/m/Y') }}</strong>
                            </td>
                            <td>{{ $p->kehadiran ? $p->berat_badan . ' kg' : '-' }}</td>
                            <td>{{ $p->kehadiran ? $p->tinggi_badan . ' cm' : '-' }}</td>
                            <td>
                                @if($p->kehadiran)
                                    @if($p->status_stunting === 'risiko_stunting' || $p->status_stunting === 'stunting')
                                        <span class="badge-stunting risiko">🔴 Risiko</span>
                                    @elseif($p->status_stunting === 'pemantauan')
                                        <span class="badge-stunting pemantauan">🟠 Pemantauan</span>
                                    @else
                                        <span class="badge-stunting normal">🟢 Normal</span>
                                    @endif
                                @else
                                    <span class="badge-stunting belum">-</span>
                                @endif
                            </td>
                            <td>
                                @if($p->kehadiran)
                                    <span class="badge-stunting {{ $p->status_imunisasi === 'lengkap' ? 'normal' : ($p->status_imunisasi === 'tertunda' ? 'risiko' : 'pemantauan') }}">
                                        {{ ucfirst(str_replace('_', ' ', $p->status_imunisasi)) }}
                                    </span>
                                @else
                                    <span class="badge-stunting belum">-</span>
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
                                Belum ada catatan riwayat pemeriksaan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
