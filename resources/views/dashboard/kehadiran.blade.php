@extends('layouts.app')

@section('title', 'Rekap Kehadiran Posyandu')
@section('page-title', 'Kehadiran Posyandu')

@section('content')
<div class="page-heading">
    <div>
        <span class="eyebrow">MONITORING KEHADIRAN & PARTISIPASI MASYARAKAT</span>
        <h1>📅 Rekap Kehadiran & Agenda Posyandu</h1>
        <p>
            Pantau tingkat partisipasi masyarakat di setiap Posyandu Tapos, tren bulanan, dan jadwal pelaksanaan kegiatan.
        </p>
    </div>
    <div class="date-card">
        <span>Tahun Evaluasi</span>
        <strong>2026</strong>
    </div>
</div>

{{-- GRAFIK BULANAN --}}
<div class="dashboard-card attendance-card" style="margin-bottom: 24px;">
    <div class="card-header">
        <div>
            <span class="card-eyebrow">TREN TINGKAT KEHADIRAN</span>
            <h2>Partisipasi Bulanan Wilayah (Rata-rata Gabungan)</h2>
            <p>Persentase kehadiran sasaran balita dan ibu hamil tiap bulan di wilayah kerja Puskesmas</p>
        </div>
        <span class="badge primary" style="align-self: flex-start;">Rata-rata: 88.5%</span>
    </div>

    <div class="bar-chart" style="min-height: 190px; display: flex; align-items: flex-end; justify-content: space-around; padding: 24px 10px 14px;">
        @foreach($kehadiran as $bulan => $nilai)
            <div class="bar-column" style="display: flex; flex-direction: column; align-items: center; gap: 6px;">
                <div class="bar-value" style="font-size: 11px; font-weight: 700; color: #334155;">{{ $nilai }}%</div>
                <div class="bar" style="height: {{ max(20, $nilai * 1.6) }}px; width: 32px; background: linear-gradient(180deg, var(--primary), var(--primary-dark)); border-radius: 6px 6px 0 0;"></div>
                <span style="font-size: 11px; color: #64748b; font-weight: 600;">{{ substr($bulan, 0, 3) }}</span>
            </div>
        @endforeach
    </div>
</div>

{{-- MATRIX KEHADIRAN PER TAPOS --}}
@if(!empty($matrixKehadiran))
<div class="dashboard-card" style="margin-bottom: 24px;">
    <div class="card-header" style="margin-bottom: 16px;">
        <div>
            <span class="card-eyebrow">DISTRIBUSI KEHADIRAN TAPOS</span>
            <h2>Performa Kehadiran Per Posyandu Tapos</h2>
            <p>Persentase kehadiran masyarakat di masing-masing Posyandu Tapos</p>
        </div>
        <span class="badge secondary">{{ count($matrixKehadiran) }} Posyandu</span>
    </div>

    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Nama Posyandu Tapos</th>
                    <th>Kelurahan</th>
                    <th>Rata-rata Kehadiran</th>
                    <th>Status Partisipasi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($matrixKehadiran as $m)
                    <tr>
                        <td>
                            <strong>{{ $m['tapos']->nama }}</strong>
                            <div style="font-size: 11px; color: #64748b;">Kode: {{ $m['tapos']->kode }}</div>
                        </td>
                        <td>{{ $m['tapos']->kelurahan }}</td>
                        <td>
                            <div style="display: flex; align-items: center; gap: 10px;">
                                <div style="flex: 1; max-width: 140px; height: 8px; background: #e2e8f0; border-radius: 4px; overflow: hidden;">
                                    <div style="width: {{ $m['average'] }}%; height: 100%; background: {{ $m['average'] >= 85 ? '#10b981' : ($m['average'] >= 75 ? '#f59e0b' : '#ef4444') }};"></div>
                                </div>
                                <strong>{{ $m['average'] }}%</strong>
                            </div>
                        </td>
                        <td>
                            @if($m['average'] >= 85)
                                <span class="status-pill success">🟢 Sangat Baik</span>
                            @elseif($m['average'] >= 75)
                                <span class="status-pill warning">🟡 Cukup Baik</span>
                            @else
                                <span class="status-pill danger">🔴 Perlu Dorongan</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif

{{-- JADWAL & RIWAYAT POSYANDU --}}
<div class="dashboard-card" style="padding: 20px;">
    <div class="card-header" style="margin-bottom: 16px;">
        <div>
            <span class="card-eyebrow">AGENDA PELAYANAN</span>
            <h2>Jadwal & Riwayat Kegiatan Posyandu</h2>
        </div>
        <span class="badge secondary">{{ $jadwals->total() }} Kegiatan</span>
    </div>

    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Tanggal Pelaksanaan</th>
                    <th>Posyandu Tapos</th>
                    <th>Nama Kegiatan</th>
                    <th>Waktu Pelayanan</th>
                    <th>Status Kegiatan</th>
                </tr>
            </thead>
            <tbody>
                @forelse($jadwals as $j)
                    <tr>
                        <td>
                            <strong>{{ $j->tanggal?->format('d/m/Y') }}</strong>
                            <div style="font-size: 11px; color: #64748b;">{{ $j->tanggal?->translatedFormat('l') }}</div>
                        </td>
                        <td>
                            <strong>{{ $j->tapos?->nama ?? '-' }}</strong>
                            <div style="font-size: 11px; color: #64748b;">{{ $j->tapos?->kelurahan ?? '' }}</div>
                        </td>
                        <td>{{ $j->nama_kegiatan }}</td>
                        <td>{{ substr($j->waktu_mulai, 0, 5) }} - {{ substr($j->waktu_selesai, 0, 5) }} WIB</td>
                        <td>
                            @if($j->status === 'selesai')
                                <span class="status-pill success">✓ Selesai</span>
                            @elseif($j->status === 'mendatang')
                                <span class="status-pill info">⏳ Mendatang</span>
                            @else
                                <span class="status-pill secondary">{{ ucfirst($j->status) }}</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center" style="padding: 24px; color: #94a3b8;">
                            Belum ada agenda jadwal posyandu.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div style="margin-top: 16px;">
        {{ $jadwals->links() }}
    </div>
</div>
@endsection

