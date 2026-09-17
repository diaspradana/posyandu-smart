@extends('layouts.app')

@section('title', 'Detail Longitudinal Balita - ' . $balita->nama)
@section('page-title', 'Detail Monitoring Balita')

@section('content')

{{-- PAGE HEADER --}}
<div class="page-heading">
    <div>
        <span class="eyebrow">LONGITUDINAL MONITORING & AI SCREENING</span>
        <h1>👶 Profil & Tren Pertumbuhan: {{ $balita->nama }}</h1>
        <p>NIK: <strong>{{ $balita->nik }}</strong> | Umur/JK: <strong>{{ $balita->umur_jk }}</strong> | Tapos: <strong>{{ $tapos->nama }}</strong></p>
    </div>

    <div style="display: flex; gap: 8px;">
        <a href="{{ route('kader.kegiatan.pemeriksaan', ['tab' => 'balita', 'balita_id' => $balita->id]) }}" class="button primary">
            <span>🩺</span>
            <span>Catat Pemeriksaan Baru</span>
        </a>
        <a href="{{ route('kader.monitoring.balita') }}" class="button secondary">
            ← Kembali ke Monitoring
        </a>
    </div>
</div>

{{-- 1. AI SCREENING HERO BANNER --}}
<div class="ai-risk-banner {{ $isHighRisk ? 'high' : ($isMediumRisk ? 'medium' : 'low') }}" style="border-radius: 16px; padding: 24px; margin-bottom: 24px;">
    <div>
        <div style="font-size: 11.5px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 4px;">
            HASIL SCREENING AWAL AI (DECISION SUPPORT)
        </div>
        <div style="font-size: 24px; font-weight: 800;">
            @if($isHighRisk)
                🔴 RISIKO STUNTING (MEMERLUKAN INTERVENSI)
            @elseif($isMediumRisk)
                🟡 MEMERLUKAN PEMANTAUAN TUMBUH KEMBANG
            @else
                🟢 PERTUMBUHAN NORMAL
            @endif
        </div>
        <div style="font-size: 13px; margin-top: 6px; opacity: 0.95;">
            Estimasi keyakinan model screening: <strong>{{ $aiScore }}%</strong> (Model: Stunting AI v1.0)
        </div>
    </div>

    <div style="text-align: right;">
        <div style="font-size: 11px; font-weight: 600; text-transform: uppercase; opacity: 0.9;">Arah Tren Pasien</div>
        <div style="margin-top: 6px;">
            {!! $trendData['trend_badge'] !!}
        </div>
    </div>
</div>

{{-- 2. TREN LONGITUDINAL MONITORING CARD (ITEM 9 BLUEPRINT) --}}
<div class="dashboard-card" style="margin-bottom: 24px; background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);">
    <div class="card-header">
        <div>
            <span class="card-eyebrow" style="color: #2563eb; font-weight: 800;">NILAI TAMBAH: LONGITUDINAL PROGRESSION</span>
            <h3>Riwayat & Evaluasi Tren Pertumbuhan Anak</h3>
        </div>
        <span class="badge secondary">{{ count($history) }} Kali Pemeriksaan</span>
    </div>

    <div style="background: white; border: 1px solid #e2e8f0; border-radius: 12px; padding: 18px; margin-top: 14px;">
        <span style="font-size: 12px; color: #64748b; font-weight: 600; display: block; margin-bottom: 8px;">
            Alur Perkembangan Status dari Pemeriksaan ke Pemeriksaan:
        </span>
        
        <div style="display: flex; align-items: center; gap: 12px; flex-wrap: wrap; font-size: 16px; margin: 12px 0;">
            {!! $trendData['sequence_html'] !!}
        </div>

        <div style="background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 8px; padding: 10px 14px; font-size: 13px; color: #166534; margin-top: 12px;">
            📊 <strong>Analisis Sistem:</strong> {{ $trendData['trend_label'] }}. Posyandu Smart mendokumentasikan efektivitas intervensi gizi & pemantauan berkelanjutan.
        </div>
    </div>
</div>

<div class="dashboard-grid" style="grid-template-columns: 1fr 1fr; gap: 24px; margin-bottom: 24px;">

    {{-- 3. INDIKATOR & REKOMENDASI --}}
    <div class="dashboard-card">
        <div class="card-header">
            <div>
                <span class="card-eyebrow">SCREENING DETAIL</span>
                <h3>Indikator Pemeriksaan Terakhir</h3>
            </div>
        </div>

        <ul class="ai-factor-list" style="margin-top: 14px;">
            <li>
                <span style="color: #2563eb;">•</span>
                <div>
                    <strong>Tinggi Badan Terakhir:</strong>
                    <div style="color: #475569; font-size: 12.5px;">{{ $latest ? $latest->tinggi_badan . ' cm' : '-' }} (Usia: {{ $balita->usia_bulan }} bulan)</div>
                </div>
            </li>
            <li>
                <span style="color: #2563eb;">•</span>
                <div>
                    <strong>Berat Badan Terakhir:</strong>
                    <div style="color: #475569; font-size: 12.5px;">{{ $latest ? $latest->berat_badan . ' kg' : '-' }}</div>
                </div>
            </li>
            <li>
                <span style="color: #2563eb;">•</span>
                <div>
                    <strong>Status Imunisasi:</strong>
                    <div style="color: #475569; font-size: 12.5px;">{{ $latest ? ucfirst(str_replace('_', ' ', $latest->status_imunisasi)) : 'Belum tercatat' }}</div>
                </div>
            </li>
        </ul>

        <div style="background: #fffbeb; border: 1px solid #fde68a; border-radius: 10px; padding: 12px; margin-top: 16px;">
            <strong style="font-size: 12.5px; color: #92400e; display: block; margin-bottom: 4px;">💡 Saran Tindak Lanjut:</strong>
            <span style="font-size: 12.5px; color: #b45309;">
                @if($isHighRisk)
                    Berikan PMT pemulihan kaya protein hewani (telur/ikan), dampingi konseling gizi PMBA pada orang tua, dan laporkan rujukan ke Puskesmas.
                @elseif($isMediumRisk)
                    Lakukan penimbangan rutin tiap bulan, edukasi variasi MP-ASI bergizi, dan pantau kenaikan BB sesuai kurva KMS.
                @else
                    Pertahankan stimulasi tumbuh kembang aktif dan pola makan gizi seimbang.
                @endif
            </span>
        </div>
    </div>

    {{-- 4. PROFIL IDENTITAS & KELUARGA --}}
    <div class="dashboard-card">
        <div class="card-header">
            <div>
                <span class="card-eyebrow">INFORMASI SASARAN</span>
                <h3>Profil Pasien & Keluarga</h3>
            </div>
        </div>

        <div style="display: flex; flex-direction: column; gap: 10px; font-size: 13px; color: #334155; margin-top: 14px;">
            <div style="display: flex; justify-content: space-between; border-bottom: 1px solid #f1f5f9; padding-bottom: 6px;">
                <span style="color: #64748b;">Nama Lengkap:</span>
                <strong>{{ $balita->nama }}</strong>
            </div>
            <div style="display: flex; justify-content: space-between; border-bottom: 1px solid #f1f5f9; padding-bottom: 6px;">
                <span style="color: #64748b;">NIK Balita:</span>
                <strong>{{ $balita->nik }}</strong>
            </div>
            <div style="display: flex; justify-content: space-between; border-bottom: 1px solid #f1f5f9; padding-bottom: 6px;">
                <span style="color: #64748b;">Tanggal Lahir:</span>
                <strong>{{ $balita->tanggal_lahir?->translatedFormat('d F Y') }}</strong>
            </div>
            <div style="display: flex; justify-content: space-between; border-bottom: 1px solid #f1f5f9; padding-bottom: 6px;">
                <span style="color: #64748b;">Nama Ibu:</span>
                <strong>{{ $balita->nama_ibu ?? '-' }}</strong>
            </div>
            <div style="display: flex; justify-content: space-between; border-bottom: 1px solid #f1f5f9; padding-bottom: 6px;">
                <span style="color: #64748b;">Nama Ayah:</span>
                <strong>{{ $balita->nama_ayah ?? '-' }}</strong>
            </div>
            <div style="display: flex; justify-content: space-between;">
                <span style="color: #64748b;">Kontak Orang Tua:</span>
                <strong>{{ $balita->no_hp_orang_tua ?? '-' }}</strong>
            </div>
        </div>
    </div>

</div>

{{-- 5. TABEL RIWAYAT LENGKAP --}}
<div class="dashboard-card">
    <div class="card-header">
        <div>
            <span class="card-eyebrow">HISTORI PEMERIKSAAN</span>
            <h3>Catatan Pemeriksaan Pasien</h3>
        </div>
    </div>

    <div class="table-responsive" style="margin-top: 16px;">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Usia</th>
                    <th>BB (kg)</th>
                    <th>TB (cm)</th>
                    <th>Hasil AI</th>
                    <th>Status Validasi</th>
                    <th>Catatan Kader / Evaluasi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($history as $h)
                    <tr>
                        <td>{{ $h->tanggal_pemeriksaan->format('d/m/Y') }}</td>
                        <td>{{ $h->umur_bulan ?? $balita->usia_bulan }} bulan</td>
                        <td>{{ $h->berat_badan }} kg</td>
                        <td>{{ $h->tinggi_badan }} cm</td>
                        <td>{!! $h->ai_badge !!}</td>
                        <td>{!! $h->status_validasi_badge !!}</td>
                        <td>{{ $h->catatan ?? '-' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center" style="padding: 20px; color: #64748b;">Belum ada riwayat pemeriksaan.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div style="margin-top: 16px; padding: 12px; border-radius: 10px; background: #eff6ff; border: 1px solid #bfdbfe; font-size: 12px; color: #1e40af; display: flex; align-items: center; gap: 8px;">
    <span>⚠️</span>
    <span><strong>Disclaimer Medis:</strong> Hasil AI pada Posyandu Smart merupakan screening awal & decision support. Diagnosis stunting dan keputusan medis dilakukan oleh dokter / tenaga kesehatan Puskesmas.</span>
</div>

@endsection
