@extends('layouts.app')

@section('title', 'Pemeriksaan Ibu Hamil')
@section('page-title', 'Pemeriksaan Ibu Hamil')

@section('content')
<div class="page-heading">
    <div>
        <span class="eyebrow">REKAP PEMERIKSAAN</span>
        <h1>🩺 Data Pemeriksaan Ibu Hamil</h1>
        <p>
            Daftar catatan usia kehamilan, berat badan, tekanan darah, dan riwayat kunjungan berkala ibu hamil.
        </p>
    </div>
    <div class="date-card">
        <span>Total Pemeriksaan</span>
        <strong>{{ $totalPemeriksaan }} Data</strong>
    </div>
</div>

{{-- SUMMARY MINI CARDS --}}
<div class="stats-grid" style="grid-template-columns: repeat(2, 1fr); margin-bottom: 20px;">
    <div class="stat-card">
        <span class="stat-label">Sudah Diperiksa</span>
        <strong class="stat-number" style="color: #16a34a;">{{ $diperiksaCount }}</strong>
        <span class="stat-description">Pemeriksaan terlaksana</span>
    </div>
    <div class="stat-card">
        <span class="stat-label">Belum Diperiksa</span>
        <strong class="stat-number" style="color: #dc2626;">{{ $belumCount }}</strong>
        <span class="stat-description">Perlu tindak lanjut / kunjungan</span>
    </div>
</div>

{{-- FILTER FORM --}}
<div class="dashboard-card" style="padding: 20px; margin-bottom: 24px;">
    <form method="GET" action="{{ route('admin.pemeriksaan.ibu-hamil') }}" class="table-toolbar">
        <div class="search-input-wrap">
            <span class="search-icon">🔍</span>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama ibu hamil / NIK...">
        </div>

        <div style="display: flex; gap: 8px; flex-wrap: wrap;">
            <select name="tapos_id" class="custom-select" onchange="this.form.submit()">
                <option value="">Semua Tapos</option>
                @foreach($taposList as $t)
                    <option value="{{ $t->id }}" {{ request('tapos_id') == $t->id ? 'selected' : '' }}>
                        {{ $t->nama }}
                    </option>
                @endforeach
            </select>

            <select name="status_pemeriksaan" class="custom-select" onchange="this.form.submit()">
                <option value="">Semua Status</option>
                <option value="diperiksa" {{ request('status_pemeriksaan') == 'diperiksa' ? 'selected' : '' }}>🟢 Sudah Diperiksa</option>
                <option value="belum_diperiksa" {{ request('status_pemeriksaan') == 'belum_diperiksa' ? 'selected' : '' }}>🔴 Belum Diperiksa</option>
            </select>

            @if(request()->hasAny(['search', 'tapos_id', 'status_pemeriksaan']))
                <a href="{{ route('admin.pemeriksaan.ibu-hamil') }}" class="filter-tab" style="display: inline-flex; align-items: center; background: #e2e8f0;">
                    Reset
                </a>
            @endif
        </div>
    </form>

    <div class="table-responsive">
        <table class="custom-table">
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Nama Ibu Hamil</th>
                    <th>Tapos</th>
                    <th>Usia Kehamilan</th>
                    <th>Berat Badan</th>
                    <th>Tekanan Darah</th>
                    <th>Status Pemeriksaan</th>
                    <th style="text-align: center;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pemeriksaan as $p)
                    <tr>
                        <td>
                            <strong>{{ $p->tanggal_pemeriksaan?->format('d/m/Y') }}</strong>
                        </td>
                        <td>
                            <strong>{{ $p->ibuHamil?->nama ?? '-' }}</strong>
                            <div style="font-size: 11px; color: #64748b;">NIK: {{ $p->ibuHamil?->nik ?? '-' }}</div>
                        </td>
                        <td>{{ $p->ibuHamil?->tapos?->nama ?? '-' }}</td>
                        <td>
                            @if($p->kehadiran)
                                <span>{{ $p->usia_kehamilan_minggu }} minggu</span>
                            @else
                                <span style="color: #94a3b8;">-</span>
                            @endif
                        </td>
                        <td>
                            @if($p->kehadiran)
                                <span>{{ $p->berat_badan }} kg</span>
                            @else
                                <span style="color: #94a3b8;">-</span>
                            @endif
                        </td>
                        <td>
                            @if($p->kehadiran)
                                <span>{{ $p->tekanan_darah }}</span>
                            @else
                                <span style="color: #94a3b8;">-</span>
                            @endif
                        </td>
                        <td>
                            @if($p->status_pemeriksaan === 'diperiksa' && $p->kehadiran)
                                <span class="badge-stunting normal">🟢 Diperiksa</span>
                            @else
                                <span class="badge-stunting risiko">🔴 Belum Diperiksa</span>
                            @endif
                        </td>
                        <td style="text-align: center;">
                            @if($p->ibu_hamil_id)
                                <a href="{{ route('ibu-hamil.show', $p->ibu_hamil_id) }}" class="btn-detail-link">
                                    Detail
                                </a>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" style="text-align: center; padding: 24px; color: #94a3b8;">
                            Tidak ada data pemeriksaan ibu hamil yang cocok.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div style="margin-top: 18px;">
        {{ $pemeriksaan->links() }}
    </div>
</div>
@endsection
