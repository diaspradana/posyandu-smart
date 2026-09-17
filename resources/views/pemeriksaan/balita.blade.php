@extends('layouts.app')

@section('title', 'Pemeriksaan Balita')
@section('page-title', 'Pemeriksaan Balita')

@section('content')
<div class="page-heading">
    <div>
        <span class="eyebrow">REKAP PEMERIKSAAN</span>
        <h1>🩺 Data Pemeriksaan Balita</h1>
        <p>
            Daftar catatan penimbangan, pengukuran tinggi badan, status stunting, dan imunisasi balita.
        </p>
    </div>
    <div class="date-card">
        <span>Total Pemeriksaan</span>
        <strong>{{ $totalPemeriksaan }} Data</strong>
    </div>
</div>

{{-- SUMMARY MINI CARDS --}}
<div class="stats-grid" style="grid-template-columns: repeat(3, 1fr); margin-bottom: 20px;">
    <div class="stat-card">
        <span class="stat-label">Risiko Stunting</span>
        <strong class="stat-number" style="color: #dc2626;">{{ $risikoCount }}</strong>
        <span class="stat-description">Pemeriksaan berisiko</span>
    </div>
    <div class="stat-card">
        <span class="stat-label">Perlu Pemantauan</span>
        <strong class="stat-number" style="color: #ea580c;">{{ $pemantauanCount }}</strong>
        <span class="stat-description">Pemeriksaan pemantauan</span>
    </div>
    <div class="stat-card">
        <span class="stat-label">Normal</span>
        <strong class="stat-number" style="color: #16a34a;">{{ $normalCount }}</strong>
        <span class="stat-description">Pemeriksaan normal</span>
    </div>
</div>

{{-- FILTER FORM --}}
<div class="dashboard-card" style="padding: 20px; margin-bottom: 24px;">
    <form method="GET" action="{{ route('admin.pemeriksaan.balita') }}" class="table-toolbar">
        <div class="search-input-wrap">
            <span class="search-icon">🔍</span>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama balita / NIK...">
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

            <select name="status_stunting" class="custom-select" onchange="this.form.submit()">
                <option value="">Semua Status Stunting</option>
                <option value="risiko_stunting" {{ request('status_stunting') == 'risiko_stunting' ? 'selected' : '' }}>🔴 Risiko Stunting</option>
                <option value="pemantauan" {{ request('status_stunting') == 'pemantauan' ? 'selected' : '' }}>🟠 Perlu Pemantauan</option>
                <option value="normal" {{ request('status_stunting') == 'normal' ? 'selected' : '' }}>🟢 Normal</option>
            </select>

            <select name="status_imunisasi" class="custom-select" onchange="this.form.submit()">
                <option value="">Semua Imunisasi</option>
                <option value="lengkap" {{ request('status_imunisasi') == 'lengkap' ? 'selected' : '' }}>Lengkap</option>
                <option value="belum_lengkap" {{ request('status_imunisasi') == 'belum_lengkap' ? 'selected' : '' }}>Belum Lengkap</option>
                <option value="tertunda" {{ request('status_imunisasi') == 'tertunda' ? 'selected' : '' }}>Tertunda</option>
            </select>

            @if(request()->hasAny(['search', 'tapos_id', 'status_stunting', 'status_imunisasi']))
                <a href="{{ route('admin.pemeriksaan.balita') }}" class="filter-tab" style="display: inline-flex; align-items: center; background: #e2e8f0;">
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
                    <th>Nama Balita</th>
                    <th>Tapos</th>
                    <th>Berat Badan</th>
                    <th>Tinggi Badan</th>
                    <th>Status Stunting</th>
                    <th>Status Imunisasi</th>
                    <th>Kehadiran</th>
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
                            <strong>{{ $p->balita?->nama ?? '-' }}</strong>
                            <div style="font-size: 11px; color: #64748b;">NIK: {{ $p->balita?->nik ?? '-' }} &bull; {{ $p->balita?->umur_jk ?? '-' }}</div>
                        </td>
                        <td>{{ $p->balita?->tapos?->nama ?? '-' }}</td>
                        <td>{{ $p->kehadiran ? $p->berat_badan . ' kg' : '-' }}</td>
                        <td>{{ $p->kehadiran ? $p->tinggi_badan . ' cm' : '-' }}</td>
                        <td>
                            @if($p->kehadiran)
                                @if($p->status_stunting === 'risiko_stunting' || $p->status_stunting === 'stunting')
                                    <span class="badge-stunting risiko">🔴 Risiko</span>
                                @elseif($p->status_stunting === 'pemantauan')
                                    <span class="badge-stunting pemantauan">🟠 Pemantauan</span>
                                @else
                                    <span class="badge-stunting normal">🟢 Normal</span>
                                @endif
                            @else
                                <span class="badge-stunting belum">-</span>
                            @endif
                        </td>
                        <td>
                            @if($p->kehadiran)
                                <span class="badge-stunting {{ $p->status_imunisasi === 'lengkap' ? 'normal' : ($p->status_imunisasi === 'tertunda' ? 'risiko' : 'pemantauan') }}">
                                    {{ ucfirst(str_replace('_', ' ', $p->status_imunisasi)) }}
                                </span>
                            @else
                                <span class="badge-stunting belum">-</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge-stunting {{ $p->kehadiran ? 'normal' : 'risiko' }}">
                                {{ $p->kehadiran ? 'Hadir' : 'Tidak Hadir' }}
                            </span>
                        </td>
                        <td style="text-align: center;">
                            @if($p->balita_id)
                                <a href="{{ route('balita.show', $p->balita_id) }}" class="btn-detail-link">
                                    Detail
                                </a>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" style="text-align: center; padding: 24px; color: #94a3b8;">
                            Tidak ada data pemeriksaan yang cocok.
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
