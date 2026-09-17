@extends('layouts.app')

@section('title', 'Rekap Kehadiran Posyandu - Kader')
@section('page-title', 'Kehadiran Posyandu')

@section('content')

{{-- PAGE HEADER --}}
<div class="page-heading">
    <div>
        <span class="eyebrow">RINGKASAN TINGKAT KEHADIRAN</span>
        <h1>📊 Tingkat Kehadiran — {{ $tapos->nama }}</h1>
        <p>Pantauan keaktifan kunjungan posyandu warga bulan September 2026.</p>
    </div>

    <div class="date-card">
        <span>Periode Pelayanan</span>
        <strong>September 2026</strong>
    </div>
</div>

{{-- 1. PROGRESS BARS KEHADIRAN PER KATEGORI --}}
<div class="dashboard-card" style="padding: 24px; margin-bottom: 24px;">
    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 20px; border-bottom: 1px solid #f1f5f9; padding-bottom: 12px;">
        <h2 style="font-size: 16px; font-weight: 700; color: #1e293b;">
            KEGIATAN SEPTEMBER (Pertemuan 1)
        </h2>
        <span class="badge-status aktif">📍 {{ $tapos->nama }}</span>
    </div>

    {{-- Progress Balita --}}
    <div class="progress-group-item" style="margin-bottom: 20px;">
        <div class="progress-header">
            <span style="display: flex; align-items: center; gap: 6px;">
                <span>👶</span>
                <span>Balita</span>
            </span>
            <strong style="color: #059669; font-size: 14px;">{{ $balitaRate }}%</strong>
        </div>
        <div class="progress-bar-track">
            <div class="progress-bar-fill balita" style="width: {{ $balitaRate }}%;"></div>
        </div>
    </div>

    {{-- Progress Ibu Hamil --}}
    <div class="progress-group-item">
        <div class="progress-header">
            <span style="display: flex; align-items: center; gap: 6px;">
                <span>🤰</span>
                <span>Ibu Hamil</span>
            </span>
            <strong style="color: #db2777; font-size: 14px;">{{ $bumilRate }}%</strong>
        </div>
        <div class="progress-bar-track">
            <div class="progress-bar-fill bumil" style="width: {{ $bumilRate }}%;"></div>
        </div>
    </div>
</div>

{{-- 2. TOTAL COUNTERS --}}
<div class="stats-grid" style="grid-template-columns: repeat(3, 1fr); margin-bottom: 24px;">
    <div class="stat-card">
        <span class="stat-label">Total Terdaftar</span>
        <strong class="stat-number" style="color: #334155;">{{ $totalTerdaftar }}</strong>
        <span class="stat-description">Warga sasaran terdaftar</span>
    </div>

    <div class="stat-card">
        <span class="stat-label">Hadir Pelayanan</span>
        <strong class="stat-number" style="color: var(--primary);">{{ $totalHadir }}</strong>
        <span class="stat-description">Warga hadir di posyandu</span>
    </div>

    <div class="stat-card">
        <span class="stat-label">Tidak Hadir</span>
        <strong class="stat-number" style="color: #dc2626;">{{ $totalTidakHadir }}</strong>
        <span class="stat-description">Perlu kunjungan rumah / tindak lanjut</span>
    </div>
</div>

@endsection
