@extends('layouts.app')

@section('title', 'Detail Longitudinal Ibu Hamil - ' . $ibuHamil->nama)
@section('page-title', 'Detail Monitoring Ibu Hamil')

@section('content')

{{-- PAGE HEADER --}}
<div class="page-heading">
    <div>
        <span class="eyebrow">MATERNAL HEALTH MONITORING & AI SCREENING</span>
        <h1>🤰 Profil & Tren Kesehatan: {{ $ibuHamil->nama }}</h1>
        <p>NIK: <strong>{{ $ibuHamil->nik }}</strong> | Usia Kehamilan: <strong>{{ $ibuHamil->usia_kehamilan_minggu ?? '-' }} Minggu</strong> | Tapos: <strong>{{ $tapos->nama }}</strong></p>
    </div>

    <div style="display: flex; gap: 8px;">
        <a href="{{ route('kader.kegiatan.pemeriksaan', ['tab' => 'ibu_hamil', 'ibu_hamil_id' => $ibuHamil->id]) }}" class="button primary" style="background: #db2777; border: none;">
            <span>🩺</span>
            <span>Catat Pemeriksaan Baru</span>
        </a>
        <a href="{{ route('kader.monitoring.ibu-hamil') }}" class="button secondary">
            ← Kembali ke Monitoring
        </a>
    </div>
</div>

{{-- 1. AI SCREENING HERO BANNER --}}
<div class="ai-risk-banner {{ $isHighRisk ? 'high' : ($isMediumRisk ? 'medium' : 'low') }}" style="border-radius: 16px; padding: 24px; margin-bottom: 24px;">
    <div>
        <div style="font-size: 11.5px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 4px;">
            HASIL SCREENING AWAL MATERNAL AI (DECISION SUPPORT)
        </div>
        <div style="font-size: 24px; font-weight: 800;">
            @if($isHighRisk)
                🔴 TINGKAT RISIKO TINGGI (HIGH RISK)
            @elseif($isMediumRisk)
                🟡 MEMERLUKAN PEMANTAUAN (MEDIUM RISK)
            @else
                🟢 KONDISI TERPANTAU BAIK (LOW RISK)
            @endif
        </div>
        <div style="font-size: 13px; margin-top: 6px; opacity: 0.95;">
            Estimasi keyakinan model screening: <strong>{{ $aiScore }}%</strong> (Model: Maternal AI v1.0)
        </div>
    </div>

    <div style="text-align: right;">
        <div style="font-size: 11px; font-weight: 600; text-transform: uppercase; opacity: 0.9;">Arah Tren Maternal</div>
        <div style="margin-top: 6px;">
            {!! $trendData['trend_badge'] !!}
        </div>
    </div>
</div>

{{-- 2. TREN LONGITUDINAL MONITORING CARD --}}
<div class="dashboard-card" style="margin-bottom: 24px; background: linear-gradient(135deg, #ffffff 0%, #fff5f8 100%);">
    <div class="card-header">
        <div>
            <span class="card-eyebrow" style="color: #db2777; font-weight: 800;">NILAI TAMBAH: LONGITUDINAL MONITORING</span>
            <h3>Riwayat & Evaluasi Tren Kesehatan Kehamilan</h3>
        </div>
        <span class="badge secondary">{{ count($history) }} Kali Pemeriksaan</span>
    </div>

    <div style="background: white; border: 1px solid #e2e8f0; border-radius: 12px; padding: 18px; margin-top: 14px;">
        <span style="font-size: 12px; color: #64748b; font-weight: 600; display: block; margin-bottom: 8px;">
            Alur Perkembangan Tingkat Risiko Maternal Pasien:
        </span>
        
        <div style="display: flex; align-items: center; gap: 12px; flex-wrap: wrap; font-size: 16px; margin: 12px 0;">
            {!! $trendData['sequence_html'] !!}
        </div>

        <div style="background: #fdf2f8; border: 1px solid #fbcfe8; border-radius: 8px; padding: 10px 14px; font-size: 13px; color: #831843; margin-top: 12px;">
            📊 <strong>Analisis Sistem:</strong> {{ $trendData['trend_label'] }}. Pemantauan berkelanjutan mendukung deteksi dini tanda bahaya kehamilan.
        </div>
    </div>
</div>

<div class="dashboard-grid" style="grid-template-columns: 1fr 1fr; gap: 24px; margin-bottom: 24px;">

    {{-- 3. INDIKATOR KLINIS & REKOMENDASI --}}
    <div class="dashboard-card">
        <div class="card-header">
            <div>
                <span class="card-eyebrow">SCREENING DETAIL</span>
                <h3>Indikator Klinis Terakhir</h3>
            </div>
        </div>

        <ul class="ai-factor-list" style="margin-top: 14px;">
            <li>
                <span style="color: #db2777;">•</span>
                <div>
                    <strong>Tekanan Darah Terakhir:</strong>
                    <div style="color: #475569; font-size: 12.5px;">{{ $latest ? ($latest->systolic_bp ? "{$latest->systolic_bp}/{$latest->diastolic_bp} mmHg" : $latest->tekanan_darah) : '-' }}</div>
                </div>
            </li>
            <li>
                <span style="color: #db2777;">•</span>
                <div>
                    <strong>Kadar Gula Darah:</strong>
                    <div style="color: #475569; font-size: 12.5px;">{{ $latest && $latest->blood_sugar ? $latest->blood_sugar . ' mmol/L' : '7.0 mmol/L' }}</div>
                </div>
            </li>
            <li>
                <span style="color: #db2777;">•</span>
                <div>
                    <strong>Denyut Jantung & Suhu:</strong>
                    <div style="color: #475569; font-size: 12.5px;">{{ $latest && $latest->heart_rate ? $latest->heart_rate . ' bpm' : '75 bpm' }} | {{ $latest && $latest->body_temp ? $latest->body_temp . ' °F' : '98.6 °F' }}</div>
                </div>
            </li>
        </ul>

        <div style="background: #fffbeb; border: 1px solid #fde68a; border-radius: 10px; padding: 12px; margin-top: 16px;">
            <strong style="font-size: 12.5px; color: #92400e; display: block; margin-bottom: 4px;">💡 Saran Tindak Lanjut Sistem:</strong>
            <span style="font-size: 12.5px; color: #b45309;">
                @if($isHighRisk)
                    Segera lakukan rujukan dan konsultasi ke Bidan Desa / Puskesmas untuk pemeriksaan laboratorium lanjutan dan pemantauan risiko preeklamsia/hipertensi gestasional.
                @elseif($isMediumRisk)
                    Jadwalkan pemantauan berkala tiap 2 minggu, edukasi nutrisi bergizi seimbang, dan pastikan kepatuhan minum Tablet Tambah Darah (TTD).
                @else
                    Pertahankan pola hidup sehat, istirahat cukup, dan jadwalkan pemeriksaan antenatal care (ANC) rutin bulan berikutnya.
                @endif
            </span>
        </div>
    </div>

    {{-- 4. PROFIL IDENTITAS PASIEN --}}
    <div class="dashboard-card">
        <div class="card-header">
            <div>
                <span class="card-eyebrow">INFORMASI SASARAN</span>
                <h3>Profil Identitas Ibu Hamil</h3>
            </div>
        </div>

        <div style="display: flex; flex-direction: column; gap: 10px; font-size: 13px; color: #334155; margin-top: 14px;">
            <div style="display: flex; justify-content: space-between; border-bottom: 1px solid #f1f5f9; padding-bottom: 6px;">
                <span style="color: #64748b;">Nama Lengkap:</span>
                <strong>{{ $ibuHamil->nama }}</strong>
            </div>
            <div style="display: flex; justify-content: space-between; border-bottom: 1px solid #f1f5f9; padding-bottom: 6px;">
                <span style="color: #64748b;">NIK:</span>
                <strong>{{ $ibuHamil->nik }}</strong>
            </div>
            <div style="display: flex; justify-content: space-between; border-bottom: 1px solid #f1f5f9; padding-bottom: 6px;">
                <span style="color: #64748b;">Tanggal Lahir / Usia:</span>
                <strong>{{ $ibuHamil->tanggal_lahir?->translatedFormat('d F Y') }} ({{ $ibuHamil->tanggal_lahir ? $ibuHamil->tanggal_lahir->diffInYears(now()) . ' Th' : '-' }})</strong>
            </div>
            <div style="display: flex; justify-content: space-between; border-bottom: 1px solid #f1f5f9; padding-bottom: 6px;">
                <span style="color: #64748b;">Kehamilan Ke (Gravida):</span>
                <strong>Ke-{{ $ibuHamil->kehamilan_ke ?? 1 }}</strong>
            </div>
            <div style="display: flex; justify-content: space-between; border-bottom: 1px solid #f1f5f9; padding-bottom: 6px;">
                <span style="color: #64748b;">Hari Pertama Haid Terakhir:</span>
                <strong>{{ $ibuHamil->hari_pertama_haid_terakhir?->translatedFormat('d F Y') ?? '-' }}</strong>
            </div>
            <div style="display: flex; justify-content: space-between;">
                <span style="color: #64748b;">Kontak Telepon:</span>
                <strong>{{ $ibuHamil->no_hp ?? '-' }}</strong>
            </div>
        </div>
    </div>

</div>

{{-- 5. TABEL RIWAYAT LENGKAP --}}
<div class="dashboard-card">
    <div class="card-header">
        <div>
            <span class="card-eyebrow">HISTORI PEMERIKSAAN</span>
            <h3>Catatan Pemeriksaan Maternal</h3>
        </div>
    </div>

    <div class="table-responsive" style="margin-top: 16px;">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Usia Hamil</th>
                    <th>BB (kg)</th>
                    <th>Tekanan Darah</th>
                    <th>Gula Darah</th>
                    <th>Hasil AI</th>
                    <th>Status Validasi</th>
                    <th>Catatan</th>
                </tr>
            </thead>
            <tbody>
                @forelse($history as $h)
                    <tr>
                        <td>{{ $h->tanggal_pemeriksaan->format('d/m/Y') }}</td>
                        <td>{{ $h->usia_kehamilan_minggu }} minggu</td>
                        <td>{{ $h->berat_badan }} kg</td>
                        <td>{{ $h->systolic_bp ? "{$h->systolic_bp}/{$h->diastolic_bp} mmHg" : $h->tekanan_darah }}</td>
                        <td>{{ $h->blood_sugar ? $h->blood_sugar . ' mmol/L' : '-' }}</td>
                        <td>{!! $h->risk_badge !!}</td>
                        <td>{!! $h->status_validasi_badge !!}</td>
                        <td>{{ $h->catatan ?? '-' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center" style="padding: 20px; color: #64748b;">Belum ada riwayat pemeriksaan.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div style="margin-top: 16px; padding: 12px; border-radius: 10px; background: #fff1f2; border: 1px solid #fecdd3; font-size: 12px; color: #9f1239; display: flex; align-items: center; gap: 8px;">
    <span>⚠️</span>
    <span><strong>Disclaimer Medis:</strong> Hasil screening Maternal AI merupakan sistem pendukung keputusan (decision support) dan bukan diagnosis medis. Diagnosis klinis kehamilan tetap ditegakkan oleh dokter / bidan Puskesmas.</span>
</div>

@endsection
