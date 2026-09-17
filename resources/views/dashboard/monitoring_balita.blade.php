@extends('layouts.app')

@section('title', 'Monitoring Balita Wilayah')
@section('page-title', 'Monitoring Balita Wilayah')

@section('content')

<div class="page-heading">
    <div>
        <span class="eyebrow">SCREENING PERTUMBUHAN & EARLY WARNING STUNTING</span>
        <h1>Monitoring Balita Wilayah Puskesmas 👶</h1>
        <p>
            Pantau status pertumbuhan anak, screening risiko awal stunting berbasis standar WHO/DHS, dan tren longitudinal intervensi Posyandu.
        </p>
    </div>
    <div class="date-card">
        <span>Total Terpantau</span>
        <strong>{{ count($items) }} Balita</strong>
    </div>
</div>

{{-- SUMMARY STATUS CARDS --}}
<div class="stats-grid" style="grid-template-columns: repeat(auto-fit, minmax(170px, 1fr)); gap: 14px; margin-bottom: 20px;">
    <div class="stat-card" style="border-left: 4px solid #ef4444;">
        <span class="stat-label">🔴 Risiko Stunting</span>
        <strong class="stat-number" style="color: #dc2626;">{{ $risikoCount }}</strong>
        <span class="stat-description">Perlu intervensi gizi</span>
    </div>

    <div class="stat-card" style="border-left: 4px solid #f59e0b;">
        <span class="stat-label">🟡 Pemantauan</span>
        <strong class="stat-number" style="color: #d97706;">{{ $pemantauanCount }}</strong>
        <span class="stat-description">Pertumbuhan melambat</span>
    </div>

    <div class="stat-card" style="border-left: 4px solid #10b981;">
        <span class="stat-label">🟢 Normal</span>
        <strong class="stat-number" style="color: #16a34a;">{{ $normalCount }}</strong>
        <span class="stat-description">Sesuai kurva standar</span>
    </div>

    <div class="stat-card" style="border-left: 4px solid #94a3b8;">
        <span class="stat-label">⚪ Belum Diperiksa</span>
        <strong class="stat-number" style="color: #64748b;">{{ $belumDiperiksaCount }}</strong>
        <span class="stat-description">Perlu kunjungan</span>
    </div>
</div>

{{-- FILTER SECTION --}}
<div class="dashboard-card" style="margin-bottom: 20px; padding: 16px;">
    <form method="GET" action="{{ route('admin.monitoring.balita') }}" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 14px;">
        <div style="display: flex; gap: 8px; flex-wrap: wrap; align-items: center;">
            <span style="font-size: 13px; font-weight: 700; color: #475569;">Filter Status:</span>
            <a href="{{ route('admin.monitoring.balita', ['status' => 'semua', 'tapos_id' => $selectedTaposId]) }}" class="status-pill {{ $statusFilter === 'semua' ? 'info' : 'secondary' }}" style="text-decoration:none;">Semua</a>
            <a href="{{ route('admin.monitoring.balita', ['status' => 'risiko_stunting', 'tapos_id' => $selectedTaposId]) }}" class="status-pill {{ $statusFilter === 'risiko_stunting' ? 'danger' : 'secondary' }}" style="text-decoration:none;">🔴 Risiko Stunting ({{ $risikoCount }})</a>
            <a href="{{ route('admin.monitoring.balita', ['status' => 'pemantauan', 'tapos_id' => $selectedTaposId]) }}" class="status-pill {{ $statusFilter === 'pemantauan' ? 'warning' : 'secondary' }}" style="text-decoration:none;">🟡 Pemantauan ({{ $pemantauanCount }})</a>
            <a href="{{ route('admin.monitoring.balita', ['status' => 'normal', 'tapos_id' => $selectedTaposId]) }}" class="status-pill {{ $statusFilter === 'normal' ? 'success' : 'secondary' }}" style="text-decoration:none;">🟢 Normal ({{ $normalCount }})</a>
            <a href="{{ route('admin.monitoring.balita', ['status' => 'belum_diperiksa', 'tapos_id' => $selectedTaposId]) }}" class="status-pill {{ $statusFilter === 'belum_diperiksa' ? 'secondary' : 'secondary' }}" style="text-decoration:none;">⚪ Belum Diperiksa</a>
        </div>

        <div style="display: flex; align-items: center; gap: 10px;">
            <label style="font-size: 13px; font-weight: 600; color: #475569;">Tapos:</label>
            <select name="tapos_id" onchange="this.form.submit()" style="padding: 6px 12px; border-radius: 8px; border: 1px solid #cbd5e1; font-size: 13px;">
                <option value="semua" {{ $selectedTaposId === 'semua' ? 'selected' : '' }}>Semua Tapos</option>
                @foreach($taposList as $t)
                    <option value="{{ $t->id }}" {{ $selectedTaposId == $t->id ? 'selected' : '' }}>{{ $t->nama }}</option>
                @endforeach
            </select>
            <input type="hidden" name="status" value="{{ $statusFilter }}">
        </div>
    </form>
</div>

{{-- DATA TABLE --}}
<div class="dashboard-card">
    <div class="card-header">
        <div>
            <span class="card-eyebrow">DAFTAR BALITA</span>
            <h3>Hasil Screening & Monitoring Wilayah</h3>
        </div>
        <span class="badge secondary">{{ count($items) }} Pasien</span>
    </div>

    <div class="table-responsive" style="margin-top: 16px;">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Balita</th>
                    <th>Tapos</th>
                    <th>Umur / JK</th>
                    <th>Pemeriksaan Terakhir</th>
                    <th>Antropometri (BB/TB)</th>
                    <th>Screening AI</th>
                    <th>Tren Longitudinal</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($items as $row)
                    <tr>
                        <td>
                            <strong>{{ $row['balita']->nama }}</strong>
                            <div style="font-size: 11px; color: #64748b;">NIK: {{ $row['balita']->nik }}</div>
                        </td>
                        <td>
                            <span class="badge secondary" style="font-size: 11px;">{{ $row['tapos_nama'] }}</span>
                        </td>
                        <td>{{ $row['umur_jk'] }}</td>
                        <td>{{ $row['tanggal_terakhir'] }}</td>
                        <td>
                            @if($row['berat_badan'])
                                <div><strong>BB:</strong> {{ $row['berat_badan'] }} kg</div>
                                <div><strong>TB:</strong> {{ $row['tinggi_badan'] }} cm</div>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>
                            @if($row['status'] !== 'belum_diperiksa')
                                <strong>{{ $row['ai_prediksi'] }}</strong>
                                <div style="font-size: 10.5px; color: #64748b;">Stunting AI v1.0</div>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>
                            {!! $row['trend']['sequence_html'] !!}
                            <div style="font-size: 11px; margin-top: 2px;">
                                {!! $row['trend']['trend_badge'] !!}
                            </div>
                        </td>
                        <td>
                            @if($row['status'] === 'risiko_stunting' || $row['status'] === 'stunting')
                                <span class="status-pill danger">🔴 Risiko Stunting</span>
                            @elseif($row['status'] === 'pemantauan')
                                <span class="status-pill warning">🟡 Pemantauan</span>
                            @elseif($row['status'] === 'normal')
                                <span class="status-pill success">🟢 Normal</span>
                            @else
                                <span class="status-pill secondary">⚪ Belum Diperiksa</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center" style="padding: 24px; color: #64748b;">
                            Tidak ada data balita yang cocok dengan filter yang dipilih.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div style="margin-top: 16px; padding: 12px; border-radius: 10px; background: #fff; border: 1px solid #e2e8f0; font-size: 12px; color: #64748b; display: flex; align-items: center; gap: 8px;">
    <span>⚠️</span>
    <span><strong>Disclaimer Medis:</strong> Hasil AI pada sistem Posyandu Smart berfungsi sebagai <em>screening awal & decision support</em>. Diagnosis klinis stunting tetap ditegakkan oleh dokter/tenaga kesehatan Puskesmas.</span>
</div>

@endsection
