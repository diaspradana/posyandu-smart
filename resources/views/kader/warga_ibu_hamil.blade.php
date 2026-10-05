@extends('layouts.app')

@section('title', 'Data Ibu Hamil - Kader ' . $tapos->nama)
@section('page-title', 'Data Warga Ibu Hamil')

@section('content')

{{-- PAGE HEADER --}}
<div class="page-heading">
    <div>
        <span class="eyebrow">MASTER DATA WARGA</span>
        <h1>🤰 Data Ibu Hamil — {{ $tapos->nama }}</h1>
        <p>Kelola data ibu hamil sasaran posyandu: tambah data baru, edit profil kehamilan, hapus, dan lihat riwayat pemeriksaan.</p>
    </div>

    <div style="display: flex; gap: 8px; flex-wrap: wrap; align-items: center;">
        <a href="{{ route('ibu-hamil.create') }}" class="btn-primary" style="background: #db2777;">
            <span>+</span>
            <span>Tambah Ibu Hamil Baru</span>
        </a>
        <a href="{{ route('kader.kegiatan.pemeriksaan', ['tab' => 'ibu_hamil']) }}" class="btn-primary" style="background: #0284c7;">
            <span>🩺</span>
            <span>Catat Pemeriksaan</span>
        </a>
    </div>
</div>

{{-- SEARCH & FILTER BAR --}}
<div class="dashboard-card" style="padding: 16px 20px; margin-bottom: 20px;">
    <form method="GET" action="{{ route('kader.warga.ibu-hamil') }}" class="table-toolbar" style="margin-bottom: 0;">
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
            <select name="status" class="custom-select" onchange="this.form.submit()">
                <option value="">Semua Status</option>
                <option value="aktif" @selected(request('status') === 'aktif')>🟢 Aktif</option>
                <option value="melahirkan" @selected(request('status') === 'melahirkan')>👶 Melahirkan</option>
                <option value="pindah" @selected(request('status') === 'pindah')>⚪ Pindah</option>
                <option value="meninggal" @selected(request('status') === 'meninggal')>🔴 Meninggal</option>
            </select>

            <button type="submit" class="filter-tab active" style="padding: 7px 16px;">
                Cari
            </button>

            @if(request()->hasAny(['search', 'status']))
                <a href="{{ route('kader.warga.ibu-hamil') }}" class="filter-tab" style="background: #e2e8f0; padding: 7px 14px; text-decoration: none;">
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
                    <th>NIK</th>
                    <th style="text-align: center;">Kehamilan Ke-</th>
                    <th>Usia Kehamilan</th>
                    <th>HPHT / Kontak</th>
                    <th style="text-align: center;">Status</th>
                    <th style="text-align: right;">Aksi & Tindakan</th>
                </tr>
            </thead>
            <tbody>
                @forelse($ibuHamilList as $item)
                    <tr>
                        <td>
                            <strong>{{ $item->nama }}</strong>
                            <div style="font-size: 11px; color: #64748b;">Posyandu: {{ $tapos->nama }}</div>
                        </td>
                        <td>
                            <span style="font-family: monospace; font-size: 12px; color: #475569;">{{ $item->nik }}</span>
                        </td>
                        <td style="text-align: center; font-weight: 700; color: var(--primary);">
                            Gravida (G{{ $item->kehamilan_ke }})
                        </td>
                        <td>
                            <span style="font-weight: 600;">{{ $item->usia_kehamilan_minggu ? $item->usia_kehamilan_minggu . ' Minggu' : '-' }}</span>
                        </td>
                        <td>
                            <div>{{ $item->hari_pertama_haid_terakhir ? \Carbon\Carbon::parse($item->hari_pertama_haid_terakhir)->format('d/m/Y') : '-' }}</div>
                            <div style="font-size: 11px; color: #64748b;">{{ $item->no_hp ?? '-' }}</div>
                        </td>
                        <td style="text-align: center;">
                            <span class="badge-status {{ $item->status === 'aktif' ? 'aktif' : ($item->status === 'meninggal' ? 'meninggal' : ($item->status === 'melahirkan' ? 'melahirkan' : 'pindah')) }}">
                                {{ ucfirst($item->status) }}
                            </span>
                        </td>
                        <td style="text-align: right;">
                            <div class="actions-group">
                                <a href="{{ route('ibu-hamil.show', $item) }}" class="btn-action view" title="Lihat Detail Profil">
                                    Detail
                                </a>

                                <a href="{{ route('ibu-hamil.edit', $item) }}" class="btn-action edit" title="Edit Data Ibu Hamil">
                                    Edit
                                </a>

                                <form
                                    method="POST"
                                    action="{{ route('ibu-hamil.destroy', $item) }}"
                                    onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ibu hamil {{ $item->nama }}?')"
                                    style="display: inline;"
                                >
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-action delete" title="Hapus Data">
                                        Hapus
                                    </button>
                                </form>

                                <a href="{{ route('kader.kegiatan.pemeriksaan', ['tab' => 'ibu_hamil', 'ibu_hamil_id' => $item->id]) }}" class="btn-action" style="background: #fdf2f8; color: #db2777; border: 1px solid #db2777; padding: 4px 8px;" title="Catat Pemeriksaan">
                                    🩺 Periksa
                                </a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" style="text-align: center; padding: 32px 16px; color: #94a3b8;">
                            Belum ada data ibu hamil yang cocok dengan filter.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div style="margin-top: 16px;">
    {{ $ibuHamilList->links() }}
</div>

@endsection
