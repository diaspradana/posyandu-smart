@extends('layouts.app')

@section('title', 'Monitoring Ibu Hamil Wilayah')
@section('page-title', 'Monitoring Ibu Hamil Wilayah')

@section('content')

<div class="page-heading">
    <div>
        <span class="eyebrow">MATERNAL HEALTH EARLY WARNING & RISK SCREENING</span>
        <h1>Monitoring Ibu Hamil Wilayah Puskesmas 🤰</h1>
        <p>
            Supervisi kesehatan maternal dari faktor usia, tekanan darah, gula darah, dan denyut jantung untuk deteksi dini komplikasi kehamilan.
        </p>
    </div>
    <div class="date-card">
        <span>Total Terpantau</span>
        <strong>{{ count($items) }} Ibu Hamil</strong>
    </div>
</div>

{{-- SUMMARY STATUS CARDS --}}
<div class="stats-grid" style="grid-template-columns: repeat(auto-fit, minmax(170px, 1fr)); gap: 14px; margin-bottom: 20px;">
    <div class="stat-card" style="border-left: 4px solid #ef4444;">
        <span class="stat-label">🔴 High Risk</span>
        <strong class="stat-number" style="color: #dc2626;">{{ $highCount }}</strong>
        <span class="stat-description">Perlu rujukan/supervisi intensif</span>
    </div>

    <div class="stat-card" style="border-left: 4px solid #f59e0b;">
        <span class="stat-label">🟡 Medium Risk</span>
        <strong class="stat-number" style="color: #d97706;">{{ $mediumCount }}</strong>
        <span class="stat-description">Perlu pemantauan 2 mingguan</span>
    </div>

    <div class="stat-card" style="border-left: 4px solid #10b981;">
        <span class="stat-label">🟢 Low Risk</span>
        <strong class="stat-number" style="color: #16a34a;">{{ $lowCount }}</strong>
        <span class="stat-description">Kondisi kehamilan baik</span>
    </div>

    <div class="stat-card" style="border-left: 4px solid #94a3b8;">
        <span class="stat-label">⚪ Belum Diperiksa</span>
        <strong class="stat-number" style="color: #64748b;">{{ $belumDiperiksaCount }}</strong>
        <span class="stat-description">Perlu jadwal pemeriksaan</span>
    </div>
</div>

{{-- FILTER SECTION --}}
<div class="dashboard-card" style="margin-bottom: 20px; padding: 16px;">
    <form method="GET" action="{{ route('admin.monitoring.ibu-hamil') }}" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 14px;">
        <div style="display: flex; gap: 8px; flex-wrap: wrap; align-items: center;">
            <span style="font-size: 13px; font-weight: 700; color: #475569;">Filter Risiko:</span>
            <a href="{{ route('admin.monitoring.ibu-hamil', ['risk' => 'semua', 'tapos_id' => $selectedTaposId]) }}" class="status-pill {{ $riskFilter === 'semua' ? 'info' : 'secondary' }}" style="text-decoration:none;">Semua</a>
            <a href="{{ route('admin.monitoring.ibu-hamil', ['risk' => 'high', 'tapos_id' => $selectedTaposId]) }}" class="status-pill {{ $riskFilter === 'high' ? 'danger' : 'secondary' }}" style="text-decoration:none;">🔴 High Risk ({{ $highCount }})</a>
            <a href="{{ route('admin.monitoring.ibu-hamil', ['risk' => 'medium', 'tapos_id' => $selectedTaposId]) }}" class="status-pill {{ $riskFilter === 'medium' ? 'warning' : 'secondary' }}" style="text-decoration:none;">🟡 Medium Risk ({{ $mediumCount }})</a>
            <a href="{{ route('admin.monitoring.ibu-hamil', ['risk' => 'low', 'tapos_id' => $selectedTaposId]) }}" class="status-pill {{ $riskFilter === 'low' ? 'success' : 'secondary' }}" style="text-decoration:none;">🟢 Low Risk ({{ $lowCount }})</a>
            <a href="{{ route('admin.monitoring.ibu-hamil', ['risk' => 'belum_diperiksa', 'tapos_id' => $selectedTaposId]) }}" class="status-pill {{ $riskFilter === 'belum_diperiksa' ? 'secondary' : 'secondary' }}" style="text-decoration:none;">⚪ Belum Diperiksa</a>
        </div>

        <div style="display: flex; align-items: center; gap: 10px;">
            <label style="font-size: 13px; font-weight: 600; color: #475569;">Tapos:</label>
            <select name="tapos_id" onchange="this.form.submit()" style="padding: 6px 12px; border-radius: 8px; border: 1px solid #cbd5e1; font-size: 13px;">
                <option value="semua" {{ $selectedTaposId === 'semua' ? 'selected' : '' }}>Semua Tapos</option>
                @foreach($taposList as $t)
                    <option value="{{ $t->id }}" {{ $selectedTaposId == $t->id ? 'selected' : '' }}>{{ $t->nama }}</option>
                @endforeach
            </select>
            <input type="hidden" name="risk" value="{{ $riskFilter }}">
        </div>
    </form>
</div>

{{-- DATA TABLE --}}
<div class="dashboard-card">
    <div class="card-header">
        <div>
            <span class="card-eyebrow">DAFTAR IBU HAMIL</span>
            <h3>Hasil Screening Maternal & Monitoring Wilayah</h3>
        </div>
        <span class="badge secondary">{{ count($items) }} Pasien</span>
    </div>

    <div class="table-responsive" style="margin-top: 16px;">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Ibu Hamil</th>
                    <th>Tapos</th>
                    <th>Usia Kehamilan</th>
                    <th>Pemeriksaan Terakhir</th>
                    <th>Klinis (TD / Gula)</th>
                    <th>Screening AI</th>
                    <th>Tren Longitudinal</th>
                    <th>Tingkat Risiko</th>
                </tr>
            </thead>
            <tbody>
                @forelse($items as $row)
                    <tr>
                        <td>
                            <strong>{{ $row['ibu_hamil']->nama }}</strong>
                            <div style="font-size: 11px; color: #64748b;">NIK: {{ $row['ibu_hamil']->nik }}</div>
                        </td>
                        <td>
                            <span class="badge secondary" style="font-size: 11px;">{{ $row['tapos_nama'] }}</span>
                        </td>
                        <td>{{ $row['usia_kehamilan'] }}</td>
                        <td>{{ $row['tanggal_terakhir'] }}</td>
                        <td>
                            <div><strong>TD:</strong> {{ $row['tekanan_darah'] }}</div>
                            <div><strong>Gula:</strong> {{ $row['gula_darah'] }}</div>
                        </td>
                        <td>
                            @if($row['risk_level'] !== 'belum_diperiksa')
                                <strong>{{ $row['ai_prediksi'] }}</strong>
                                <div style="font-size: 10.5px; color: #64748b;">Maternal AI v1.0</div>
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
                            @if($row['risk_level'] === 'high')
                                <span class="status-pill danger">🔴 High Risk</span>
                            @elseif($row['risk_level'] === 'medium')
                                <span class="status-pill warning">🟡 Medium Risk</span>
                            @elseif($row['risk_level'] === 'low')
                                <span class="status-pill success">🟢 Low Risk</span>
                            @else
                                <span class="status-pill secondary">⚪ Belum Diperiksa</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center" style="padding: 24px; color: #64748b;">
                            Tidak ada data ibu hamil yang cocok dengan filter yang dipilih.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div style="margin-top: 16px; padding: 12px; border-radius: 10px; background: #fff; border: 1px solid #e2e8f0; font-size: 12px; color: #64748b; display: flex; align-items: center; gap: 8px;">
    <span>⚠️</span>
    <span><strong>Disclaimer Medis:</strong> AI Maternal Health Risk berfungsi sebagai sistem <em>early warning & decision support</em>. Penilaian klinis dan diagnosis tetap dilakukan oleh Bidan / Dokter Puskesmas.</span>
</div>

@endsection
