@extends('layouts.app')

@section('title', 'Laporan Bulanan Posyandu - Kader')
@section('page-title', 'Laporan Posyandu')

@section('head')
<style>
    .filter-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 14px;
        align-items: flex-end;
    }
    .filter-group label {
        display: block;
        font-size: 12px;
        font-weight: 600;
        color: #475569;
        margin-bottom: 6px;
    }
    .filter-input {
        width: 100%;
        height: 38px;
        font-size: 13px;
        border-radius: 8px;
        border: 1px solid #cbd5e1;
        padding: 0 12px;
        background: #ffffff;
        color: #1e293b;
        outline: none;
        transition: border-color 0.2s;
    }
    .filter-input:focus {
        border-color: #087f5b;
        box-shadow: 0 0 0 2px rgba(8, 127, 91, 0.15);
    }
    .badge-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        background: #f1f5f9;
        color: #334155;
    }
    .badge-pill.active {
        background: #e8f7f1;
        color: #087f5b;
        border: 1px solid #a7f3d0;
    }
    @media print {
        body {
            background: #ffffff !important;
            font-size: 11pt;
            color: #000000;
        }
        .sidebar, .topbar, .page-heading, .filter-card, .status-bar, .no-print, .btn, .button, .sidebar-overlay {
            display: none !important;
        }
        .main-content {
            margin-left: 0 !important;
            padding: 0 !important;
            width: 100% !important;
        }
        .page-content {
            padding: 0 !important;
        }
        .report-card {
            border: none !important;
            box-shadow: none !important;
            padding: 0 !important;
            margin: 0 !important;
        }
        .data-table {
            width: 100% !important;
            border-collapse: collapse !important;
        }
        .data-table th, .data-table td {
            border: 1px solid #333333 !important;
            padding: 6px 8px !important;
            font-size: 10pt !important;
        }
        .data-table th {
            background-color: #f2f2f2 !important;
            color: #000 !important;
        }
    }
</style>
@endsection

@section('content')

{{-- PAGE HEADER --}}
<div class="page-heading no-print">
    <div>
        <span class="eyebrow">REKAPITULASI PELAYANAN POSYANDU</span>
        <h1>📄 Laporan Kegiatan — {{ $tapos->nama }}</h1>
        <p>Rekapitulasi dan laporan bulanan pelayanan penimbangan balita dan pemeriksaan ibu hamil Posyandu.</p>
    </div>
    <div class="date-card" style="display: flex; gap: 8px; align-items: center;">
        <button onclick="window.print()" class="button primary" style="font-size: 13px; padding: 8px 16px;">
            🖨️ Cetak / Unduh PDF
        </button>
        <button onclick="exportToCSV()" class="button secondary" style="font-size: 13px; padding: 8px 16px; background: #0284c7; color: white; border: none;">
            📊 Export Excel
        </button>
    </div>
</div>

{{-- 1. FILTER & PARAMETER FORM --}}
<div class="dashboard-card filter-card no-print" style="padding: 22px; margin-bottom: 24px; border: 1px solid #e2e8f0; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px; flex-wrap: wrap; gap: 8px;">
        <h2 style="font-size: 15px; font-weight: 700; color: #1e293b; display: flex; align-items: center; gap: 8px;">
            <span>⚙️</span>
            <span>Parameter Laporan Posyandu</span>
        </h2>
        <span style="font-size: 12px; color: #64748b;">Pilih periode dan kategori sasaran untuk menghasilkan rekap data</span>
    </div>

    <form method="GET" action="{{ route('kader.laporan') }}" id="kaderLaporanFilterForm">
        <input type="hidden" name="generate" value="1">

        <div class="filter-grid">
            {{-- 1. Periode Pelayanan --}}
            <div class="filter-group">
                <label>Periode Pelayanan</label>
                <select name="periode" class="filter-input">
                    <option value="Agustus 2026" {{ ($periode ?? '') === 'Agustus 2026' ? 'selected' : '' }}>Agustus 2026</option>
                    <option value="September 2026" {{ ($periode ?? '') === 'September 2026' ? 'selected' : '' }}>September 2026</option>
                    <option value="Juli 2026" {{ ($periode ?? '') === 'Juli 2026' ? 'selected' : '' }}>Juli 2026</option>
                    <option value="Juni 2026" {{ ($periode ?? '') === 'Juni 2026' ? 'selected' : '' }}>Juni 2026</option>
                    <option value="Mei 2026" {{ ($periode ?? '') === 'Mei 2026' ? 'selected' : '' }}>Mei 2026</option>
                    <option value="April 2026" {{ ($periode ?? '') === 'April 2026' ? 'selected' : '' }}>April 2026</option>
                    <option value="Maret 2026" {{ ($periode ?? '') === 'Maret 2026' ? 'selected' : '' }}>Maret 2026</option>
                    <option value="Februari 2026" {{ ($periode ?? '') === 'Februari 2026' ? 'selected' : '' }}>Februari 2026</option>
                    <option value="Januari 2026" {{ ($periode ?? '') === 'Januari 2026' ? 'selected' : '' }}>Januari 2026</option>
                    <option value="Tahun 2026" {{ ($periode ?? '') === 'Tahun 2026' ? 'selected' : '' }}>Rekapitulasi Tahun 2026</option>
                    <option value="semua" {{ ($periode ?? '') === 'semua' ? 'selected' : '' }}>Semua Periode</option>
                </select>
            </div>

            {{-- 2. Kategori Sasaran --}}
            <div class="filter-group">
                <label>Kategori Sasaran</label>
                <select name="jenis" class="filter-input">
                    <option value="semua" {{ ($jenis ?? 'semua') === 'semua' ? 'selected' : '' }}>Semua Sasaran (Balita & Ibu Hamil)</option>
                    <option value="balita" {{ ($jenis ?? '') === 'balita' ? 'selected' : '' }}>Khusus Balita & Stunting</option>
                    <option value="ibu_hamil" {{ ($jenis ?? '') === 'ibu_hamil' ? 'selected' : '' }}>Khusus Ibu Hamil & Maternal</option>
                </select>
            </div>

            {{-- 3. Status Hasil AI --}}
            <div class="filter-group">
                <label>Hasil Deteksi Dini AI</label>
                <select name="status_filter" class="filter-input">
                    <option value="semua" {{ ($statusFilter ?? 'semua') === 'semua' ? 'selected' : '' }}>Semua Status Hasil AI</option>
                    <option value="risiko" {{ ($statusFilter ?? '') === 'risiko' ? 'selected' : '' }}>🔴 Berisiko (Stunting / High Risk)</option>
                    <option value="pemantauan" {{ ($statusFilter ?? '') === 'pemantauan' ? 'selected' : '' }}>🟡 Pemantauan (Medium Risk)</option>
                    <option value="normal" {{ ($statusFilter ?? '') === 'normal' ? 'selected' : '' }}>🟢 Normal / Low Risk</option>
                </select>
            </div>

            {{-- 4. Status Validasi --}}
            <div class="filter-group">
                <label>Status Validasi Puskesmas</label>
                <select name="status_validasi" class="filter-input">
                    <option value="semua" {{ ($statusValidasi ?? 'semua') === 'semua' ? 'selected' : '' }}>Semua Status Validasi</option>
                    <option value="validated" {{ ($statusValidasi ?? '') === 'validated' ? 'selected' : '' }}>✓ Sudah Tervalidasi</option>
                    <option value="pending" {{ ($statusValidasi ?? '') === 'pending' ? 'selected' : '' }}>⏳ Menunggu Validasi</option>
                </select>
            </div>

            {{-- 5. Search --}}
            <div class="filter-group">
                <label>Cari Nama / NIK</label>
                <input type="text" name="search" class="filter-input" placeholder="Ketik nama atau NIK..." value="{{ $search ?? '' }}">
            </div>
        </div>

        <div style="display: flex; gap: 10px; margin-top: 18px; justify-content: flex-end; flex-wrap: wrap;">
            <a href="{{ route('kader.laporan') }}" class="button secondary" style="font-size: 13px; height: 38px; padding: 0 16px; display: inline-flex; align-items: center;">
                🔄 Reset Filter
            </a>
            <button type="submit" class="button primary" style="font-size: 13px; height: 38px; padding: 0 22px; display: inline-flex; align-items: center; gap: 6px;">
                <span>⚡</span>
                <span>Generate Laporan</span>
            </button>
        </div>
    </form>

    {{-- ACTIVE FILTER BADGES --}}
    <div style="display: flex; gap: 8px; flex-wrap: wrap; align-items: center; margin-top: 16px; padding-top: 14px; border-top: 1px dashed #e2e8f0;">
        <span style="font-size: 11px; font-weight: 700; color: #64748b; text-transform: uppercase;">Parameter Aktif:</span>
        <span class="badge-pill active">📍 {{ $tapos->nama }}</span>
        <span class="badge-pill active">📅 Periode: {{ $parsedPeriode['label'] }}</span>
        <span class="badge-pill active">👥 Kategori: {{ $jenis === 'balita' ? 'Khusus Balita' : ($jenis === 'ibu_hamil' ? 'Khusus Ibu Hamil' : 'Semua Sasaran') }}</span>
        @if(($statusFilter ?? 'semua') !== 'semua')
            <span class="badge-pill active">🏷️ Status AI: {{ ucfirst($statusFilter) }}</span>
        @endif
        @if(($statusValidasi ?? 'semua') !== 'semua')
            <span class="badge-pill active">🛡️ Validasi: {{ $statusValidasi === 'validated' ? 'Tervalidasi' : 'Menunggu Validasi' }}</span>
        @endif
        @if(!empty($search))
            <span class="badge-pill active">🔍 "{{ $search }}"</span>
        @endif
    </div>
</div>

{{-- 2. REPORT GENERATED RESULT NOTIFICATION --}}
<div class="status-bar no-print" style="background: #f0fdf4; border: 1px solid #bbf7d0; border-left: 4px solid #16a34a; border-radius: 12px; padding: 16px 20px; margin-bottom: 24px; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px;">
    <div style="display: flex; align-items: center; gap: 12px;">
        <div style="width: 36px; height: 36px; border-radius: 50%; background: #dcfce7; display: flex; align-items: center; justify-content: center; font-size: 18px; color: #16a34a; font-weight: bold;">
            ✓
        </div>
        <div>
            <strong style="color: #166534; font-size: 14px;">Laporan Posyandu Siap & Sesuai Parameter!</strong>
            <div style="font-size: 12px; color: #15803d;">
                Posyandu: <strong>{{ $tapos->nama }}</strong> | Periode: <strong>{{ $parsedPeriode['label'] }}</strong>
            </div>
        </div>
    </div>

    <div style="display: flex; gap: 8px;">
        <button onclick="window.print()" class="button primary" style="background: #0284c7; padding: 8px 16px; font-size: 13px;">
            <span>🖨️</span>
            <span>Cetak / PDF</span>
        </button>
        <button onclick="exportToCSV()" class="button primary" style="background: #16a34a; padding: 8px 16px; font-size: 13px;">
            <span>📊</span>
            <span>Unduh Excel</span>
        </button>
    </div>
</div>

{{-- 3. STATS GRID ADAPTS DYNAMICALLY TO SELECTED CATEGORY --}}
<div class="stats-grid" style="grid-template-columns: repeat(auto-fit, minmax(190px, 1fr)); gap: 16px; margin-bottom: 24px;">
    @if(($jenis ?? 'semua') === 'semua')
        <div class="stat-card" style="border-left: 4px solid var(--primary);">
            <span class="stat-label">Sasaran Balita</span>
            <strong class="stat-number" style="color: var(--primary);">{{ $totalBalita }}</strong>
            <span class="stat-description">{{ $balitaDiperiksaCount }} balita diperiksa di periode ini</span>
        </div>

        <div class="stat-card" style="border-left: 4px solid #db2777;">
            <span class="stat-label">Sasaran Ibu Hamil</span>
            <strong class="stat-number" style="color: #db2777;">{{ $totalIbuHamil }}</strong>
            <span class="stat-description">{{ $ibuHamilDiperiksaCount }} bumil diperiksa di periode ini</span>
        </div>

        <div class="stat-card" style="border-left: 4px solid #ef4444;">
            <span class="stat-label">Balita Risiko Stunting</span>
            <strong class="stat-number" style="color: #dc2626;">{{ $balitaStuntingCount }}</strong>
            <span class="stat-description">Hasil screening AI</span>
        </div>

        <div class="stat-card" style="border-left: 4px solid #e11d48;">
            <span class="stat-label">Bumil Risiko Tinggi</span>
            <strong class="stat-number" style="color: #be123c;">{{ $ibuHamilRiskCount }}</strong>
            <span class="stat-description">Maternal AI High Risk</span>
        </div>

        <div class="stat-card" style="border-left: 4px solid #0284c7;">
            <span class="stat-label">Tingkat Kehadiran</span>
            <strong class="stat-number" style="color: #0284c7;">{{ $attendanceRate }}%</strong>
            <span class="stat-description">Kehadiran posyandu</span>
        </div>
    @elseif($jenis === 'balita')
        <div class="stat-card" style="border-left: 4px solid var(--primary);">
            <span class="stat-label">Total Sasaran Balita</span>
            <strong class="stat-number" style="color: var(--primary);">{{ $totalBalita }}</strong>
            <span class="stat-description">Balita terdaftar di Tapos</span>
        </div>

        <div class="stat-card" style="border-left: 4px solid #0284c7;">
            <span class="stat-label">Balita Hadir & Diperiksa</span>
            <strong class="stat-number" style="color: #0284c7;">{{ $balitaDiperiksaCount }}</strong>
            <span class="stat-description">Penimbangan bulan ini</span>
        </div>

        <div class="stat-card" style="border-left: 4px solid #16a34a;">
            <span class="stat-label">Pertumbuhan Normal</span>
            <strong class="stat-number" style="color: #16a34a;">{{ $balitaNormalCount }}</strong>
            <span class="stat-description">Status gizi baik</span>
        </div>

        <div class="stat-card" style="border-left: 4px solid #d97706;">
            <span class="stat-label">Pemantauan Khusus</span>
            <strong class="stat-number" style="color: #d97706;">{{ $balitaPemantauanCount }}</strong>
            <span class="stat-description">Borderline tumbuh kembang</span>
        </div>

        <div class="stat-card" style="border-left: 4px solid #dc2626;">
            <span class="stat-label">Risiko Stunting</span>
            <strong class="stat-number" style="color: #dc2626;">{{ $balitaStuntingCount }}</strong>
            <span class="stat-description">Perlu PMT & pendampingan</span>
        </div>

        <div class="stat-card" style="border-left: 4px solid #9333ea;">
            <span class="stat-label">Imunisasi Tertunda</span>
            <strong class="stat-number" style="color: #9333ea;">{{ $balitaImunisasiTertunda }}</strong>
            <span class="stat-description">Belum lengkap / tunda</span>
        </div>
    @elseif($jenis === 'ibu_hamil')
        <div class="stat-card" style="border-left: 4px solid #db2777;">
            <span class="stat-label">Total Sasaran Bumil</span>
            <strong class="stat-number" style="color: #db2777;">{{ $totalIbuHamil }}</strong>
            <span class="stat-description">Ibu hamil binaan Tapos</span>
        </div>

        <div class="stat-card" style="border-left: 4px solid #0284c7;">
            <span class="stat-label">Bumil Hadir & Diperiksa</span>
            <strong class="stat-number" style="color: #0284c7;">{{ $ibuHamilDiperiksaCount }}</strong>
            <span class="stat-description">Pemeriksaan bulan ini</span>
        </div>

        <div class="stat-card" style="border-left: 4px solid #16a34a;">
            <span class="stat-label">Low Risk (Rendah)</span>
            <strong class="stat-number" style="color: #16a34a;">{{ $ibuHamilLowCount }}</strong>
            <span class="stat-description">Kondisi kehamilan prima</span>
        </div>

        <div class="stat-card" style="border-left: 4px solid #d97706;">
            <span class="stat-label">Medium Risk (Sedang)</span>
            <strong class="stat-number" style="color: #d97706;">{{ $ibuHamilMediumCount }}</strong>
            <span class="stat-description">Pantau tensi & asupan gizi</span>
        </div>

        <div class="stat-card" style="border-left: 4px solid #dc2626;">
            <span class="stat-label">High Risk (Tinggi)</span>
            <strong class="stat-number" style="color: #dc2626;">{{ $ibuHamilRiskCount }}</strong>
            <span class="stat-description">Konsultasi Bidan & RS</span>
        </div>

        <div class="stat-card" style="border-left: 4px solid #eab308;">
            <span class="stat-label">Menunggu Validasi</span>
            <strong class="stat-number" style="color: #ca8a04;">{{ $ibuHamilPendingValidasi }}</strong>
            <span class="stat-description">Proses supervisi Bidan</span>
        </div>
    @endif
</div>

{{-- 4. REPORT PREVIEW CARD (OFFICIAL DOCUMENT) --}}
<div class="dashboard-card report-card" style="padding: 28px; margin-bottom: 24px;">
    {{-- KOP LAPORAN RESMI POSYANDU --}}
    <div style="border-bottom: 2px solid #087f5b; padding-bottom: 16px; margin-bottom: 24px; text-align: center;">
        <div style="font-size: 13px; font-weight: 800; letter-spacing: 1.5px; color: #087f5b; text-transform: uppercase;">
            POSYANDU {{ strtoupper($tapos->nama) }} — WILAYAH KERJA PUSKESMAS SUKAMAJU
        </div>
        <h2 style="font-size: 19px; font-weight: 800; color: #172b26; margin-top: 4px;">
            LAPORAN REKAPITULASI PELAYANAN POSYANDU BULANAN
        </h2>
        <div style="font-size: 13px; color: #475569; margin-top: 4px; display: flex; justify-content: center; gap: 16px; flex-wrap: wrap;">
            <span>Posyandu: <strong>{{ $tapos->nama }} ({{ $tapos->kode }})</strong></span>
            <span>•</span>
            <span>Kelurahan: <strong>{{ $tapos->kelurahan }}</strong></span>
            <span>•</span>
            <span>Periode: <strong>{{ $parsedPeriode['label'] }}</strong></span>
        </div>
    </div>

    {{-- TABEL DATA PEMERIKSAAN BALITA --}}
    @if(($jenis ?? 'semua') === 'semua' || $jenis === 'balita')
        <div style="margin-bottom: 32px;">
            <div class="card-header" style="margin-bottom: 14px; display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <span class="card-eyebrow">REKAP BALITA</span>
                    <h3 style="font-size: 16px; font-weight: 700; color: #1e293b;">
                        {{ ($jenis ?? 'semua') === 'semua' ? '1. Rekapitulasi Pelayanan Balita' : 'Daftar Pemeriksaan Balita' }}
                        <span style="font-size: 13px; font-weight: 400; color: #64748b;">({{ $balitaExaminations->count() }} Balita)</span>
                    </h3>
                </div>
                <span class="badge secondary">{{ $balitaExaminations->where('kehadiran', true)->count() }} Hadir & Ditimbang</span>
            </div>

            <div class="table-responsive">
                <table class="data-table" id="tableBalitaKader">
                    <thead>
                        <tr>
                            <th style="width: 40px; text-align: center;">No</th>
                            <th>NIK</th>
                            <th>Nama Balita</th>
                            <th style="text-align: center;">JK</th>
                            <th style="text-align: center;">Usia</th>
                            <th style="text-align: right;">BB (kg)</th>
                            <th style="text-align: right;">TB (cm)</th>
                            <th style="text-align: right;">LK (cm)</th>
                            <th>Screening AI Stunting</th>
                            <th>Status Imunisasi</th>
                            <th>Tgl Periksa</th>
                            <th style="text-align: center;">Validasi Bidan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($balitaExaminations as $idx => $p)
                            <tr>
                                <td style="text-align: center;">{{ $idx + 1 }}</td>
                                <td><span style="font-family: monospace; font-size: 12px;">{{ $p->balita->nik ?? '-' }}</span></td>
                                <td>
                                    <strong>{{ $p->balita->nama ?? '-' }}</strong>
                                    <div style="font-size: 11px; color: #64748b;">Ibu: {{ $p->balita->nama_ibu ?? '-' }}</div>
                                </td>
                                <td style="text-align: center;">
                                    <span style="font-weight: 700; color: {{ ($p->balita->jenis_kelamin ?? 'L') === 'L' ? '#0284c7' : '#db2777' }};">
                                        {{ $p->balita->jenis_kelamin ?? 'L' }}
                                    </span>
                                </td>
                                <td style="text-align: center;">{{ $p->umur_bulan }} bln</td>
                                <td style="text-align: right; font-weight: 600;">{{ $p->kehadiran ? number_format($p->berat_badan, 1) : '-' }}</td>
                                <td style="text-align: right; font-weight: 600;">{{ $p->kehadiran ? number_format($p->tinggi_badan, 1) : '-' }}</td>
                                <td style="text-align: right;">{{ ($p->kehadiran && $p->lingkar_kepala) ? number_format($p->lingkar_kepala, 1) : '-' }}</td>
                                <td>
                                    @if(!$p->kehadiran)
                                        <span class="status-pill secondary" style="font-size: 11px;">⚪ Tidak Hadir</span>
                                    @else
                                        {!! $p->ai_badge !!}
                                        @if($p->ai_probability)
                                            <div style="font-size: 10px; color: #64748b; margin-top: 2px;">Keyakinan: {{ round($p->ai_probability * 100) }}%</div>
                                        @endif
                                    @endif
                                </td>
                                <td>
                                    @if(!$p->kehadiran)
                                        <span style="font-size: 12px; color: #94a3b8;">-</span>
                                    @elseif($p->status_imunisasi === 'lengkap')
                                        <span class="status-pill success" style="font-size: 11px;">✓ Lengkap</span>
                                    @elseif($p->status_imunisasi === 'tertunda')
                                        <span class="status-pill danger" style="font-size: 11px;">⚠️ Tertunda</span>
                                    @else
                                        <span class="status-pill warning" style="font-size: 11px;">Belum Lengkap</span>
                                    @endif
                                </td>
                                <td style="font-size: 12px; color: #475569;">{{ $p->tanggal_pemeriksaan?->format('d/m/Y') ?? '-' }}</td>
                                <td style="text-align: center;">
                                    {!! $p->status_validasi_badge !!}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="12" class="text-center" style="padding: 24px; color: #64748b;">
                                    Tidak ada data pemeriksaan balita untuk parameter yang dipilih.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    {{-- TABEL DATA PEMERIKSAAN IBU HAMIL --}}
    @if(($jenis ?? 'semua') === 'semua' || $jenis === 'ibu_hamil')
        <div style="margin-bottom: 24px;">
            <div class="card-header" style="margin-bottom: 14px; display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <span class="card-eyebrow">REKAP IBU HAMIL</span>
                    <h3 style="font-size: 16px; font-weight: 700; color: #1e293b;">
                        {{ ($jenis ?? 'semua') === 'semua' ? '2. Rekapitulasi Pelayanan Ibu Hamil' : 'Daftar Pemeriksaan Ibu Hamil' }}
                        <span style="font-size: 13px; font-weight: 400; color: #64748b;">({{ $bumilExaminations->count() }} Ibu Hamil)</span>
                    </h3>
                </div>
                <span class="badge secondary">{{ $bumilExaminations->where('kehadiran', true)->count() }} Hadir & Diperiksa</span>
            </div>

            <div class="table-responsive">
                <table class="data-table" id="tableBumilKader">
                    <thead>
                        <tr>
                            <th style="width: 40px; text-align: center;">No</th>
                            <th>NIK</th>
                            <th>Nama Ibu Hamil</th>
                            <th style="text-align: center;">Usia Kehamilan</th>
                            <th style="text-align: right;">BB (kg)</th>
                            <th style="text-align: center;">Tekanan Darah</th>
                            <th style="text-align: right;">Gula Darah</th>
                            <th>Screening AI Maternal</th>
                            <th>Catatan Kader</th>
                            <th>Tgl Periksa</th>
                            <th style="text-align: center;">Validasi Bidan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($bumilExaminations as $idx => $p)
                            <tr>
                                <td style="text-align: center;">{{ $idx + 1 }}</td>
                                <td><span style="font-family: monospace; font-size: 12px;">{{ $p->ibuHamil->nik ?? '-' }}</span></td>
                                <td>
                                    <strong>{{ $p->ibuHamil->nama ?? '-' }}</strong>
                                    <div style="font-size: 11px; color: #64748b;">HP: {{ $p->ibuHamil->no_hp ?? '-' }}</div>
                                </td>
                                <td style="text-align: center; font-weight: 600;">
                                    {{ $p->usia_kehamilan_minggu ? $p->usia_kehamilan_minggu . ' mgg' : '-' }}
                                </td>
                                <td style="text-align: right; font-weight: 600;">{{ $p->kehadiran ? number_format($p->berat_badan, 1) : '-' }}</td>
                                <td style="text-align: center;">
                                    @if($p->kehadiran)
                                        <span style="font-weight: 600; color: {{ ($p->systolic_bp >= 140 || $p->diastolic_bp >= 90) ? '#dc2626' : '#1e293b' }};">
                                            {{ $p->systolic_bp ? "{$p->systolic_bp}/{$p->diastolic_bp}" : ($p->tekanan_darah ?? '-') }} mmHg
                                        </span>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td style="text-align: right;">
                                    {{ ($p->kehadiran && $p->blood_sugar) ? number_format($p->blood_sugar, 1) . ' mmol/L' : '-' }}
                                </td>
                                <td>
                                    @if(!$p->kehadiran)
                                        <span class="status-pill secondary" style="font-size: 11px;">⚪ Tidak Hadir</span>
                                    @else
                                        {!! $p->risk_badge !!}
                                        @if($p->ai_probability)
                                            <div style="font-size: 10px; color: #64748b; margin-top: 2px;">Keyakinan: {{ round($p->ai_probability * 100) }}%</div>
                                        @endif
                                    @endif
                                </td>
                                <td style="font-size: 12px; color: #64748b; max-width: 180px;">
                                    {{ $p->catatan ? \Illuminate\Support\Str::limit($p->catatan, 35) : '-' }}
                                </td>
                                <td style="font-size: 12px; color: #475569;">{{ $p->tanggal_pemeriksaan?->format('d/m/Y') ?? '-' }}</td>
                                <td style="text-align: center;">
                                    {!! $p->status_validasi_badge !!}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="11" class="text-center" style="padding: 24px; color: #64748b;">
                                    Tidak ada data pemeriksaan ibu hamil untuk parameter yang dipilih.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    {{-- KETERANGAN PELAKSANAAN --}}
    <div style="font-size: 13px; color: #475569; line-height: 1.6; background: #f8fafc; padding: 16px 20px; border-radius: 8px; border: 1px solid #e2e8f0; margin-top: 24px;">
        <strong>Catatan & Keterangan Posyandu:</strong>
        <ul style="margin-top: 6px; padding-left: 20px;">
            <li>Pelayanan penimbangan dan pemeriksaan kesehatan telah dilaksanakan secara tertib di {{ $tapos->nama }}.</li>
            <li>Hasil deteksi dini risiko stunting dan kehamilan risiko tinggi telah diteruskan untuk tindak lanjut supervisi Puskesmas.</li>
            <li>Laporan ini digenerate secara otomatis oleh sistem Posyandu Smart berbasis rekam data kader posyandu.</li>
        </ul>
    </div>

    {{-- REPORT FOOTER & DUAL SIGNATURES --}}
    <div style="margin-top: 36px; padding-top: 20px; border-top: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: flex-end; font-size: 12px; color: #475569; flex-wrap: wrap; gap: 24px;">
        <div style="text-align: center; min-width: 180px;">
            <p>Mengetahui,</p>
            <p style="font-weight: 600;">Bidan Pendamping Posyandu</p>
            <div style="height: 50px;"></div>
            <p style="font-weight: 700; text-decoration: underline; color: #1e293b;">Bd. Ratna Dewi, S.Tr.Keb</p>
            <p style="font-size: 11px; color: #64748b;">NIP. 19890712 201402 2 003</p>
        </div>

        <div style="text-align: center; min-width: 180px;">
            <p>{{ $tapos->kelurahan }}, {{ now()->format('d F Y') }}</p>
            <p style="font-weight: 600;">Kader Penanggung Jawab {{ $tapos->nama }}</p>
            <div style="height: 50px;"></div>
            <p style="font-weight: 700; text-decoration: underline; color: #1e293b;">{{ $user->name }}</p>
            <p style="font-size: 11px; color: #64748b;">ID Kader: KDR-{{ sprintf('%03d', $tapos->id) }}-{{ $user->id }}</p>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    function exportToCSV() {
        let tables = [];
        const jenis = "{{ $jenis ?? 'semua' }}";
        
        if (jenis === 'semua') {
            const t1 = document.getElementById('tableBalitaKader');
            const t2 = document.getElementById('tableBumilKader');
            if (t1) tables.push({ name: 'Data_Balita_{{ $tapos->nama }}', table: t1 });
            if (t2) tables.push({ name: 'Data_Bumil_{{ $tapos->nama }}', table: t2 });
        } else if (jenis === 'balita') {
            const t = document.getElementById('tableBalitaKader');
            if (t) tables.push({ name: 'Data_Balita_{{ $tapos->nama }}', table: t });
        } else if (jenis === 'ibu_hamil') {
            const t = document.getElementById('tableBumilKader');
            if (t) tables.push({ name: 'Data_Bumil_{{ $tapos->nama }}', table: t });
        }

        if (tables.length === 0) {
            alert('Tidak ada tabel data untuk diexport.');
            return;
        }

        let csvContent = "data:text/csv;charset=utf-8,\uFEFF";
        
        tables.forEach(item => {
            csvContent += `=== ${item.name} ===\r\n`;
            let rows = item.table.querySelectorAll('tr');
            rows.forEach(row => {
                let rowData = [];
                row.querySelectorAll('th, td').forEach(cell => {
                    let text = cell.innerText.replace(/(\r\n|\n|\r)/gm, ' ').replace(/"/g, '""').trim();
                    rowData.push(`"${text}"`);
                });
                csvContent += rowData.join(',') + '\r\n';
            });
            csvContent += '\r\n';
        });

        const encodedUri = encodeURI(csvContent);
        const link = document.createElement('a');
        const filename = `Laporan_Posyandu_{{ \Illuminate\Support\Str::slug($tapos->nama) }}_{{ \Illuminate\Support\Str::slug($parsedPeriode['label']) }}.csv`;
        link.setAttribute('href', encodedUri);
        link.setAttribute('download', filename);
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }
</script>
@endsection
