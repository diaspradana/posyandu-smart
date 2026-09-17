@extends('layouts.app')

@section('title', 'Data Ibu Hamil')
@section('page-title', 'Data Ibu Hamil')

@section('content')

{{-- PAGE HEADING --}}
<div class="page-heading">
    <div>
        <span class="eyebrow">MASTER DATA</span>
        <h1>Data Ibu Hamil</h1>
        <p>Kelola data ibu hamil yang terpantau pada seluruh Posyandu Tapos wilayah Puskesmas.</p>
    </div>

    <a href="{{ route('ibu-hamil.create') }}" class="btn-primary">
        <span>+</span>
        <span>Tambah Ibu Hamil</span>
    </a>
</div>

{{-- SEARCH & FILTER CARD --}}
<div class="dashboard-card" style="padding: 16px 20px; margin-bottom: 20px;">
    <form method="GET" action="{{ route('ibu-hamil.index') }}" class="table-toolbar" style="margin-bottom: 0;">
        <div class="search-input-wrap">
            <span class="search-icon">🔍</span>
            <input
                type="text"
                name="search"
                value="{{ request('search') }}"
                placeholder="Cari nama ibu hamil atau NIK..."
            >
        </div>

        <div style="display: flex; gap: 8px; flex-wrap: wrap; align-items: center;">
            <select name="tapos_id" class="custom-select" onchange="this.form.submit()">
                <option value="">Semua Tapos</option>
                @foreach($tapos as $item)
                    <option value="{{ $item->id }}" @selected(request('tapos_id') == $item->id)>
                        {{ $item->nama }}
                    </option>
                @endforeach
            </select>

            <select name="status" class="custom-select" onchange="this.form.submit()">
                <option value="">Semua Status</option>
                <option value="aktif" @selected(request('status') === 'aktif')>🟢 Aktif</option>
                <option value="melahirkan" @selected(request('status') === 'melahirkan')>👶 Melahirkan</option>
                <option value="pindah" @selected(request('status') === 'pindah')>⚪ Pindah</option>
                <option value="meninggal" @selected(request('status') === 'meninggal')>🔴 Meninggal</option>
            </select>

            <button type="submit" class="filter-tab active" style="padding: 7px 16px;">
                Filter
            </button>

            @if(request()->hasAny(['search', 'tapos_id', 'status']))
                <a href="{{ route('ibu-hamil.index') }}" class="filter-tab" style="background: #e2e8f0; padding: 7px 14px; text-decoration: none;">
                    Reset
                </a>
            @endif
        </div>
    </form>
</div>

{{-- TABLE CARD --}}
<div class="dashboard-card" style="padding: 0; overflow: hidden; margin-bottom: 24px;">
    <div class="table-responsive">
        <table class="custom-table">
            <thead>
                <tr>
                    <th>Nama Ibu Hamil</th>
                    <th>Tapos</th>
                    <th style="text-align: center;">Kehamilan Ke-</th>
                    <th>Usia Kehamilan</th>
                    <th>HPHT</th>
                    <th style="text-align: center;">Status</th>
                    <th style="text-align: right;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($ibuHamil as $item)
                    <tr>
                        <td>
                            <strong>{{ $item->nama }}</strong>
                            <div style="font-size: 11px; color: #64748b;">NIK: {{ $item->nik }}</div>
                        </td>
                        <td>
                            <strong>{{ $item->tapos->nama ?? '-' }}</strong>
                        </td>
                        <td style="text-align: center; font-weight: 700;">
                            G{{ $item->kehamilan_ke }}
                        </td>
                        <td>
                            <span>{{ $item->usia_kehamilan_minggu ?? '-' }} Minggu</span>
                        </td>
                        <td>
                            <div>{{ $item->hari_pertama_haid_terakhir ? \Carbon\Carbon::parse($item->hari_pertama_haid_terakhir)->format('d/m/Y') : '-' }}</div>
                            <div style="font-size: 11px; color: #64748b;">
                                {{ $item->no_hp ?? '-' }}
                            </div>
                        </td>
                        <td style="text-align: center;">
                            <span class="badge-status {{ $item->status === 'aktif' ? 'aktif' : ($item->status === 'meninggal' ? 'meninggal' : 'pindah') }}">
                                {{ ucfirst($item->status) }}
                            </span>
                        </td>
                        <td style="text-align: right;">
                            <div class="actions-group">
                                <a href="{{ route('ibu-hamil.show', $item) }}" class="btn-action view" title="Lihat Detail">
                                    Detail
                                </a>

                                <a href="{{ route('ibu-hamil.edit', $item) }}" class="btn-action edit" title="Edit Data">
                                    Edit
                                </a>

                                <form
                                    method="POST"
                                    action="{{ route('ibu-hamil.destroy', $item) }}"
                                    onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ibu hamil ini?')"
                                    style="display: inline;"
                                >
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-action delete" title="Hapus">
                                        Hapus
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" style="text-align: center; padding: 32px 16px; color: #94a3b8;">
                            Belum ada data ibu hamil yang cocok dengan pencarian.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div style="margin-top: 16px;">
    {{ $ibuHamil->links() }}
</div>

@endsection