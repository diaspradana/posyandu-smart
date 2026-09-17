@extends('layouts.app')

@section('title', 'Dashboard Wilayah Puskesmas')
@section('page-title', 'Dashboard Wilayah Puskesmas')

@section('content')

{{-- 1. PAGE HEADER --}}
<div class="page-heading">
    <div>
        <span class="eyebrow">
            PUSKESMAS SUPERVISION & MONITORING WILAYAH
        </span>
        <h1>
            Dashboard Puskesmas {{ auth()->user()->puskesmas->nama ?? 'Sehat Sejahtera' }} 🏥
        </h1>
        <p>
            Supervisi wilayah kerja terpadu: Early warning risiko maternal & stunting, validasi data nakes, dan pemantauan Tapos berkelanjutan.
        </p>
    </div>

    <div class="date-card">
        <span>Hari ini</span>
        <strong>{{ now()->translatedFormat('l, d F Y') }}</strong>
    </div>
</div>

{{-- 2. STATS GRID --}}
<div class="stats-grid" style="grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 16px; margin-bottom: 24px;">
    <div class="stat-card">
        <div class="stat-card-top">
            <div class="stat-icon blue">🏠</div>
            <span class="stat-trend positive">Wilayah</span>
        </div>
        <span class="stat-label">Total Tapos</span>
        <strong class="stat-number">{{ $totalTapos }}</strong>
        <span class="stat-description">Tapos binaan aktif</span>
    </div>

    <div class="stat-card">
        <div class="stat-card-top">
            <div class="stat-icon purple">👶</div>
            <span class="stat-trend positive">Total</span>
        </div>
        <span class="stat-label">Total Balita</span>
        <strong class="stat-number">{{ $totalBalita }}</strong>
        <span class="stat-description">Balita terdaftar</span>
    </div>

    <div class="stat-card">
        <div class="stat-card-top">
            <div class="stat-icon pink">🤰</div>
            <span class="stat-trend positive">Total</span>
        </div>
        <span class="stat-label">Total Ibu Hamil</span>
        <strong class="stat-number">{{ $totalIbuHamil }}</strong>
        <span class="stat-description">Ibu hamil terpantau</span>
    </div>

    <div class="stat-card" style="border-left: 4px solid #ef4444;">
        <div class="stat-card-top">
            <div class="stat-icon red" style="background:#fee2e2; color:#dc2626;">⚠️</div>
            <span class="stat-trend" style="background:#fee2e2; color:#dc2626;">AI Screening</span>
        </div>
        <span class="stat-label">Risiko Balita</span>
        <strong class="stat-number" style="color:#dc2626;">{{ $balitaStuntingCount }}</strong>
        <span class="stat-description">{{ $balitaPemantauanCount }} butuh pemantauan</span>
    </div>

    <div class="stat-card" style="border-left: 4px solid #f97316;">
        <div class="stat-card-top">
            <div class="stat-icon orange" style="background:#ffedd5; color:#ea580c;">🩺</div>
            <span class="stat-trend" style="background:#ffedd5; color:#ea580c;">Maternal</span>
        </div>
        <span class="stat-label">Risiko Ibu Hamil</span>
        <strong class="stat-number" style="color:#ea580c;">{{ $ibuHamilHighCount }}</strong>
        <span class="stat-description">{{ $ibuHamilMediumCount }} risiko sedang</span>
    </div>

    <div class="stat-card" style="border-left: 4px solid #3b82f6;">
        <div class="stat-card-top">
            <div class="stat-icon blue" style="background:#dbeafe; color:#2563eb;">🛡️</div>
            <span class="stat-trend {{ $pendingValidationCount > 0 ? 'negative' : 'positive' }}">{{ $pendingValidationCount > 0 ? 'Perlu Review' : 'Up to Date' }}</span>
        </div>
        <span class="stat-label">Menunggu Validasi</span>
        <strong class="stat-number" style="color:#2563eb;">{{ $pendingValidationCount }}</strong>
        <span class="stat-description">Pemeriksaan dari Kader</span>
    </div>
</div>

{{-- 3. AI EARLY WARNING WILAYAH BANNER --}}
@if(count($taposRisikoAlerts) > 0)
<div class="dashboard-card" style="background: linear-gradient(135deg, #fff1f2 0%, #fff7ed 100%); border: 1px solid #fed7aa; margin-bottom: 24px; padding: 20px; border-radius: 16px;">
    <div style="display: flex; align-items: flex-start; gap: 16px;">
        <div style="font-size: 32px; line-height: 1;">⚠️</div>
        <div style="flex: 1;">
            <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 8px;">
                <strong style="color: #9a3412; font-size: 16px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px;">
                    AI EARLY WARNING SYSTEM — MONITORING WILAYAH
                </strong>
                <span class="badge" style="background: #ea580c; color: white; border-radius: 20px; padding: 3px 10px; font-size: 11px; font-weight: 700;">DECISION SUPPORT</span>
            </div>
            <p style="color: #7c2d12; margin: 8px 0 12px 0; font-size: 14px; line-height: 1.5;">
                Berdasarkan hasil screening awal AI, terdapat beberapa Tapos yang memerlukan perhatian khusus dan supervisi tenaga kesehatan:
            </p>
            <div style="display: flex; flex-direction: column; gap: 8px;">
                @foreach($taposRisikoAlerts as $alert)
                    <div style="background: rgba(255,255,255,0.8); border: 1px solid #fed7aa; border-radius: 10px; padding: 10px 14px; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 10px;">
                        <span style="color: #431407; font-size: 13.5px; font-weight: 600;">
                            🔴 <strong>{{ $alert['nama'] }}</strong> teridentifikasi memiliki <strong>{{ $alert['stunting_count'] }} balita berisiko</strong> dan <strong>{{ $alert['high_risk_bumil'] }} ibu hamil risiko tinggi</strong>.
                        </span>
                        <a href="{{ route('admin.monitoring.balita') }}" class="button primary small" style="background: #ea580c; border: none; font-size: 12px;">
                            Lihat Monitoring Wilayah →
                        </a>
                    </div>
                @endforeach
            </div>
            <div style="margin-top: 12px; font-size: 11.5px; color: #9a3412; display: flex; align-items: center; gap: 6px;">
                <span>ℹ️</span>
                <em>Catatan: Hasil AI merupakan early warning screening awal dan bukan diagnosis medis klinis.</em>
            </div>
        </div>
    </div>
</div>
@endif

{{-- 4. HIGHLIGHT MONITORING TAPOS (🟢 / 🟡 / 🔴) --}}
<div class="dashboard-grid" style="grid-template-columns: 2fr 1fr; gap: 24px; margin-bottom: 24px;">
    
    {{-- TAPOS INTELLIGENCE TABLE --}}
    <div class="dashboard-card">
        <div class="card-header">
            <div>
                <span class="card-eyebrow">DISTRIBUSI STATUS WILAYAH</span>
                <h3>Monitoring Tapos & Indikator Risiko</h3>
            </div>
            <span class="badge secondary">{{ count($monitoringTapos) }} Tapos</span>
        </div>

        <div class="table-responsive" style="margin-top: 16px;">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Tapos</th>
                        <th>Balita</th>
                        <th>Ibu Hamil</th>
                        <th>Risiko Balita</th>
                        <th>Risiko Bumil</th>
                        <th>Kehadiran</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($monitoringTapos as $t)
                        <tr>
                            <td>
                                <strong>{{ $t['nama'] }}</strong>
                                <div style="font-size: 11px; color: #64748b;">{{ $t['kode'] }}</div>
                            </td>
                            <td>{{ $t['balita'] }}</td>
                            <td>{{ $t['bumil'] }}</td>
                            <td>
                                @if($t['stunting_risk'] > 0)
                                    <span class="status-pill danger" style="font-size: 11px;">🔴 {{ $t['stunting_risk'] }} Risiko</span>
                                @elseif($t['pemantauan'] > 0)
                                    <span class="status-pill warning" style="font-size: 11px;">🟡 {{ $t['pemantauan'] }} Pantau</span>
                                @else
                                    <span class="status-pill success" style="font-size: 11px;">🟢 Aman</span>
                                @endif
                            </td>
                            <td>
                                @if($t['high_risk_bumil'] > 0)
                                    <span class="status-pill danger" style="font-size: 11px;">🔴 {{ $t['high_risk_bumil'] }} High</span>
                                @else
                                    <span class="status-pill success" style="font-size: 11px;">🟢 Normal</span>
                                @endif
                            </td>
                            <td>
                                <div style="display: flex; align-items: center; gap: 6px;">
                                    <div style="flex: 1; height: 6px; background: #e2e8f0; border-radius: 3px; overflow: hidden;">
                                        <div style="width: {{ $t['kehadiran'] }}%; height: 100%; background: {{ $t['kehadiran'] >= 80 ? '#10b981' : '#f59e0b' }};"></div>
                                    </div>
                                    <span style="font-size: 11.5px; font-weight: 600;">{{ $t['kehadiran'] }}%</span>
                                </div>
                            </td>
                            <td>
                                @if($t['status_badge'] === 'red')
                                    <span class="status-pill danger">🔴 Perhatian</span>
                                @elseif($t['status_badge'] === 'yellow')
                                    <span class="status-pill warning">🟡 Pemantauan</span>
                                @else
                                    <span class="status-pill success">🟢 Baik</span>
                                @endif
                            </td>
                            <td>
                                <button type="button" class="button secondary small" onclick="showTaposDetail({{ json_encode($t) }})" style="font-size: 11px; padding: 4px 8px;">
                                    Detail
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center">Belum ada data Tapos.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- CONTINUUM OF CARE CARD --}}
    <div class="dashboard-card" style="display: flex; flex-direction: column; justify-content: space-between;">
        <div>
            <div class="card-header">
                <div>
                    <span class="card-eyebrow">KERANGKA KERJA MEDIS</span>
                    <h3>Continuum of Care</h3>
                </div>
            </div>

            <p style="font-size: 13px; color: #64748b; margin: 12px 0 16px 0; line-height: 1.5;">
                Pemantauan kesehatan berkelanjutan dari masa kehamilan hingga tumbuh kembang balita 0-59 bulan.
            </p>

            <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 16px; display: flex; flex-direction: column; gap: 12px;">
                <div style="display: flex; align-items: center; gap: 12px;">
                    <div style="width: 32px; height: 32px; border-radius: 8px; background: #fce7f3; color: #db2777; display: flex; align-items: center; justify-content: center; font-size: 16px; font-weight: 700;">1</div>
                    <div>
                        <strong style="font-size: 13px; color: #0f172a;">Masa Kehamilan (Maternal AI)</strong>
                        <div style="font-size: 11.5px; color: #64748b;">Screening risiko TD, Gula Darah, Suhu tubuh</div>
                    </div>
                </div>

                <div style="text-align: center; color: #94a3b8; font-size: 12px; line-height: 1;">↓ Early Warning & Pencegahan</div>

                <div style="display: flex; align-items: center; gap: 12px;">
                    <div style="width: 32px; height: 32px; border-radius: 8px; background: #e0e7ff; color: #4338ca; display: flex; align-items: center; justify-content: center; font-size: 16px; font-weight: 700;">2</div>
                    <div>
                        <strong style="font-size: 13px; color: #0f172a;">Kelahiran & Tumbuh Kembang (Balita AI)</strong>
                        <div style="font-size: 11.5px; color: #64748b;">Screening pertumbuhan kurva WHO & DHS</div>
                    </div>
                </div>

                <div style="text-align: center; color: #94a3b8; font-size: 12px; line-height: 1;">↓ Evaluasi & Intervensi Gizi</div>

                <div style="display: flex; align-items: center; gap: 12px;">
                    <div style="width: 32px; height: 32px; border-radius: 8px; background: #dcfce7; color: #15803d; display: flex; align-items: center; justify-content: center; font-size: 16px; font-weight: 700;">3</div>
                    <div>
                        <strong style="font-size: 13px; color: #0f172a;">Longitudinal Monitoring & Validasi</strong>
                        <div style="font-size: 11.5px; color: #64748b;">Evaluasi tren per bulan oleh Admin Puskesmas</div>
                    </div>
                </div>
            </div>
        </div>

        <div style="margin-top: 16px; border-top: 1px solid #f1f5f9; padding-top: 14px;">
            <a href="{{ route('admin.validasi.index') }}" class="button primary" style="width: 100%; justify-content: center; display: flex;">
                🛡️ Buka Antrean Validasi Pemeriksaan ({{ $pendingValidationCount }})
            </a>
        </div>
    </div>

</div>

{{-- MODAL DETAIL TAPOS --}}
<div id="taposModal" class="modal-backdrop" style="display: none; position: fixed; inset: 0; background: rgba(15,23,42,0.6); z-index: 9999; align-items: center; justify-content: center;">
    <div style="background: white; border-radius: 16px; width: 90%; max-width: 480px; padding: 24px; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
            <h3 id="modalTaposNama" style="margin: 0; font-size: 18px; color: #0f172a;">Detail Tapos</h3>
            <button type="button" onclick="closeTaposModal()" style="background: none; border: none; font-size: 20px; cursor: pointer; color: #64748b;">×</button>
        </div>
        <div id="modalTaposContent" style="display: flex; flex-direction: column; gap: 12px; font-size: 13.5px;">
            <!-- Dynamic Content -->
        </div>
        <div style="margin-top: 20px; display: flex; justify-content: flex-end;">
            <button type="button" class="button secondary" onclick="closeTaposModal()">Tutup</button>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
function showTaposDetail(data) {
    document.getElementById('modalTaposNama').innerText = data.nama + ' (' + data.kode + ')';
    
    let html = `
        <div style="display:grid; grid-template-columns: 1fr 1fr; gap: 10px;">
            <div style="background:#f8fafc; padding:12px; border-radius:8px;">
                <span style="color:#64748b; font-size:12px;">Total Balita</span>
                <div style="font-size:18px; font-weight:700; color:#0f172a;">${data.balita}</div>
            </div>
            <div style="background:#f8fafc; padding:12px; border-radius:8px;">
                <span style="color:#64748b; font-size:12px;">Total Ibu Hamil</span>
                <div style="font-size:18px; font-weight:700; color:#0f172a;">${data.bumil}</div>
            </div>
            <div style="background:#fee2e2; padding:12px; border-radius:8px;">
                <span style="color:#991b1b; font-size:12px;">🔴 Risiko Stunting</span>
                <div style="font-size:18px; font-weight:700; color:#dc2626;">${data.stunting_risk}</div>
            </div>
            <div style="background:#fef3c7; padding:12px; border-radius:8px;">
                <span style="color:#92400e; font-size:12px;">🟡 Pemantauan</span>
                <div style="font-size:18px; font-weight:700; color:#d97706;">${data.pemantauan}</div>
            </div>
            <div style="background:#fee2e2; padding:12px; border-radius:8px;">
                <span style="color:#991b1b; font-size:12px;">🔴 Ibu Hamil High Risk</span>
                <div style="font-size:18px; font-weight:700; color:#dc2626;">${data.high_risk_bumil}</div>
            </div>
            <div style="background:#f0fdf4; padding:12px; border-radius:8px;">
                <span style="color:#166534; font-size:12px;">Kehadiran Bulan Ini</span>
                <div style="font-size:18px; font-weight:700; color:#16a34a;">${data.kehadiran}%</div>
            </div>
        </div>
        <div style="margin-top:8px; padding:10px; border-radius:8px; background:#eff6ff; color:#1e40af; font-size:12px;">
            ℹ️ Status Wilayah: <strong>${data.status_dot} ${data.status_label}</strong>.
        </div>
    `;
    document.getElementById('modalTaposContent').innerHTML = html;
    document.getElementById('taposModal').style.display = 'flex';
}

function closeTaposModal() {
    document.getElementById('taposModal').style.display = 'none';
}
</script>
@endsection