@extends('layouts.app')

@section('title', 'Data Tapos')
@section('page-title', 'Data Tapos')

@section('content')

{{-- PAGE HEADING --}}
<div class="page-heading">
    <div>
        <span class="eyebrow">MASTER DATA</span>
        <h1>Data Posyandu (Tapos)</h1>
        <p>Kelola data tempat pelayanan dan pos kegiatan Posyandu di wilayah kerja Puskesmas.</p>
    </div>

    <a href="{{ route('tapos.create') }}" class="btn-primary">
        <span>+</span>
        <span>Tambah Tapos</span>
    </a>
</div>

{{-- SEARCH & FILTER CARD --}}
<div class="dashboard-card" style="padding: 16px 20px; margin-bottom: 20px;">
    <form method="GET" action="{{ route('tapos.index') }}" class="table-toolbar" style="margin-bottom: 0;">
        <div class="search-input-wrap">
            <span class="search-icon">🔍</span>
            <input
                type="text"
                name="search"
                value="{{ request('search') }}"
                placeholder="Cari nama, kode, atau kelurahan..."
            >
        </div>

        <div style="display: flex; gap: 8px; flex-wrap: wrap; align-items: center;">
            <select name="status" class="custom-select" onchange="this.form.submit()">
                <option value="">Semua Status</option>
                <option value="aktif" @selected(request('status') === 'aktif')>🟢 Aktif</option>
                <option value="tidak_aktif" @selected(request('status') === 'tidak_aktif')>🔴 Tidak Aktif</option>
            </select>

            <button type="submit" class="filter-tab active" style="padding: 7px 16px;">
                Cari
            </button>

            @if(request()->hasAny(['search', 'status']))
                <a href="{{ route('tapos.index') }}" class="filter-tab" style="background: #e2e8f0; padding: 7px 14px; text-decoration: none;">
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
                    <th>Tapos</th>
                    <th>Wilayah / Kelurahan</th>
                    <th style="text-align: center;">Balita</th>
                    <th style="text-align: center;">Ibu Hamil</th>
                    <th style="text-align: center;">Status</th>
                    <th style="text-align: right;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($tapos as $tapo)
                    <tr>
                        <td>
                            <strong>{{ $tapo->nama }}</strong>
                            <div style="font-size: 11px; color: #64748b;">Kode: {{ $tapo->kode }}</div>
                        </td>
                        <td>
                            <div>{{ $tapo->kelurahan ?? '-' }}</div>
                            <div style="font-size: 11px; color: #64748b;">{{ $tapo->kecamatan ?? '-' }}</div>
                        </td>
                        <td style="text-align: center; font-weight: 600;">
                            {{ $tapo->balita_count }}
                        </td>
                        <td style="text-align: center; font-weight: 600;">
                            {{ $tapo->ibu_hamil_count }}
                        </td>
                        <td style="text-align: center;">
                            <span class="badge-status {{ $tapo->status === 'aktif' ? 'aktif' : 'tidak_aktif' }}">
                                {{ $tapo->status === 'aktif' ? '🟢 Aktif' : '🔴 Tidak Aktif' }}
                            </span>
                        </td>
                        <td style="text-align: right;">
                            <div class="actions-group">
                                <a href="{{ route('tapos.show', $tapo) }}" class="btn-action view" title="Lihat Detail">
                                    Detail
                                </a>

                                <a href="{{ route('tapos.edit', $tapo) }}" class="btn-action edit" title="Edit Data">
                                    Edit
                                </a>

                                <form
                                    method="POST"
                                    action="{{ route('tapos.destroy', $tapo) }}"
                                    onsubmit="return confirm('Apakah Anda yakin ingin menghapus data Tapos ini?')"
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
                            Belum ada data Tapos yang cocok dengan pencarian.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div style="margin-top: 16px;">
    {{ $tapos->links() }}
</div>

@endsection