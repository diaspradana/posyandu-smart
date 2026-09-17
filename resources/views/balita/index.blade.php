@extends('layouts.app')

@section('title', 'Data Balita')
@section('page-title', 'Data Balita')

@section('content')

{{-- PAGE HEADING --}}
<div class="page-heading">
    <div>
        <span class="eyebrow">MASTER DATA</span>
        <h1>Data Balita</h1>
        <p>Kelola data balita yang terdaftar di seluruh Posyandu Tapos wilayah Puskesmas.</p>
    </div>

    <a href="{{ route('balita.create') }}" class="btn-primary">
        <span>+</span>
        <span>Tambah Balita</span>
    </a>
</div>

{{-- SEARCH & FILTER CARD --}}
<div class="dashboard-card" style="padding: 16px 20px; margin-bottom: 20px;">
    <form method="GET" action="{{ route('balita.index') }}" class="table-toolbar" style="margin-bottom: 0;">
        <div class="search-input-wrap">
            <span class="search-icon">🔍</span>
            <input
                type="text"
                name="search"
                value="{{ request('search') }}"
                placeholder="Cari nama balita, NIK, atau nama orang tua..."
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
                <option value="pindah" @selected(request('status') === 'pindah')>⚪ Pindah</option>
                <option value="meninggal" @selected(request('status') === 'meninggal')>🔴 Meninggal</option>
            </select>

            <button type="submit" class="filter-tab active" style="padding: 7px 16px;">
                Filter
            </button>

            @if(request()->hasAny(['search', 'tapos_id', 'status']))
                <a href="{{ route('balita.index') }}" class="filter-tab" style="background: #e2e8f0; padding: 7px 14px; text-decoration: none;">
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
                    <th>Nama Balita</th>
                    <th>Tapos</th>
                    <th>Jenis Kelamin</th>
                    <th>Tanggal Lahir / Umur</th>
                    <th>Nama Orang Tua</th>
                    <th style="text-align: center;">Status</th>
                    <th style="text-align: right;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($balita as $item)
                    <tr>
                        <td>
                            <strong>{{ $item->nama }}</strong>
                            <div style="font-size: 11px; color: #64748b;">NIK: {{ $item->nik }}</div>
                        </td>
                        <td>
                            <strong>{{ $item->tapos->nama ?? '-' }}</strong>
                        </td>
                        <td>
                            <span>{{ $item->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan' }}</span>
                        </td>
                        <td>
                            <div>{{ $item->tanggal_lahir?->format('d/m/Y') }}</div>
                            <div style="font-size: 11px; color: #64748b;">
                                {{ $item->umur_jk }}
                            </div>
                        </td>
                        <td>
                            <div>{{ $item->nama_ibu ?? '-' }}</div>
                            <div style="font-size: 11px; color: #64748b;">{{ $item->no_hp_orang_tua ?? '-' }}</div>
                        </td>
                        <td style="text-align: center;">
                            <span class="badge-status {{ $item->status === 'aktif' ? 'aktif' : ($item->status === 'meninggal' ? 'meninggal' : 'pindah') }}">
                                {{ ucfirst($item->status) }}
                            </span>
                        </td>
                        <td style="text-align: right;">
                            <div class="actions-group">
                                <a href="{{ route('balita.show', $item) }}" class="btn-action view" title="Lihat Detail">
                                    Detail
                                </a>

                                <a href="{{ route('balita.edit', $item) }}" class="btn-action edit" title="Edit Data">
                                    Edit
                                </a>

                                <form
                                    method="POST"
                                    action="{{ route('balita.destroy', $item) }}"
                                    onsubmit="return confirm('Apakah Anda yakin ingin menghapus data balita ini?')"
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
                            Belum ada data balita yang cocok dengan filter pencarian.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div style="margin-top: 16px;">
    {{ $balita->links() }}
</div>

@endsection