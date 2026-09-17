@extends('layouts.app')

@section('title', 'Edit Balita: ' . $balita->nama)
@section('page-title', 'Edit Balita')

@section('content')

<div class="page-heading">
    <div>
        <span class="eyebrow">MASTER DATA</span>
        <h1>Edit Data Balita</h1>
        <p>Perbarui profil dan informasi balita {{ $balita->nama }}.</p>
    </div>

    <a href="{{ route('balita.index') }}" class="btn-secondary">
        &larr; Kembali
    </a>
</div>

<div class="dashboard-card" style="padding: 28px;">
    <form method="POST" action="{{ route('balita.update', $balita) }}">
        @csrf
        @method('PUT')

        <div class="form-grid">
            <div class="form-group">
                <label>Posyandu / Tapos <span style="color: #dc2626;">*</span></label>
                <select name="tapos_id" required class="form-control">
                    <option value="">Pilih Posyandu / Tapos</option>
                    @foreach($tapos as $item)
                        <option value="{{ $item->id }}" @selected(old('tapos_id', $balita->tapos_id) == $item->id)>
                            {{ $item->nama }}
                        </option>
                    @endforeach
                </select>
                @error('tapos_id') <span style="color: #dc2626; font-size: 11px;">{{ $message }}</span> @enderror
            </div>

            <x-form-input label="NIK Balita" name="nik" :value="old('nik', $balita->nik)" required />
            <x-form-input label="Nama Lengkap Balita" name="nama" :value="old('nama', $balita->nama)" required />

            <div class="form-group">
                <label>Jenis Kelamin <span style="color: #dc2626;">*</span></label>
                <select name="jenis_kelamin" required class="form-control">
                    <option value="L" @selected(old('jenis_kelamin', $balita->jenis_kelamin) === 'L')>Laki-laki</option>
                    <option value="P" @selected(old('jenis_kelamin', $balita->jenis_kelamin) === 'P')>Perempuan</option>
                </select>
                @error('jenis_kelamin') <span style="color: #dc2626; font-size: 11px;">{{ $message }}</span> @enderror
            </div>

            <x-form-input label="Tanggal Lahir" name="tanggal_lahir" type="date" :value="old('tanggal_lahir', $balita->tanggal_lahir?->format('Y-m-d') ?? $balita->tanggal_lahir)" required />
            <x-form-input label="Nama Ibu Kandung" name="nama_ibu" :value="old('nama_ibu', $balita->nama_ibu)" />
            <x-form-input label="Nama Ayah" name="nama_ayah" :value="old('nama_ayah', $balita->nama_ayah)" />
            <x-form-input label="No. HP Orang Tua" name="no_hp_orang_tua" :value="old('no_hp_orang_tua', $balita->no_hp_orang_tua)" />

            <div class="form-group">
                <label>Status Balita</label>
                <select name="status" class="form-control">
                    <option value="aktif" @selected(old('status', $balita->status) === 'aktif')>🟢 Aktif</option>
                    <option value="pindah" @selected(old('status', $balita->status) === 'pindah')>⚪ Pindah</option>
                    <option value="meninggal" @selected(old('status', $balita->status) === 'meninggal')>🔴 Meninggal</option>
                </select>
            </div>
        </div>

        <div style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 14px;">
            <a href="{{ route('balita.index') }}" class="btn-secondary">
                Batal
            </a>
            <button type="submit" class="btn-primary">
                Perbarui Data Balita
            </button>
        </div>
    </form>
</div>

@endsection