@extends('layouts.app')

@section('title', 'Profil Kader Posyandu')
@section('page-title', 'Profil Pengguna')

@section('content')

<div class="page-heading">
    <div>
        <span class="eyebrow">PENGATURAN AKUN</span>
        <h1>⚙️ Profil Kader Posyandu</h1>
        <p>Kelola data akun dan informasi penugasan posyandu Anda.</p>
    </div>
</div>

@if(session('success'))
    <div style="background: #f0fdf4; border: 1px solid #bbf7d0; border-left: 4px solid #16a34a; border-radius: 12px; padding: 14px 20px; margin-bottom: 20px; color: #166534; font-size: 13px; font-weight: 600;">
        ✓ {{ session('success') }}
    </div>
@endif

<div class="dashboard-grid" style="grid-template-columns: 1fr 2fr; gap: 24px; align-items: flex-start;">
    {{-- INFO PENUGASAN CARD --}}
    <div class="dashboard-card" style="padding: 24px;">
        <div style="text-align: center; margin-bottom: 20px;">
            <div style="width: 72px; height: 72px; border-radius: 9999px; background: var(--primary-soft); color: var(--primary); display: flex; align-items: center; justify-content: center; font-size: 28px; font-weight: 800; margin: 0 auto 12px;">
                {{ strtoupper(substr($user->name, 0, 1)) }}
            </div>
            <h2 style="font-size: 16px; font-weight: 700; color: #1e293b;">{{ $user->name }}</h2>
            <span class="badge-status aktif" style="margin-top: 4px;">Kader Posyandu Aktif</span>
        </div>

        <div style="display: flex; flex-direction: column; gap: 12px; font-size: 13px; border-top: 1px solid #f1f5f9; padding-top: 16px;">
            <div>
                <span style="font-size: 11px; color: #64748b; font-weight: 600; text-transform: uppercase;">Posyandu Penugasan</span>
                <div style="font-weight: 700; color: var(--primary);">📍 {{ $tapos->nama }}</div>
            </div>

            <div>
                <span style="font-size: 11px; color: #64748b; font-weight: 600; text-transform: uppercase;">Kode Posyandu</span>
                <div style="font-weight: 600;">{{ $tapos->kode }}</div>
            </div>

            <div>
                <span style="font-size: 11px; color: #64748b; font-weight: 600; text-transform: uppercase;">Wilayah / Kelurahan</span>
                <div style="font-weight: 600;">{{ $tapos->kelurahan ?? '-' }}</div>
            </div>

            <div>
                <span style="font-size: 11px; color: #64748b; font-weight: 600; text-transform: uppercase;">Ketua Posyandu</span>
                <div style="font-weight: 600;">{{ $tapos->nama_ketua ?? '-' }}</div>
            </div>
        </div>
    </div>

    {{-- FORM EDIT PROFIL CARD --}}
    <div class="dashboard-card" style="padding: 28px;">
        <h2 style="font-size: 16px; font-weight: 700; color: #1e293b; margin-bottom: 20px; border-bottom: 1px solid #f1f5f9; padding-bottom: 12px;">
            Perbarui Informasi Akun
        </h2>

        <form method="POST" action="{{ route('kader.profil.update') }}">
            @csrf
            @method('PUT')

            <div class="form-group" style="margin-bottom: 16px;">
                <label>Nama Lengkap Kader <span style="color: #dc2626;">*</span></label>
                <input type="text" name="name" value="{{ old('name', $user->name) }}" required class="form-control">
                @error('name') <span style="color: #dc2626; font-size: 11px;">{{ $message }}</span> @enderror
            </div>

            <div class="form-group" style="margin-bottom: 16px;">
                <label>Alamat Email <span style="color: #dc2626;">*</span></label>
                <input type="email" name="email" value="{{ old('email', $user->email) }}" required class="form-control">
                @error('email') <span style="color: #dc2626; font-size: 11px;">{{ $message }}</span> @enderror
            </div>

            <div class="form-group" style="margin-bottom: 24px;">
                <label>Password Baru (Kosongkan jika tidak ingin mengubah)</label>
                <input type="password" name="password" placeholder="Minimal 6 karakter" class="form-control">
                @error('password') <span style="color: #dc2626; font-size: 11px;">{{ $message }}</span> @enderror
            </div>

            <div style="display: flex; justify-content: flex-end;">
                <button type="submit" class="btn-primary" style="padding: 10px 22px;">
                    Simpan Perubahan Profil
                </button>
            </div>
        </form>
    </div>
</div>

@endsection
