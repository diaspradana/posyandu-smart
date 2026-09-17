@extends('layouts.app')

@section('title', 'Monitoring Puskesmas & Stunting')
@section('page-title', 'Monitoring Puskesmas')

@section('content')
<div class="page-heading">
    <div>
        <span class="eyebrow">FITUR UTAMA PROYEK</span>
        <h1>📊 Monitoring Balita & Risiko Stunting</h1>
        <p>
            Pantau dan lakukan intervensi dini risiko stunting dan status kesehatan balita di seluruh Posyandu Tapos.
        </p>
    </div>
    <div class="date-card">
        <span>Wilayah Kerja</span>
        <strong>Puskesmas Sukamaju</strong>
    </div>
</div>

{{-- 4 STATUS SUMMARY CARDS --}}
<div class="stunting-summary-grid">
    <div class="stunting-stat-box red">
        <div class="box-header">
            <span>🔴</span>
            <span class="box-title">Risiko Stunting</span>
        </div>
        <strong class="box-number">{{ $balitaStuntingCount }}</strong>
        <span class="box-desc">Memerlukan intervensi gizi segera</span>
    </div>

    <div class="stunting-stat-box orange">
        <div class="box-header">
            <span>🟠</span>
            <span class="box-title">Perlu Pemantauan</span>
        </div>
        <strong class="box-number">{{ $balitaPemantauanCount }}</strong>
        <span class="box-desc">Perlu pemantauan berat & tinggi berkala</span>
    </div>

    <div class="stunting-stat-box green">
        <div class="box-header">
            <span>🟢</span>
            <span class="box-title">Normal</span>
        </div>
        <strong class="box-number">{{ $balitaNormalCount }}</strong>
        <span class="box-desc">Pertumbuhan balita sesuai grafik standar WHO</span>
    </div>

    <div class="stunting-stat-box gray">
        <div class="box-header">
            <span>⚪</span>
            <span class="box-title">Belum Diperiksa</span>
        </div>
        <strong class="box-number">{{ $balitaBelumDiperiksaCount }}</strong>
        <span class="box-desc">Belum memiliki riwayat pemeriksaan</span>
    </div>
</div>

{{-- TABEL DATA BALITA MONITORING --}}
<div class="dashboard-card" style="padding: 20px; margin-bottom: 30px;">
    <div class="table-toolbar">
        <div class="search-input-wrap">
            <span class="search-icon">🔍</span>
            <input type="text" id="search-monitoring" placeholder="Cari nama balita / NIK...">
        </div>

        <div style="display: flex; gap: 8px; flex-wrap: wrap;">
            <select id="filter-monitoring-tapos" class="custom-select">
                <option value="all">Semua Tapos</option>
                @foreach($taposList as $t)
                    <option value="{{ $t->nama }}">{{ $t->nama }}</option>
                @endforeach
            </select>

            <select id="filter-monitoring-status" class="custom-select">
                <option value="all">Semua Status</option>
                <option value="risiko_stunting">🔴 Risiko Stunting</option>
                <option value="pemantauan">🟠 Perlu Pemantauan</option>
                <option value="normal">🟢 Normal</option>
                <option value="belum_diperiksa">⚪ Belum Diperiksa</option>
            </select>
        </div>
    </div>

    <div class="table-responsive">
        <table class="custom-table">
            <thead>
                <tr>
                    <th>Nama Balita</th>
                    <th>Tapos</th>
                    <th>Umur / JK</th>
                    <th>Berat / Tinggi</th>
                    <th>Tanggal Periksa</th>
                    <th>Status</th>
                    <th style="text-align: center;">AI Prediksi</th>
                    <th style="text-align: center;">Aksi</th>
                </tr>
            </thead>
            <tbody id="monitoring-table-body">
                @forelse($balitaMonitoringList as $b)
                    <tr
                        class="monitoring-row"
                        data-nama="{{ strtolower($b['nama']) }}"
                        data-nik="{{ $b['nik'] }}"
                        data-tapos="{{ $b['tapos_nama'] }}"
                        data-status="{{ $b['status_stunting'] }}"
                    >
                        <td>
                            <strong>{{ $b['nama'] }}</strong>
                            <div style="font-size: 11px; color: #64748b;">NIK: {{ $b['nik'] }}</div>
                        </td>
                        <td>
                            <span>{{ $b['tapos_nama'] }}</span>
                        </td>
                        <td>
                            <span>{{ $b['umur_jk'] }}</span>
                        </td>
                        <td>
                            @if($b['berat_badan'])
                                <span>{{ $b['berat_badan'] }} kg / {{ $b['tinggi_badan'] }} cm</span>
                            @else
                                <span style="color: #94a3b8;">-</span>
                            @endif
                        </td>
                        <td>
                            <span>{{ $b['tanggal_periksa'] }}</span>
                        </td>
                        <td>
                            @if($b['status_stunting'] === 'risiko_stunting' || $b['status_stunting'] === 'stunting')
                                <span class="badge-stunting risiko">
                                    🔴 Risiko
                                </span>
                            @elseif($b['status_stunting'] === 'pemantauan')
                                <span class="badge-stunting pemantauan">
                                    🟠 Pemantauan
                                </span>
                            @elseif($b['status_stunting'] === 'normal')
                                <span class="badge-stunting normal">
                                    🟢 Normal
                                </span>
                            @else
                                <span class="badge-stunting belum">
                                    ⚪ Belum diperiksa
                                </span>
                            @endif
                        </td>
                        <td style="text-align: center;">
                            @if($b['ai_prediksi'] !== '-')
                                <span class="badge-ai-score">
                                    {{ $b['ai_prediksi'] }}
                                </span>
                            @else
                                <span class="badge-ai-score dash">
                                    -
                                </span>
                            @endif
                        </td>
                        <td style="text-align: center;">
                            <a href="{{ route('balita.show', $b['id']) }}" class="btn-detail-link">
                                Detail
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" style="text-align: center; padding: 24px; color: #94a3b8;">
                            Belum ada data balita.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('search-monitoring');
    const tableTaposFilter = document.getElementById('filter-monitoring-tapos');
    const tableStatusFilter = document.getElementById('filter-monitoring-status');
    const rows = document.querySelectorAll('.monitoring-row');

    function filterTable() {
        const query = searchInput ? searchInput.value.toLowerCase().trim() : '';
        const selectedTapos = tableTaposFilter ? tableTaposFilter.value : 'all';
        const selectedStatus = tableStatusFilter ? tableStatusFilter.value : 'all';

        rows.forEach(row => {
            const nama = row.dataset.nama || '';
            const nik = row.dataset.nik || '';
            const tapos = row.dataset.tapos || '';
            const status = row.dataset.status || '';

            const matchesQuery = !query || nama.includes(query) || nik.includes(query);
            const matchesTapos = (selectedTapos === 'all') || (tapos === selectedTapos);
            const matchesStatus = (selectedStatus === 'all') || (status === selectedStatus);

            if (matchesQuery && matchesTapos && matchesStatus) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    }

    if (searchInput) searchInput.addEventListener('input', filterTable);
    if (tableTaposFilter) tableTaposFilter.addEventListener('change', filterTable);
    if (tableStatusFilter) tableStatusFilter.addEventListener('change', filterTable);
});
</script>
@endsection

@endsection
