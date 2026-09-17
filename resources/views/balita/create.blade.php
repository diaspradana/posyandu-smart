@extends('layouts.app')

@section('title', 'Tambah Balita')
@section('page-title', 'Tambah Balita')

@section('content')

<div class="page-heading">
    <div>
        <span class="eyebrow">MASTER DATA</span>
        <h1>Tambah Data Balita</h1>
        <p>Daftarkan data balita baru ke dalam sistem pemantauan Posyandu.</p>
    </div>

    <a href="{{ route('balita.index') }}" class="btn-secondary">
        &larr; Kembali
    </a>
</div>

<div class="dashboard-card" style="padding: 28px;">
    <form method="POST" action="{{ route('balita.store') }}">
        @csrf

        <div class="form-grid">
            <div class="form-group">
                <label>Posyandu / Tapos <span style="color: #dc2626;">*</span></label>
                <select name="tapos_id" required class="form-control">
                    <option value="">Pilih Posyandu / Tapos</option>
                    @foreach($tapos as $item)
                        <option value="{{ $item->id }}" @selected(old('tapos_id') == $item->id)>
                            {{ $item->nama }}
                        </option>
                    @endforeach
                </select>
                @error('tapos_id') <span style="color: #dc2626; font-size: 11px;">{{ $message }}</span> @enderror
            </div>

            <x-form-input label="NIK Balita" name="nik" placeholder="16 digit NIK balita" required />
            <x-form-input label="Nama Lengkap Balita" name="nama" placeholder="Nama lengkap balita" required />

            <div class="form-group">
                <label>Jenis Kelamin <span style="color: #dc2626;">*</span></label>
                <select name="jenis_kelamin" required class="form-control">
                    <option value="">Pilih Jenis Kelamin</option>
                    <option value="L" @selected(old('jenis_kelamin') === 'L')>Laki-laki</option>
                    <option value="P" @selected(old('jenis_kelamin') === 'P')>Perempuan</option>
                </select>
                @error('jenis_kelamin') <span style="color: #dc2626; font-size: 11px;">{{ $message }}</span> @enderror
            </div>

            <x-form-input label="Tanggal Lahir" name="tanggal_lahir" type="date" required />
            <x-form-input label="Nama Ibu Kandung" name="nama_ibu" placeholder="Nama ibu" />
            <x-form-input label="Nama Ayah" name="nama_ayah" placeholder="Nama ayah" />
            <x-form-input label="No. HP Orang Tua" name="no_hp_orang_tua" placeholder="08xxxxxxxxxx" />

            <div class="form-group">
                <label>Status Balita</label>
                <select name="status" class="form-control">
                    <option value="aktif" @selected(old('status') === 'aktif')>🟢 Aktif</option>
                    <option value="pindah" @selected(old('status') === 'pindah')>⚪ Pindah</option>
                    <option value="meninggal" @selected(old('status') === 'meninggal')>🔴 Meninggal</option>
                </select>
            </div>
        </div>

        <div style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 14px;">
            <a href="{{ route('balita.index') }}" class="btn-secondary">
                Batal
            </a>
            <button type="submit" class="btn-primary">
                Simpan Data Balita
            </button>
        </div>
    </form>
</div>

@endsection