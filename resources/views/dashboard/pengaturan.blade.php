@extends('layouts.app')

@section('title', 'Pengaturan')
@section('page-title', 'Pengaturan')

@section('content')
<div class="page-heading">
    <div>
        <span class="eyebrow">KONFIGURASI APLIKASI</span>
        <h1>⚙️ Pengaturan Akun & Puskesmas</h1>
        <p>
            Kelola profil Puskesmas, akun pengguna, dan preferensi sistem Posyandu Smart.
        </p>
    </div>
</div>

<div class="dashboard-grid" style="grid-template-columns: 1fr 1fr;">
    {{-- PROFILE PUSKESMAS --}}
    <div class="dashboard-card" style="padding: 24px;">
        <h2 style="font-size: 16px; margin-bottom: 16px; color: #172b26;">Profil Puskesmas</h2>
        <div style="display: flex; flex-direction: column; gap: 14px;">
            <div>
                <label style="display: block; font-size: 11px; color: #64748b; font-weight: 600; text-transform: uppercase;">Nama Instansi</label>
                <input type="text" class="custom-select" style="width: 100%; margin-top: 4px;" value="{{ $puskesmas->nama ?? 'Puskesmas Sehat Sejahtera' }}" readonly>
            </div>
            <div>
                <label style="display: block; font-size: 11px; color: #64748b; font-weight: 600; text-transform: uppercase;">Alamat</label>
                <input type="text" class="custom-select" style="width: 100%; margin-top: 4px;" value="{{ $puskesmas->alamat ?? 'Jl. Pemuda No. 45' }}" readonly>
            </div>
            <div>
                <label style="display: block; font-size: 11px; color: #64748b; font-weight: 600; text-transform: uppercase;">Kecamatan / Kabupaten</label>
                <input type="text" class="custom-select" style="width: 100%; margin-top: 4px;" value="{{ $puskesmas->kecamatan ?? 'Sukamaju' }} / {{ $puskesmas->kabupaten ?? 'Kota Sehat' }}" readonly>
            </div>
        </div>
    </div>

    {{-- USER ACCOUNT --}}
    <div class="dashboard-card" style="padding: 24px;">
        <h2 style="font-size: 16px; margin-bottom: 16px; color: #172b26;">Informasi Akun</h2>
        <div style="display: flex; flex-direction: column; gap: 14px;">
            <div>
                <label style="display: block; font-size: 11px; color: #64748b; font-weight: 600; text-transform: uppercase;">Nama Pengguna</label>
                <input type="text" class="custom-select" style="width: 100%; margin-top: 4px;" value="{{ $user->name ?? 'Admin Puskesmas' }}" readonly>
            </div>
            <div>
                <label style="display: block; font-size: 11px; color: #64748b; font-weight: 600; text-transform: uppercase;">Email Akun</label>
                <input type="text" class="custom-select" style="width: 100%; margin-top: 4px;" value="{{ $user->email ?? 'admin@posyandusmart.test' }}" readonly>
            </div>
            <div>
                <label style="display: block; font-size: 11px; color: #64748b; font-weight: 600; text-transform: uppercase;">Peran Akses</label>
                <span class="badge-stunting normal" style="margin-top: 6px; display: inline-block;">
                    {{ strtoupper($user->role ?? 'ADMIN') }}
                </span>
            </div>
        </div>
    </div>
</div>
@endsection
