@extends('layouts.app')

@section('title', 'Monitoring Ibu Hamil')
@section('page-title', 'Monitoring Ibu Hamil')

@section('content')

<div class="page-heading">
    <div>
        <span class="eyebrow">MATERNAL RISK EARLY WARNING & SCREENING</span>
        <h1>Monitoring Ibu Hamil Posyandu {{ $tapos->nama }} 🤰</h1>
        <p>
            Pemantauan berkala faktor risiko kesehatan ibu hamil berbasis screening AI, riwayat klinis, dan tren kehamilan.
        </p>
    </div>
    <div class="date-card">
        <span>Sasaran Tapos</span>
        <strong>{{ count($items) }} Ibu Hamil</strong>
    </div>
</div>

{{-- STATUS FILTER BUTTONS (ITEM 14 BLUEPRINT) --}}
<div style="display: flex; gap: 8px; flex-wrap: wrap; margin-bottom: 20px;">
    <a href="{{ route('kader.monitoring.ibu-hamil', ['risk' => 'semua']) }}" class="status-pill {{ ($riskFilter ?? 'semua') === 'semua' ? 'info' : 'secondary' }}" style="text-decoration:none; font-size:13px; padding:6px 14px;">
        Semua ({{ count($items) }})
    </a>
    <a href="{{ route('kader.monitoring.ibu-hamil', ['risk' => 'high']) }}" class="status-pill {{ ($riskFilter ?? '') === 'high' ? 'danger' : 'secondary' }}" style="text-decoration:none; font-size:13px; padding:6px 14px;">
        🔴 High Risk ({{ $highCount }})
    </a>
    <a href="{{ route('kader.monitoring.ibu-hamil', ['risk' => 'medium']) }}" class="status-pill {{ ($riskFilter ?? '') === 'medium' ? 'warning' : 'secondary' }}" style="text-decoration:none; font-size:13px; padding:6px 14px;">
        🟡 Medium Risk ({{ $mediumCount }})
    </a>
    <a href="{{ route('kader.monitoring.ibu-hamil', ['risk' => 'low']) }}" class="status-pill {{ ($riskFilter ?? '') === 'low' ? 'success' : 'secondary' }}" style="text-decoration:none; font-size:13px; padding:6px 14px;">
        🟢 Low Risk ({{ $lowCount }})
    </a>
    <a href="{{ route('kader.monitoring.ibu-hamil', ['risk' => 'belum_diperiksa']) }}" class="status-pill {{ ($riskFilter ?? '') === 'belum_diperiksa' ? 'secondary' : 'secondary' }}" style="text-decoration:none; font-size:13px; padding:6px 14px;">
        ⚪ Belum Diperiksa ({{ $belumDiperiksaCount }})
    </a>
</div>

{{-- TABLE MONITORING IBU HAMIL --}}
<div class="dashboard-card">
    <div class="card-header">
        <div>
            <span class="card-eyebrow">DATA PEMANTAUAN KESEHATAN MATERNAL</span>
            <h3>Tabel Monitoring Ibu Hamil</h3>
        </div>
        <span class="badge secondary">{{ count($items) }} Terdaftar</span>
    </div>

    <div class="table-responsive" style="margin-top: 16px;">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Nama Ibu Hamil</th>
                    <th>Usia Kehamilan</th>
                    <th>Pemeriksaan Terakhir</th>
                    <th>Klinis (TD / Gula)</th>
                    <th>AI Screening</th>
                    <th>Tren Maternal</th>
                    <th>Status Risiko</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($items as $row)
                    <tr>
                        <td>
                            <strong>{{ $row['ibu_hamil']->nama }}</strong>
                            <div style="font-size: 11px; color: #64748b;">NIK: {{ $row['ibu_hamil']->nik }}</div>
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
                                <span class="status-pill danger">🔴 HIGH</span>
                            @elseif($row['risk_level'] === 'medium')
                                <span class="status-pill warning">🟡 MEDIUM</span>
                            @elseif($row['risk_level'] === 'low')
                                <span class="status-pill success">🟢 LOW</span>
                            @else
                                <span class="status-pill secondary">⚪ Belum Diperiksa</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('kader.ai-detail.ibu-hamil', $row['ibu_hamil']->id) }}" class="button secondary small" style="font-size: 11.5px; padding: 4px 10px;">
                                Detail Pasien →
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center" style="padding: 24px; color: #64748b;">
                            Tidak ada data ibu hamil dalam kategori ini.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div style="margin-top: 16px; padding: 12px; border-radius: 10px; background: #fff; border: 1px solid #e2e8f0; font-size: 12px; color: #64748b; display: flex; align-items: center; gap: 8px;">
    <span>⚠️</span>
    <span><strong>Catatan Penting:</strong> Hasil AI Maternal Risk merupakan early warning dan bukan diagnosis medis. Konsultasi dan pemeriksaan klinis dilakukan oleh Tenaga Kesehatan / Bidan Desa.</span>
</div>

@endsection
