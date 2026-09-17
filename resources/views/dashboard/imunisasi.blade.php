@extends('layouts.app')

@section('title', 'Imunisasi Balita')
@section('page-title', 'Imunisasi Balita')

@section('content')
<div class="page-heading">
    <div>
        <span class="eyebrow">PROGRAM IMUNISASI NASIONAL</span>
        <h1>💉 Status Imunisasi Balita</h1>
        <p>
            Pantau cakupan imunisasi dasar lengkap dan balita dengan jadwal imunisasi tertunda.
        </p>
    </div>
    <div class="date-card">
        <span>Cakupan Lengkap</span>
        <strong>
            {{ count($imunisasiList) > 0 ? round(($lengkapCount / count($imunisasiList)) * 100) : 0 }}%
        </strong>
    </div>
</div>

{{-- STATUS STATS --}}
<div class="stats-grid" style="grid-template-columns: repeat(3, 1fr); margin-bottom: 20px;">
    <div class="stat-card">
        <span class="stat-label">Imunisasi Lengkap</span>
        <strong class="stat-number" style="color: #16a34a;">{{ $lengkapCount }}</strong>
        <span class="stat-description">Sesuai usia perkembangan</span>
    </div>
    <div class="stat-card">
        <span class="stat-label">Belum Lengkap</span>
        <strong class="stat-number" style="color: #ea580c;">{{ $belumLengkapCount }}</strong>
        <span class="stat-description">Menunggu tahap berikutnya</span>
    </div>
    <div class="stat-card">
        <span class="stat-label">Tertunda</span>
        <strong class="stat-number" style="color: #dc2626;">{{ $tertundaCount }}</strong>
        <span class="stat-description">Perlu tindak lanjut / sweeping</span>
    </div>
</div>

{{-- TABEL IMUNISASI --}}
<div class="dashboard-card" style="padding: 20px;">
    <div class="table-responsive">
        <table class="custom-table">
            <thead>
                <tr>
                    <th>Nama Balita</th>
                    <th>Tapos</th>
                    <th>Tanggal Lahir / Umur</th>
                    <th>Pemeriksaan Terakhir</th>
                    <th>Status Imunisasi</th>
                    <th style="text-align: center;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($imunisasiList as $item)
                    <tr>
                        <td>
                            <strong>{{ $item['balita']->nama }}</strong>
                            <div style="font-size: 11px; color: #64748b;">NIK: {{ $item['balita']->nik }}</div>
                        </td>
                        <td>{{ $item['balita']->tapos?->nama ?? '-' }}</td>
                        <td>
                            <div>{{ $item['balita']->tanggal_lahir?->format('d/m/Y') }}</div>
                            <div style="font-size: 11px; color: #64748b;">{{ $item['balita']->umur_jk }}</div>
                        </td>
                        <td>{{ $item['tanggal'] }}</td>
                        <td>
                            @if($item['status'] === 'lengkap')
                                <span class="badge-stunting normal">🟢 Lengkap</span>
                            @elseif($item['status'] === 'tertunda')
                                <span class="badge-stunting risiko">🔴 Tertunda</span>
                            @else
                                <span class="badge-stunting pemantauan">🟠 Belum Lengkap</span>
                            @endif
                        </td>
                        <td style="text-align: center;">
                            <a href="{{ route('balita.show', $item['balita']->id) }}" class="btn-detail-link">
                                Detail
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="text-align: center; padding: 24px; color: #94a3b8;">
                            Belum ada data imunisasi balita.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
