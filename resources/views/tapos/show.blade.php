@extends('layouts.app')

@section('title', 'Detail Tapos: ' . $tapo->nama)
@section('page-title', 'Detail Tapos')

@section('content')
<div class="page-heading">
    <div>
        <span class="eyebrow">DETAIL WILAYAH POSYANDU</span>
        <h1>{{ $tapo->nama }}</h1>
        <p>Kode: <strong>{{ $tapo->kode }}</strong> | Kelurahan: {{ $tapo->kelurahan ?? '-' }}, Kecamatan: {{ $tapo->kecamatan ?? '-' }}</p>
    </div>

    <div style="display: flex; gap: 8px;">
        <a href="{{ route('tapos.edit', $tapo) }}" class="btn-primary" style="background: #eab308; color: #713f12;">
            ✏️ Edit Data
        </a>
        <a href="{{ route('tapos.index') }}" class="btn-secondary">
            &larr; Kembali
        </a>
    </div>
</div>

<div class="stats-grid" style="grid-template-columns: repeat(2, 1fr); margin-bottom: 24px;">
    <div class="stat-card">
        <span class="stat-label">Total Balita</span>
        <strong class="stat-number" style="color: var(--primary);">{{ $tapo->balita_count }}</strong>
        <a href="{{ route('balita.index', ['tapos_id' => $tapo->id]) }}" class="stat-description" style="color: var(--primary); font-weight: 600; text-decoration: underline; margin-top: 8px; display: inline-block;">
            Lihat Daftar Balita →
        </a>
    </div>

    <div class="stat-card">
        <span class="stat-label">Total Ibu Hamil</span>
        <strong class="stat-number" style="color: #db2777;">{{ $tapo->ibu_hamil_count }}</strong>
        <a href="{{ route('ibu-hamil.index', ['tapos_id' => $tapo->id]) }}" class="stat-description" style="color: #db2777; font-weight: 600; text-decoration: underline; margin-top: 8px; display: inline-block;">
            Lihat Daftar Ibu Hamil →
        </a>
    </div>
</div>

<div class="dashboard-card" style="padding: 24px;">
    <h2 style="font-size: 16px; font-weight: 700; margin-bottom: 16px; color: #172b26; border-bottom: 1px solid #f1f5f9; padding-bottom: 10px;">
        Informasi Lokasi & Kepengurusan
    </h2>

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 16px; font-size: 13px;">
        <div>
            <span style="font-size: 11px; color: #64748b; font-weight: 600; text-transform: uppercase;">Nama Ketua Tapos</span>
            <div style="font-weight: 700; color: #1e293b; margin-top: 2px;">{{ $tapo->nama_ketua ?? '-' }}</div>
        </div>

        <div>
            <span style="font-size: 11px; color: #64748b; font-weight: 600; text-transform: uppercase;">No. Telepon / HP</span>
            <div style="font-weight: 600; margin-top: 2px;">{{ $tapo->no_hp ?? '-' }}</div>
        </div>

        <div>
            <span style="font-size: 11px; color: #64748b; font-weight: 600; text-transform: uppercase;">Alamat Lengkap</span>
            <div style="font-weight: 600; margin-top: 2px;">{{ $tapo->alamat ?? '-' }}</div>
        </div>

        <div>
            <span style="font-size: 11px; color: #64748b; font-weight: 600; text-transform: uppercase;">Status Operasional</span>
            <div style="margin-top: 4px;">
                <span class="badge-status {{ $tapo->status === 'aktif' ? 'aktif' : 'tidak_aktif' }}">
                    {{ $tapo->status === 'aktif' ? '🟢 Aktif' : '🔴 Tidak Aktif' }}
                </span>
            </div>
        </div>
    </div>
</div>
@endsection