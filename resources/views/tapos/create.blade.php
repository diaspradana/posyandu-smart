@extends('layouts.app')

@section('title', 'Tambah Tapos')
@section('page-title', 'Tambah Tapos')

@section('content')

<div class="page-heading">
    <div>
        <span class="eyebrow">MASTER DATA</span>
        <h1>Tambah Data Tapos</h1>
        <p>Daftarkan pos pelayanan posyandu baru dalam wilayah kerja Puskesmas.</p>
    </div>

    <a href="{{ route('tapos.index') }}" class="btn-secondary">
        &larr; Kembali
    </a>
</div>

<div class="dashboard-card" style="padding: 28px;">
    <form method="POST" action="{{ route('tapos.store') }}">
        @csrf

        <div class="form-grid">
            <div class="form-group">
                <label>Nama Tapos <span style="color: #dc2626;">*</span></label>
                <input type="text" name="nama" value="{{ old('nama') }}" required class="form-control" placeholder="Contoh: Tapos Melati">
                @error('nama') <span style="color: #dc2626; font-size: 11px;">{{ $message }}</span> @enderror
            </div>

            <div class="form-group">
                <label>Kode Tapos <span style="color: #dc2626;">*</span></label>
                <input type="text" name="kode" value="{{ old('kode') }}" required class="form-control" placeholder="Contoh: TAP-005">
                @error('kode') <span style="color: #dc2626; font-size: 11px;">{{ $message }}</span> @enderror
            </div>

            <div class="form-group">
                <label>Kelurahan</label>
                <input type="text" name="kelurahan" value="{{ old('kelurahan') }}" class="form-control" placeholder="Nama Kelurahan / Desa">
            </div>

            <div class="form-group">
                <label>Kecamatan</label>
                <input type="text" name="kecamatan" value="{{ old('kecamatan') }}" class="form-control" placeholder="Nama Kecamatan">
            </div>

            <div class="form-group">
                <label>Nama Ketua Tapos</label>
                <input type="text" name="nama_ketua" value="{{ old('nama_ketua') }}" class="form-control" placeholder="Nama lengkap ketua">
            </div>

            <div class="form-group">
                <label>No. HP / Telepon</label>
                <input type="text" name="no_hp" value="{{ old('no_hp') }}" class="form-control" placeholder="08xxxxxxxxxx">
            </div>

            <div class="form-group">
                <label>Status Operasional</label>
                <select name="status" class="form-control">
                    <option value="aktif" @selected(old('status') === 'aktif')>🟢 Aktif</option>
                    <option value="tidak_aktif" @selected(old('status') === 'tidak_aktif')>🔴 Tidak Aktif</option>
                </select>
            </div>
        </div>

        <div class="form-group" style="margin-bottom: 24px;">
            <label>Alamat Lengkap</label>
            <textarea name="alamat" rows="3" class="form-control" placeholder="Alamat lengkap pos pelayanan">{{ old('alamat') }}</textarea>
        </div>

        <div style="display: flex; justify-content: flex-end; gap: 10px;">
            <a href="{{ route('tapos.index') }}" class="btn-secondary">
                Batal
            </a>
            <button type="submit" class="btn-primary">
                Simpan Data Tapos
            </button>
        </div>
    </form>
</div>

@endsection