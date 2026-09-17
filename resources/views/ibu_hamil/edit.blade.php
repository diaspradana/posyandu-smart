@extends('layouts.app')

@section('title', 'Edit Ibu Hamil: ' . $ibuHamil->nama)
@section('page-title', 'Edit Ibu Hamil')

@section('content')

<div class="page-heading">
    <div>
        <span class="eyebrow">MASTER DATA</span>
        <h1>Edit Data Ibu Hamil</h1>
        <p>Perbarui informasi profil dan kehamilan {{ $ibuHamil->nama }}.</p>
    </div>

    <a href="{{ route('ibu-hamil.index') }}" class="btn-secondary">
        &larr; Kembali
    </a>
</div>

<div class="dashboard-card" style="padding: 28px;">
    <form method="POST" action="{{ route('ibu-hamil.update', $ibuHamil) }}">
        @csrf
        @method('PUT')

        <div class="form-grid">
            <div class="form-group">
                <label>Posyandu / Tapos <span style="color: #dc2626;">*</span></label>
                <select name="tapos_id" required class="form-control">
                    <option value="">Pilih Posyandu / Tapos</option>
                    @foreach($tapos as $item)
                        <option value="{{ $item->id }}" @selected(old('tapos_id', $ibuHamil->tapos_id) == $item->id)>
                            {{ $item->nama }}
                        </option>
                    @endforeach
                </select>
                @error('tapos_id') <span style="color: #dc2626; font-size: 11px;">{{ $message }}</span> @enderror
            </div>

            <x-form-input label="NIK Ibu Hamil" name="nik" :value="old('nik', $ibuHamil->nik)" required />
            <x-form-input label="Nama Lengkap" name="nama" :value="old('nama', $ibuHamil->nama)" required />
            <x-form-input label="Tanggal Lahir" name="tanggal_lahir" type="date" :value="old('tanggal_lahir', $ibuHamil->tanggal_lahir?->format('Y-m-d') ?? $ibuHamil->tanggal_lahir)" required />
            <x-form-input label="No. Telepon / HP" name="no_hp" :value="old('no_hp', $ibuHamil->no_hp)" />
            <x-form-input label="Kehamilan Ke- (Gravida)" name="kehamilan_ke" type="number" min="1" :value="old('kehamilan_ke', $ibuHamil->kehamilan_ke)" required />
            <x-form-input label="Usia Kehamilan (Minggu)" name="usia_kehamilan_minggu" type="number" min="1" max="42" :value="old('usia_kehamilan_minggu', $ibuHamil->usia_kehamilan_minggu)" />
            <x-form-input label="Hari Pertama Haid Terakhir (HPHT)" name="hari_pertama_haid_terakhir" type="date" :value="old('hari_pertama_haid_terakhir', $ibuHamil->hari_pertama_haid_terakhir?->format('Y-m-d') ?? $ibuHamil->hari_pertama_haid_terakhir)" />

            <div class="form-group">
                <label>Status</label>
                <select name="status" class="form-control">
                    <option value="aktif" @selected(old('status', $ibuHamil->status) === 'aktif')>🟢 Aktif</option>
                    <option value="melahirkan" @selected(old('status', $ibuHamil->status) === 'melahirkan')>👶 Melahirkan</option>
                    <option value="pindah" @selected(old('status', $ibuHamil->status) === 'pindah')>⚪ Pindah</option>
                    <option value="meninggal" @selected(old('status', $ibuHamil->status) === 'meninggal')>🔴 Meninggal</option>
                </select>
            </div>
        </div>

        <div class="form-group" style="margin-bottom: 24px;">
            <label>Alamat Lengkap</label>
            <textarea name="alamat" rows="3" class="form-control">{{ old('alamat', $ibuHamil->alamat) }}</textarea>
        </div>

        <div style="display: flex; justify-content: flex-end; gap: 10px;">
            <a href="{{ route('ibu-hamil.index') }}" class="btn-secondary">
                Batal
            </a>
            <button type="submit" class="btn-primary">
                Perbarui Data Ibu Hamil
            </button>
        </div>
    </form>
</div>

@endsection