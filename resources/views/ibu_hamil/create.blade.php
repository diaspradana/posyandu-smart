@extends('layouts.app')

@section('title', 'Tambah Ibu Hamil')
@section('page-title', 'Tambah Ibu Hamil')

@section('content')

<div class="page-heading">
    <div>
        <span class="eyebrow">MASTER DATA</span>
        <h1>Tambah Data Ibu Hamil</h1>
        <p>Daftarkan data ibu hamil baru ke dalam pemantauan kesehatan berkala Posyandu.</p>
    </div>

    <a href="{{ route('ibu-hamil.index') }}" class="btn-secondary">
        &larr; Kembali
    </a>
</div>

<div class="dashboard-card" style="padding: 28px;">
    <form method="POST" action="{{ route('ibu-hamil.store') }}">
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

            <x-form-input label="NIK Ibu Hamil" name="nik" placeholder="16 digit NIK" required />
            <x-form-input label="Nama Lengkap" name="nama" placeholder="Nama lengkap ibu hamil" required />
            <x-form-input label="Tanggal Lahir" name="tanggal_lahir" type="date" required />
            <x-form-input label="No. Telepon / HP" name="no_hp" placeholder="08xxxxxxxxxx" />
            <x-form-input label="Kehamilan Ke- (Gravida)" name="kehamilan_ke" type="number" min="1" placeholder="Contoh: 1, 2, 3" required />
            <x-form-input label="Usia Kehamilan (Minggu)" name="usia_kehamilan_minggu" type="number" min="1" max="42" placeholder="Contoh: 14" />
            <x-form-input label="Hari Pertama Haid Terakhir (HPHT)" name="hari_pertama_haid_terakhir" type="date" />

            <div class="form-group">
                <label>Status</label>
                <select name="status" class="form-control">
                    <option value="aktif" @selected(old('status') === 'aktif')>🟢 Aktif</option>
                    <option value="melahirkan" @selected(old('status') === 'melahirkan')>👶 Melahirkan</option>
                    <option value="pindah" @selected(old('status') === 'pindah')>⚪ Pindah</option>
                    <option value="meninggal" @selected(old('status') === 'meninggal')>🔴 Meninggal</option>
                </select>
            </div>
        </div>

        <div class="form-group" style="margin-bottom: 24px;">
            <label>Alamat Lengkap</label>
            <textarea name="alamat" rows="3" class="form-control" placeholder="Alamat domisili lengkap...">{{ old('alamat') }}</textarea>
        </div>

        <div style="display: flex; justify-content: flex-end; gap: 10px;">
            <a href="{{ route('ibu-hamil.index') }}" class="btn-secondary">
                Batal
            </a>
            <button type="submit" class="btn-primary">
                Simpan Data Ibu Hamil
            </button>
        </div>
    </form>
</div>

@endsection