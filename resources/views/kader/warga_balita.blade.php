@extends('layouts.app')

@section('title', 'Data Balita - Kader ' . $tapos->nama)
@section('page-title', 'Data Warga Balita')

@section('content')

{{-- PAGE HEADER --}}
<div class="page-heading">
    <div>
        <span class="eyebrow">DATA WARGA POSYANDU</span>
        <h1>👶 Data Balita — {{ $tapos->nama }}</h1>
        <p>Daftar seluruh balita sasaran posyandu di wilayah {{ $tapos->nama }}.</p>
    </div>

    <div style="display: flex; gap: 8px;">
        <a href="{{ route('kader.kegiatan.pemeriksaan', ['tab' => 'balita']) }}" class="btn-primary">
            <span>🩺</span>
            <span>Catat Pemeriksaan Balita</span>
        </a>
    </div>
</div>

{{-- SEARCH & FILTER BAR --}}
<div class="dashboard-card" style="padding: 16px 20px; margin-bottom: 20px;">
    <form method="GET" action="{{ route('kader.warga.balita') }}" class="table-toolbar" style="margin-bottom: 0;">
        <div class="search-input-wrap">
            <span class="search-icon">🔍</span>
            <input
                type="text"
                name="search"
                value="{{ request('search') }}"
                placeholder="Cari nama balita, NIK, atau nama ibu..."
            >
        </div>

        <div style="display: flex; gap: 8px; flex-wrap: wrap; align-items: center;">
            <select name="status" class="custom-select" onchange="this.form.submit()">
                <option value="">Semua Status</option>
                <option value="aktif" @selected(request('status') === 'aktif')>🟢 Aktif</option>
                <option value="pindah" @selected(request('status') === 'pindah')>⚪ Pindah</option>
                <option value="meninggal" @selected(request('status') === 'meninggal')>🔴 Meninggal</option>
            </select>

            <button type="submit" class="filter-tab active" style="padding: 7px 16px;">
                Cari
            </button>

            @if(request()->hasAny(['search', 'status']))
                <a href="{{ route('kader.warga.balita') }}" class="filter-tab" style="background: #e2e8f0; padding: 7px 14px; text-decoration: none;">
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
                    <th>NIK</th>
                    <th>Jenis Kelamin</th>
                    <th>Tanggal Lahir / Umur</th>
                    <th>Nama Ibu / Kontak</th>
                    <th style="text-align: center;">Status</th>
                    <th style="text-align: right;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($balitaList as $item)
                    <tr>
                        <td>
                            <strong>{{ $item->nama }}</strong>
                        </td>
                        <td>
                            <span style="font-size: 12px; color: #475569;">{{ $item->nik }}</span>
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
                                <a href="{{ route('kader.kegiatan.pemeriksaan', ['tab' => 'balita', 'balita_id' => $item->id]) }}" class="btn-action edit" title="Periksa Balita">
                                    🩺 Periksa
                                </a>

                                <a href="{{ route('balita.show', $item) }}" class="btn-action view" title="Detail Balita">
                                    Detail
                                </a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" style="text-align: center; padding: 32px 16px; color: #94a3b8;">
                            Belum ada data balita yang cocok dengan filter.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div style="margin-top: 16px;">
    {{ $balitaList->links() }}
</div>

@endsection
