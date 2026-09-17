@extends('layouts.app')

@section('title', 'Monitoring Balita')
@section('page-title', 'Monitoring Balita')

@section('content')

<div class="page-heading">
    <div>
        <span class="eyebrow">SCREENING TUMBUH KEMBANG BALITA</span>
        <h1>Monitoring Balita Posyandu {{ $tapos->nama }} 👶</h1>
        <p>
            Daftar pemantauan pertumbuhan balita, hasil screening AI early warning stunting, dan riwayat perkembangan intervensi.
        </p>
    </div>
    <div class="date-card">
        <span>Sasaran Tapos</span>
        <strong>{{ count($items) }} Balita</strong>
    </div>
</div>

{{-- STATUS FILTER BUTTONS (ITEM 13 BLUEPRINT) --}}
<div style="display: flex; gap: 8px; flex-wrap: wrap; margin-bottom: 20px;">
    <a href="{{ route('kader.monitoring.balita', ['status' => 'semua']) }}" class="status-pill {{ ($statusFilter ?? 'semua') === 'semua' ? 'info' : 'secondary' }}" style="text-decoration:none; font-size:13px; padding:6px 14px;">
        Semua ({{ count($items) }})
    </a>
    <a href="{{ route('kader.monitoring.balita', ['status' => 'normal']) }}" class="status-pill {{ ($statusFilter ?? '') === 'normal' ? 'success' : 'secondary' }}" style="text-decoration:none; font-size:13px; padding:6px 14px;">
        🟢 Normal ({{ $normalCount }})
    </a>
    <a href="{{ route('kader.monitoring.balita', ['status' => 'pemantauan']) }}" class="status-pill {{ ($statusFilter ?? '') === 'pemantauan' ? 'warning' : 'secondary' }}" style="text-decoration:none; font-size:13px; padding:6px 14px;">
        🟡 Pemantauan ({{ $pemantauanCount }})
    </a>
    <a href="{{ route('kader.monitoring.balita', ['status' => 'risiko_stunting']) }}" class="status-pill {{ ($statusFilter ?? '') === 'risiko_stunting' ? 'danger' : 'secondary' }}" style="text-decoration:none; font-size:13px; padding:6px 14px;">
        🔴 Risiko Stunting ({{ $risikoCount }})
    </a>
    <a href="{{ route('kader.monitoring.balita', ['status' => 'belum_diperiksa']) }}" class="status-pill {{ ($statusFilter ?? '') === 'belum_diperiksa' ? 'secondary' : 'secondary' }}" style="text-decoration:none; font-size:13px; padding:6px 14px;">
        ⚪ Belum Diperiksa ({{ $belumDiperiksaCount }})
    </a>
</div>

{{-- TABLE MONITORING BALITA (ITEM 13 BLUEPRINT) --}}
<div class="dashboard-card">
    <div class="card-header">
        <div>
            <span class="card-eyebrow">DATA PEMANTAUAN TUMBUH KEMBANG</span>
            <h3>Tabel Monitoring Balita</h3>
        </div>
        <span class="badge secondary">{{ count($items) }} Terdaftar</span>
    </div>

    <div class="table-responsive" style="margin-top: 16px;">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Balita</th>
                    <th>Umur / JK</th>
                    <th>Pemeriksaan Terakhir</th>
                    <th>Antropometri (BB/TB)</th>
                    <th>AI Screening</th>
                    <th>Tren Longitudinal</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($items as $row)
                    <tr>
                        <td>
                            <strong>{{ $row['balita']->nama }}</strong>
                            <div style="font-size: 11px; color: #64748b;">NIK: {{ $row['balita']->nik }}</div>
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
                                <span class="status-pill danger">🔴 Risiko</span>
                            @elseif($row['status'] === 'pemantauan')
                                <span class="status-pill warning">🟡 Pemantauan</span>
                            @elseif($row['status'] === 'normal')
                                <span class="status-pill success">🟢 Normal</span>
                            @else
                                <span class="status-pill secondary">⚪ Belum Diperiksa</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('kader.ai-detail.balita', $row['balita']->id) }}" class="button secondary small" style="font-size: 11.5px; padding: 4px 10px;">
                                Detail Pasien →
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center" style="padding: 24px; color: #64748b;">
                            Tidak ada data balita dalam kategori ini.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div style="margin-top: 16px; padding: 12px; border-radius: 10px; background: #fff; border: 1px solid #e2e8f0; font-size: 12px; color: #64748b; display: flex; align-items: center; gap: 8px;">
    <span>⚠️</span>
    <span><strong>Catatan Penting:</strong> Hasil AI merupakan screening awal tumbuh kembang dan bukan diagnosis stunting klinis.</span>
</div>

@endsection
