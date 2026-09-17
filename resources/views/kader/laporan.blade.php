@extends('layouts.app')

@section('title', 'Laporan Bulanan Posyandu - Kader')
@section('page-title', 'Laporan Posyandu')

@section('content')

{{-- PAGE HEADER --}}
<div class="page-heading">
    <div>
        <span class="eyebrow">REKAPITULASI PELAYANAN</span>
        <h1>📄 Laporan Kegiatan Posyandu — {{ $tapos->nama }}</h1>
        <p>Generate dan unduh rekapitulasi data pelayanan bulanan untuk diserahkan ke Puskesmas.</p>
    </div>
</div>

{{-- 1. FILTER & GENERATOR FORM --}}
<div class="dashboard-card" style="padding: 24px; margin-bottom: 24px;">
    <h2 style="font-size: 15px; font-weight: 700; margin-bottom: 16px; color: #1e293b;">
        ⚙️ Parameter Laporan
    </h2>

    <form method="GET" action="{{ route('kader.laporan') }}" style="display: flex; gap: 14px; flex-wrap: wrap; align-items: flex-end;">
        <input type="hidden" name="generate" value="1">

        <div style="min-width: 220px; flex: 1;">
            <label style="display: block; font-size: 12px; font-weight: 600; color: #475569; margin-bottom: 6px;">Periode Pelayanan</label>
            <select name="periode" style="width: 100%; height: 38px; font-size: 13px; border-radius: 8px; border: 1px solid #cbd5e1; padding: 0 12px; background: white;">
                <option value="September 2026" @selected($periode === 'September 2026')>September 2026</option>
                <option value="Agustus 2026" @selected($periode === 'Agustus 2026')>Agustus 2026</option>
                <option value="Juli 2026" @selected($periode === 'Juli 2026')>Juli 2026</option>
            </select>
        </div>

        <div style="min-width: 220px; flex: 1;">
            <label style="display: block; font-size: 12px; font-weight: 600; color: #475569; margin-bottom: 6px;">Kategori Sasaran</label>
            <select name="jenis" style="width: 100%; height: 38px; font-size: 13px; border-radius: 8px; border: 1px solid #cbd5e1; padding: 0 12px; background: white;">
                <option value="semua" @selected($jenis === 'semua')>Semua Sasaran (Balita & Ibu Hamil)</option>
                <option value="balita" @selected($jenis === 'balita')>Khusus Balita</option>
                <option value="ibu_hamil" @selected($jenis === 'ibu_hamil')>Khusus Ibu Hamil</option>
            </select>
        </div>

        <button type="submit" class="button primary" style="height: 38px; padding: 0 22px;">
            <span>⚡</span>
            <span>Generate Laporan</span>
        </button>
    </form>
</div>

{{-- 2. REPORT GENERATED RESULT --}}
@if($generated)
    <div style="background: #f0fdf4; border: 1px solid #bbf7d0; border-left: 4px solid #16a34a; border-radius: 12px; padding: 18px 24px; margin-bottom: 24px; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px;">
        <div style="display: flex; align-items: center; gap: 12px;">
            <span style="font-size: 24px;">✓</span>
            <div>
                <strong style="color: #166534; font-size: 14px;">Laporan Berhasil Dibuat!</strong>
                <div style="font-size: 12px; color: #15803d;">Periode: {{ $periode }} | Wilayah: {{ $tapos->nama }}</div>
            </div>
        </div>

        <div style="display: flex; gap: 8px;">
            <button onclick="window.print()" class="btn-primary" style="background: #0284c7; padding: 8px 16px;">
                <span>📥</span>
                <span>Download PDF</span>
            </button>

            <button onclick="alert('Laporan berhasil diexport ke format Excel (.xlsx)')" class="btn-primary" style="background: #16a34a; padding: 8px 16px;">
                <span>📊</span>
                <span>Download Excel</span>
            </button>
        </div>
    </div>

    {{-- REPORT PREVIEW CARD --}}
    <div class="dashboard-card" style="padding: 28px; margin-bottom: 24px;">
        <div style="text-align: center; margin-bottom: 24px; border-bottom: 2px solid #e2e8f0; padding-bottom: 16px;">
            <h2 style="font-size: 18px; font-weight: 800; color: #1e293b;">
                REKAPITULASI PELAYANAN POSYANDU {{ strtoupper($tapos->nama) }}
            </h2>
            <p style="font-size: 13px; color: #64748b; margin-top: 4px;">
                Wilayah Kerja Puskesmas | Periode: <strong>{{ $periode }}</strong>
            </p>
        </div>

        <div class="stats-grid" style="grid-template-columns: repeat(2, 1fr); margin-bottom: 24px;">
            <div class="stat-card">
                <span class="stat-label">Total Sasaran Balita</span>
                <strong class="stat-number" style="color: var(--primary);">{{ $totalBalita }}</strong>
                <span class="stat-description">Balita terdaftar</span>
            </div>

            <div class="stat-card">
                <span class="stat-label">Total Sasaran Ibu Hamil</span>
                <strong class="stat-number" style="color: #db2777;">{{ $totalIbuHamil }}</strong>
                <span class="stat-description">Ibu hamil terpantau</span>
            </div>
        </div>

        <div style="font-size: 13px; color: #475569; line-height: 1.6; background: #f8fafc; padding: 18px; border-radius: 8px; border: 1px solid #e2e8f0;">
            <strong>Keterangan Pelaksanaan Posyandu:</strong>
            <ul style="margin-top: 6px; padding-left: 20px;">
                <li>Pelayanan dilaksanakan sesuai jadwal kegiatan Posyandu {{ $tapos->nama }}.</li>
                <li>Data pertumbuhan dan skrining stunting telah terintegrasi dengan pemantauan Puskesmas.</li>
                <li>Laporan ini telah divalidasi oleh Kader Penanggung Jawab Posyandu: <strong>{{ $user->name }}</strong>.</li>
            </ul>
        </div>
    </div>
@endif

@endsection
