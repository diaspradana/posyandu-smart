@extends('layouts.app')

@section('title', 'Dashboard Kader')
@section('page-title', 'Dashboard Kader')

@section('content')

{{-- 1. PAGE HEADER --}}
<div class="page-heading">
    <div>
        <span class="eyebrow">POSYANDU OPERASIONAL & TINDAK LANJUT</span>
        <h1>Selamat Datang, {{ $user->name }} 👋</h1>
        <p>Wilayah Penugasan: <strong>📍 {{ $tapos->nama }}</strong> | Fokus pada tindak lanjut & intervensi dini pasien.</p>
    </div>

    <div style="display: flex; gap: 8px;">
        <a href="{{ route('kader.kegiatan.pemeriksaan') }}" class="button primary" style="padding: 9px 18px;">
            <span>🩺</span>
            <span>Mulai Pemeriksaan Hari Ini</span>
        </a>
    </div>
</div>

{{-- 2. TAMPILAN UTAMA STATISTIK KADER (ITEM 11 BLUEPRINT) --}}
<div class="stats-grid" style="grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; margin-bottom: 24px;">
    <div class="stat-card">
        <div class="stat-card-top">
            <div class="stat-icon blue">👶</div>
            <span class="stat-trend positive">Warga Binaan</span>
        </div>
        <span class="stat-label">Balita</span>
        <strong class="stat-number">{{ $balitaCount }}</strong>
        <span class="stat-description">Sasaran di {{ $tapos->nama }}</span>
    </div>

    <div class="stat-card">
        <div class="stat-card-top">
            <div class="stat-icon pink">🤰</div>
            <span class="stat-trend positive">Warga Binaan</span>
        </div>
        <span class="stat-label">Ibu Hamil</span>
        <strong class="stat-number" style="color: #db2777;">{{ $ibuHamilCount }}</strong>
        <span class="stat-description">Sasaran terpantau</span>
    </div>

    <div class="stat-card" style="border-left: 4px solid #ef4444;">
        <div class="stat-card-top">
            <div class="stat-icon red" style="background: #fee2e2; color: #dc2626;">🔴</div>
            <span class="stat-trend" style="background: #fee2e2; color: #dc2626;">Screening AI</span>
        </div>
        <span class="stat-label">🔴 Risiko Stunting</span>
        <strong class="stat-number" style="color: #dc2626;">{{ $risikoStuntingCount }}</strong>
        <span class="stat-description">Balita terindikasi risiko</span>
    </div>

    <div class="stat-card" style="border-left: 4px solid #f59e0b;">
        <div class="stat-card-top">
            <div class="stat-icon orange" style="background: #fef3c7; color: #d97706;">⚠️</div>
            <span class="stat-trend" style="background: #fef3c7; color: #d97706;">Action Needed</span>
        </div>
        <span class="stat-label">⚠️ Perlu Follow Up</span>
        <strong class="stat-number" style="color: #d97706;">{{ $totalFollowUp }}</strong>
        <span class="stat-description">Total sasaran intervensi</span>
    </div>
</div>

{{-- 3. KARTU ALERT KADER (ITEM 12 BLUEPRINT) --}}
<div class="dashboard-card" style="background: linear-gradient(135deg, #fffbeb 0%, #fef2f2 100%); border: 1px solid #fde68a; margin-bottom: 24px; padding: 20px; border-radius: 16px;">
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px; margin-bottom: 14px;">
        <div style="display: flex; align-items: center; gap: 10px;">
            <span style="font-size: 24px;">⚠️</span>
            <div>
                <strong style="color: #92400e; font-size: 16px; letter-spacing: 0.5px; text-transform: uppercase;">
                    PERLU TINDAK LANJUT SEGERA
                </strong>
                <div style="font-size: 12.5px; color: #b45309;">Prioritas intervensi & pendampingan kader Posyandu {{ $tapos->nama }}</div>
            </div>
        </div>

        <a href="{{ route('kader.monitoring.balita') }}" class="button primary small" style="background: #b45309; border: none; font-size: 12px;">
            Lihat Semua Pasien →
        </a>
    </div>

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 12px;">
        <a href="{{ route('kader.monitoring.balita', ['status' => 'risiko_stunting']) }}" style="background: white; border: 1px solid #fecaca; border-radius: 10px; padding: 12px 16px; display: flex; align-items: center; justify-content: space-between; text-decoration: none;">
            <div style="display: flex; align-items: center; gap: 10px;">
                <span style="font-size: 18px;">🔴</span>
                <span style="color: #991b1b; font-size: 13.5px; font-weight: 600;">Balita Berisiko</span>
            </div>
            <strong style="color: #dc2626; font-size: 16px;">{{ $risikoStuntingCount }}</strong>
        </a>

        <a href="{{ route('kader.monitoring.balita', ['status' => 'pemantauan']) }}" style="background: white; border: 1px solid #fed7aa; border-radius: 10px; padding: 12px 16px; display: flex; align-items: center; justify-content: space-between; text-decoration: none;">
            <div style="display: flex; align-items: center; gap: 10px;">
                <span style="font-size: 18px;">🟡</span>
                <span style="color: #9a3412; font-size: 13.5px; font-weight: 600;">Balita Pemantauan</span>
            </div>
            <strong style="color: #ea580c; font-size: 16px;">{{ $pemantauanBalitaCount }}</strong>
        </a>

        <a href="{{ route('kader.monitoring.ibu-hamil', ['risk' => 'high']) }}" style="background: white; border: 1px solid #fecaca; border-radius: 10px; padding: 12px 16px; display: flex; align-items: center; justify-content: space-between; text-decoration: none;">
            <div style="display: flex; align-items: center; gap: 10px;">
                <span style="font-size: 18px;">🔴</span>
                <span style="color: #991b1b; font-size: 13.5px; font-weight: 600;">Bumil High Risk</span>
            </div>
            <strong style="color: #dc2626; font-size: 16px;">{{ $ibuHamilHighRiskCount }}</strong>
        </a>

        <div style="background: white; border: 1px solid #e2e8f0; border-radius: 10px; padding: 12px 16px; display: flex; align-items: center; justify-content: space-between;">
            <div style="display: flex; align-items: center; gap: 10px;">
                <span style="font-size: 18px;">💉</span>
                <span style="color: #334155; font-size: 13.5px; font-weight: 600;">Imunisasi Tertunda</span>
            </div>
            <strong style="color: #475569; font-size: 16px;">{{ $imunisasiTertundaCount }}</strong>
        </div>
    </div>
</div>

{{-- 4. JADWAL TERDEKAT & GRAFIK KEHADIRAN (ITEMS 15 & 16 BLUEPRINT) --}}
<div class="dashboard-grid" style="grid-template-columns: 1fr 1fr; gap: 24px;">

    {{-- JADWAL TERDEKAT CARD --}}
    <div class="dashboard-card">
        <div class="card-header">
            <div>
                <span class="card-eyebrow">JADWAL KEGIATAN TERDEKAT</span>
                <h3>Posyandu Mendatang</h3>
            </div>
            <a href="{{ route('kader.kegiatan.jadwal') }}" style="font-size: 12px; color: #2563eb; font-weight: 600; text-decoration: none;">Lihat Semua Jadwal →</a>
        </div>

        <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 16px; margin-top: 14px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                <strong style="font-size: 15px; color: #0f172a;">{{ $kegiatanTerdekat->nama_kegiatan ?? 'Posyandu Balita & Imunisasi' }}</strong>
                <span class="badge" style="background: #2563eb; color: white; font-size: 11px;">Mendatang</span>
            </div>

            <div style="display: flex; flex-direction: column; gap: 6px; font-size: 13px; color: #475569; margin: 10px 0;">
                <div>📅 <strong>Tanggal:</strong> {{ \Carbon\Carbon::parse($kegiatanTerdekat->tanggal)->isoFormat('dddd, D MMMM Y') }}</div>
                <div>⏰ <strong>Waktu:</strong> {{ substr($kegiatanTerdekat->waktu_mulai ?? '08:00', 0, 5) }} – {{ substr($kegiatanTerdekat->waktu_selesai ?? '11:00', 0, 5) }} WIB</div>
                <div>📍 <strong>Lokasi:</strong> {{ $tapos->nama }}</div>
            </div>

            <div style="margin-top: 14px; display: flex; gap: 8px;">
                <a href="{{ route('kader.kegiatan.pemeriksaan') }}" class="button primary small" style="width: 100%; justify-content: center; display: flex;">
                    🩺 Buka Form Pemeriksaan
                </a>
            </div>
        </div>
    </div>

    {{-- GRAFIK KEHADIRAN (ITEM 15 BLUEPRINT) --}}
    <div class="dashboard-card">
        <div class="card-header">
            <div>
                <span class="card-eyebrow">MONITORING PARTISIPASI</span>
                <h3>Kehadiran Posyandu Bulanan</h3>
            </div>
            <span class="badge success">Target >85%</span>
        </div>

        <p style="font-size: 12.5px; color: #64748b; margin: 8px 0 16px 0;">
            Menampilkan tingkat kehadiran warga binaan pada setiap pertemuan dalam satu bulan:
        </p>

        {{-- Bar Chart Visual --}}
        <div style="display: flex; align-items: flex-end; justify-content: space-around; height: 140px; border-bottom: 2px solid #cbd5e1; padding-bottom: 8px; margin-top: 10px;">
            <div style="display: flex; flex-direction: column; align-items: center; gap: 6px;">
                <span style="font-size: 11px; font-weight: 700; color: #2563eb;">78%</span>
                <div style="width: 38px; height: 80px; background: #93c5fd; border-radius: 6px 6px 0 0;"></div>
                <span style="font-size: 12px; font-weight: 600; color: #475569;">P1</span>
            </div>

            <div style="display: flex; flex-direction: column; align-items: center; gap: 6px;">
                <span style="font-size: 11px; font-weight: 700; color: #2563eb;">86%</span>
                <div style="width: 38px; height: 95px; background: #60a5fa; border-radius: 6px 6px 0 0;"></div>
                <span style="font-size: 12px; font-weight: 600; color: #475569;">P2</span>
            </div>

            <div style="display: flex; flex-direction: column; align-items: center; gap: 6px;">
                <span style="font-size: 11px; font-weight: 700; color: #2563eb;">92%</span>
                <div style="width: 38px; height: 110px; background: #3b82f6; border-radius: 6px 6px 0 0;"></div>
                <span style="font-size: 12px; font-weight: 600; color: #475569;">P3</span>
            </div>

            <div style="display: flex; flex-direction: column; align-items: center; gap: 6px;">
                <span style="font-size: 11px; font-weight: 700; color: #2563eb;">89%</span>
                <div style="width: 38px; height: 102px; background: #2563eb; border-radius: 6px 6px 0 0;"></div>
                <span style="font-size: 12px; font-weight: 600; color: #475569;">P4</span>
            </div>
        </div>

        <div style="margin-top: 12px; display: flex; justify-content: space-between; font-size: 12px; color: #64748b;">
            <span>Rata-rata Kehadiran: <strong style="color: #16a34a;">86.2%</strong></span>
            <a href="{{ route('kader.kegiatan.kehadiran') }}" style="color: #2563eb; text-decoration: none; font-weight: 600;">Detail Kehadiran →</a>
        </div>
    </div>

</div>

@endsection
