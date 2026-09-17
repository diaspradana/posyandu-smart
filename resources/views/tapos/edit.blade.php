@extends('layouts.app')

@section('title', 'Edit Tapos: ' . $tapo->nama)
@section('page-title', 'Edit Tapos')

@section('content')

<div class="page-heading">
    <div>
        <span class="eyebrow">MASTER DATA</span>
        <h1>Edit Data Tapos</h1>
        <p>Perbarui informasi pos pelayanan posyandu {{ $tapo->nama }}.</p>
    </div>

    <a href="{{ route('tapos.index') }}" class="btn-secondary">
        &larr; Kembali
    </a>
</div>

<div class="dashboard-card" style="padding: 28px;">
    <form method="POST" action="{{ route('tapos.update', $tapo) }}">
        @csrf
        @method('PUT')

        <div class="form-grid">
            <div class="form-group">
                <label>Nama Tapos <span style="color: #dc2626;">*</span></label>
                <input type="text" name="nama" value="{{ old('nama', $tapo->nama) }}" required class="form-control">
                @error('nama') <span style="color: #dc2626; font-size: 11px;">{{ $message }}</span> @enderror
            </div>

            <div class="form-group">
                <label>Kode Tapos <span style="color: #dc2626;">*</span></label>
                <input type="text" name="kode" value="{{ old('kode', $tapo->kode) }}" required class="form-control">
                @error('kode') <span style="color: #dc2626; font-size: 11px;">{{ $message }}</span> @enderror
            </div>

            <div class="form-group">
                <label>Kelurahan</label>
                <input type="text" name="kelurahan" value="{{ old('kelurahan', $tapo->kelurahan) }}" class="form-control">
            </div>

            <div class="form-group">
                <label>Kecamatan</label>
                <input type="text" name="kecamatan" value="{{ old('kecamatan', $tapo->kecamatan) }}" class="form-control">
            </div>

            <div class="form-group">
                <label>Nama Ketua Tapos</label>
                <input type="text" name="nama_ketua" value="{{ old('nama_ketua', $tapo->nama_ketua) }}" class="form-control">
            </div>

            <div class="form-group">
                <label>No. HP / Telepon</label>
                <input type="text" name="no_hp" value="{{ old('no_hp', $tapo->no_hp) }}" class="form-control">
            </div>

            <div class="form-group">
                <label>Status Operasional</label>
                <select name="status" class="form-control">
                    <option value="aktif" @selected(old('status', $tapo->status) === 'aktif')>🟢 Aktif</option>
                    <option value="tidak_aktif" @selected(old('status', $tapo->status) === 'tidak_aktif')>🔴 Tidak Aktif</option>
                </select>
            </div>
        </div>

        <div class="form-group" style="margin-bottom: 24px;">
            <label>Alamat Lengkap</label>
            <textarea name="alamat" rows="3" class="form-control">{{ old('alamat', $tapo->alamat) }}</textarea>
        </div>

        <div style="display: flex; justify-content: flex-end; gap: 10px;">
            <a href="{{ route('tapos.index') }}" class="btn-secondary">
                Batal
            </a>
            <button type="submit" class="btn-primary">
                Simpan Perubahan
            </button>
        </div>
    </form>
</div>

@endsection