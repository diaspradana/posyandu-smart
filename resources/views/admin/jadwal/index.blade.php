@extends('layouts.app')

@section('title', 'Jadwal Posyandu - Admin Puskesmas')
@section('page-title', 'Jadwal Kegiatan Posyandu')

@section('content')

{{-- PAGE HEADER --}}
<div class="page-heading">
    <div>
        <span class="eyebrow">KEGIATAN POSYANDU</span>
        <h1>📅 Jadwal Kegiatan Posyandu</h1>
        <p>Atur dan kelola agenda kegiatan posyandu seluruh Tapos wilayah kerja Puskesmas.</p>
    </div>

    <div style="display: flex; gap: 10px; align-items: center;">
        <button type="button" class="btn-primary" onclick="openCreateModal()">
            <span>+</span>
            <span>Buat Jadwal</span>
        </button>
    </div>
</div>

{{-- ALERT NOTIFIKASI SUCCESS / INFO / ERROR --}}
@if(session('success'))
    <div class="alert alert-success" style="background: #ecfdf5; border-left: 4px solid #10b981; color: #065f46; padding: 14px 18px; border-radius: 8px; margin-bottom: 20px; display: flex; align-items: center; justify-content: space-between;">
        <div style="display: flex; align-items: center; gap: 10px;">
            <span style="font-size: 20px;">✅</span>
            <span style="font-weight: 500;">{{ session('success') }}</span>
        </div>
        <button type="button" onclick="this.parentElement.remove()" style="background: none; border: none; font-size: 16px; cursor: pointer; color: #065f46;">&times;</button>
    </div>
@endif

@if(session('info'))
    <div class="alert alert-info" style="background: #eff6ff; border-left: 4px solid #3b82f6; color: #1e40af; padding: 14px 18px; border-radius: 8px; margin-bottom: 20px; display: flex; align-items: center; justify-content: space-between;">
        <div style="display: flex; align-items: center; gap: 10px;">
            <span style="font-size: 20px;">ℹ️</span>
            <span style="font-weight: 500;">{{ session('info') }}</span>
        </div>
        <button type="button" onclick="this.parentElement.remove()" style="background: none; border: none; font-size: 16px; cursor: pointer; color: #1e40af;">&times;</button>
    </div>
@endif

{{-- BANNER PERMINTAAN PERUBAHAN JADWAL DARI KADER --}}
@if($usulanPerubahanList->count() > 0)
    <div style="background: linear-gradient(135deg, #fffbeb 0%, #fef3c7 100%); border: 1px solid #fde68a; border-radius: 12px; padding: 18px 20px; margin-bottom: 24px; box-shadow: 0 4px 6px -1px rgba(245, 158, 11, 0.1);">
        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 12px; border-bottom: 1px dashed #fcd34d; padding-bottom: 8px;">
            <div style="display: flex; align-items: center; gap: 10px;">
                <span style="font-size: 24px;">⚠️</span>
                <div>
                    <strong style="color: #92400e; font-size: 15px;">Permintaan Perubahan Jadwal Memerlukan Persetujuan</strong>
                    <div style="font-size: 12px; color: #b45309;">Ada {{ $usulanPerubahanList->count() }} usulan penyesuaian jadwal dari Kader Tapos.</div>
                </div>
            </div>
            <span class="badge-status" style="background: #f59e0b; color: #fff; font-weight: 700; font-size: 11px;">
                {{ $usulanPerubahanList->count() }} Perlu Respon
            </span>
        </div>

        <div style="display: flex; flex-direction: column; gap: 12px;">
            @foreach($usulanPerubahanList as $item)
                <div style="background: #ffffff; border-radius: 8px; padding: 14px 16px; border: 1px solid #fef08a; display: flex; flex-wrap: wrap; justify-content: space-between; align-items: center; gap: 12px;">
                    <div style="flex: 1; min-width: 280px;">
                        <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 4px;">
                            <span style="font-weight: 700; color: #1e293b;">{{ $item->tapos->nama ?? 'Tapos' }}</span>
                            <span style="font-size: 12px; color: #64748b;">• {{ $item->nama_kegiatan }}</span>
                        </div>
                        <div style="font-size: 12px; color: #475569; margin-bottom: 6px;">
                            <span style="color: #dc2626; text-decoration: line-through;">
                                📅 {{ \Carbon\Carbon::parse($item->tanggal)->isoFormat('D MMM Y') }} ({{ substr($item->waktu_mulai, 0, 5) }} - {{ substr($item->waktu_selesai, 0, 5) }})
                            </span>
                            <span style="margin: 0 6px;">➜</span>
                            <span style="color: #059669; font-weight: 600;">
                                📅 {{ $item->usulan_tanggal ? \Carbon\Carbon::parse($item->usulan_tanggal)->isoFormat('D MMM Y') : \Carbon\Carbon::parse($item->tanggal)->isoFormat('D MMM Y') }}
                                ({{ substr($item->usulan_waktu_mulai ?? $item->waktu_mulai, 0, 5) }} - {{ substr($item->usulan_waktu_selesai ?? $item->waktu_selesai, 0, 5) }})
                            </span>
                        </div>
                        <div style="font-size: 12px; background: #fffbeb; padding: 6px 10px; border-radius: 6px; border-left: 3px solid #f59e0b; color: #78350f;">
                            <strong>Alasan Kader:</strong> "{{ $item->alasan_perubahan }}"
                        </div>
                    </div>

                    <div style="display: flex; gap: 8px; align-items: center;">
                        <form method="POST" action="{{ route('admin.jadwal.setujui-perubahan', $item) }}" onsubmit="return confirm('Apakah Anda yakin menyetujui usulan perubahan jadwal ini?')">
                            @csrf
                            <button type="submit" class="btn-action edit" style="background: #10b981; color: white; border-color: #10b981; font-weight: 600; padding: 6px 14px;">
                                ✓ Setujui Perubahan
                            </button>
                        </form>

                        <button type="button" class="btn-action delete" style="background: #ef4444; color: white; border-color: #ef4444; font-weight: 600; padding: 6px 14px;" onclick="openRejectModal({{ $item->id }}, '{{ addslashes($item->tapos->nama ?? 'Tapos') }}', '{{ addslashes($item->nama_kegiatan) }}')">
                            ✕ Tolak
                        </button>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endif

{{-- STATS OVERVIEW CARDS --}}
<div class="stats-grid" style="grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); margin-bottom: 24px;">
    <div class="stat-card">
        <div class="stat-card-header">
            <span class="stat-label">TOTAL JADWAL</span>
            <div class="stat-icon-wrap blue">📅</div>
        </div>
        <div class="stat-value">{{ $totalJadwal }}</div>
        <div class="stat-caption">Agenda kegiatan aktif</div>
    </div>

    <div class="stat-card">
        <div class="stat-card-header">
            <span class="stat-label">KADER SIAP</span>
            <div class="stat-icon-wrap green">🟢</div>
        </div>
        <div class="stat-value" style="color: #10b981;">{{ $siapCount }}</div>
        <div class="stat-caption">Telah dikonfirmasi kader</div>
    </div>

    <div class="stat-card">
        <div class="stat-card-header">
            <span class="stat-label">MENUNGGU KONFIRMASI</span>
            <div class="stat-icon-wrap yellow">⏳</div>
        </div>
        <div class="stat-value" style="color: #f59e0b;">{{ $menungguCount }}</div>
        <div class="stat-caption">Belum direspon kader</div>
    </div>

    <div class="stat-card">
        <div class="stat-card-header">
            <span class="stat-label">USULAN PERUBAHAN</span>
            <div class="stat-icon-wrap purple">📝</div>
        </div>
        <div class="stat-value" style="color: #8b5cf6;">{{ $usulanCount }}</div>
        <div class="stat-caption">Menunggu review admin</div>
    </div>
</div>

{{-- SEARCH & FILTER BAR --}}
<div class="dashboard-card" style="padding: 16px 20px; margin-bottom: 20px;">
    <form method="GET" action="{{ route('admin.jadwal.index') }}" class="table-toolbar" style="margin-bottom: 0; display: flex; flex-wrap: wrap; gap: 12px; align-items: center; justify-content: space-between;">
        <div class="search-input-wrap" style="flex: 1; min-width: 240px;">
            <span class="search-icon">🔍</span>
            <input
                type="text"
                name="search"
                value="{{ request('search') }}"
                placeholder="Cari kegiatan, lokasi, atau tapos..."
            >
        </div>

        <div style="display: flex; gap: 10px; flex-wrap: wrap; align-items: center;">
            {{-- Filter Tapos --}}
            <select name="tapos_id" class="custom-select" onchange="this.form.submit()">
                <option value="semua">Semua Tapos</option>
                @foreach($taposList as $t)
                    <option value="{{ $t->id }}" @selected(request('tapos_id') == $t->id)>
                        {{ $t->nama }}
                    </option>
                @endforeach
            </select>

            {{-- Filter Bulan --}}
            <select name="bulan" class="custom-select" onchange="this.form.submit()">
                <option value="semua">Semua Bulan</option>
                @for($m = 1; $m <= 12; $m++)
                    <option value="{{ $m }}" @selected(request('bulan') == $m)>
                        {{ \Carbon\Carbon::create(2026, $m, 1)->isoFormat('MMMM') }}
                    </option>
                @endfor
            </select>

            {{-- Filter Status --}}
            <select name="status" class="custom-select" onchange="this.form.submit()">
                <option value="semua">Semua Status</option>
                <option value="siap" @selected(request('status') === 'siap')>🟢 Kader Siap</option>
                <option value="menunggu_konfirmasi" @selected(request('status') === 'menunggu_konfirmasi')>⏳ Menunggu Konfirmasi</option>
                <option value="usulan_perubahan" @selected(request('status') === 'usulan_perubahan')>🟡 Usulan Perubahan</option>
                <option value="selesai" @selected(request('status') === 'selesai')>🔵 Selesai</option>
                <option value="dibatalkan" @selected(request('status') === 'dibatalkan')>🔴 Dibatalkan</option>
            </select>

            <button type="submit" class="filter-tab active" style="padding: 8px 16px; border: none; cursor: pointer;">
                Terapkan
            </button>

            @if(request()->hasAny(['search', 'tapos_id', 'bulan', 'status']))
                <a href="{{ route('admin.jadwal.index') }}" class="filter-tab" style="background: #e2e8f0; padding: 8px 14px; text-decoration: none; color: #475569;">
                    Reset Filter
                </a>
            @endif
        </div>
    </form>
</div>

{{-- TABEL JADWAL POSYANDU --}}
<div class="dashboard-card" style="padding: 0; overflow: hidden; margin-bottom: 24px;">
    <div class="table-responsive">
        <table class="custom-table">
            <thead>
                <tr>
                    <th style="width: 180px;">Tapos</th>
                    <th>Jenis Kegiatan</th>
                    <th style="width: 210px;">Tanggal & Waktu</th>
                    <th>Lokasi</th>
                    <th style="text-align: center; width: 190px;">Status Konfirmasi</th>
                    <th style="text-align: right; width: 150px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($jadwals as $j)
                    <tr>
                        <td>
                            <strong style="color: #0f172a; font-size: 14px;">{{ $j->tapos->nama ?? '-' }}</strong>
                            <div style="font-size: 11px; color: #64748b;">
                                {{ $j->tapos->kelurahan ?? 'Wilayah Puskesmas' }}
                            </div>
                        </td>
                        <td>
                            <div style="font-weight: 600; color: #1e293b;">
                                {{ $j->nama_kegiatan }}
                            </div>
                            @if($j->catatan)
                                <div style="font-size: 11px; color: #64748b; margin-top: 2px;">
                                    📝 {{ Str::limit($j->catatan, 45) }}
                                </div>
                            @endif
                        </td>
                        <td>
                            <div style="font-weight: 600; color: #0f172a;">
                                📅 {{ \Carbon\Carbon::parse($j->tanggal)->isoFormat('dddd, D MMMM Y') }}
                            </div>
                            <div style="font-size: 12px; color: #64748b;">
                                ⏰ {{ substr($j->waktu_mulai, 0, 5) }} – {{ substr($j->waktu_selesai, 0, 5) }} WIB
                            </div>
                        </td>
                        <td>
                            <div style="font-size: 13px; color: #334155;">
                                📍 {{ $j->lokasi ?? ($j->tapos->nama ?? 'Balai RW') }}
                            </div>
                        </td>
                        <td style="text-align: center;">
                            @if($j->status === 'selesai')
                                <span class="badge-status" style="background: #e0f2fe; color: #0369a1; border: 1px solid #bae6fd;">
                                    🔵 Selesai
                                </span>
                            @elseif($j->status === 'dibatalkan')
                                <span class="badge-status tidak_aktif">
                                    🔴 Dibatalkan
                                </span>
                            @else
                                @if($j->status_konfirmasi === 'siap')
                                    <span class="badge-status aktif">
                                        🟢 Kader Siap
                                    </span>
                                @elseif($j->status_konfirmasi === 'usulan_perubahan')
                                    <span class="badge-status" style="background: #fef3c7; color: #b45309; border: 1px solid #fde68a;">
                                        🟡 Usulan Perubahan
                                    </span>
                                @elseif($j->status_konfirmasi === 'ditolak')
                                    <span class="badge-status" style="background: #fee2e2; color: #b91c1c; border: 1px solid #fecaca;">
                                        🔴 Usulan Ditolak
                                    </span>
                                @else
                                    <span class="badge-status" style="background: #f1f5f9; color: #475569; border: 1px solid #cbd5e1;">
                                        ⏳ Menunggu Respon
                                    </span>
                                @endif
                            @endif
                        </td>
                        <td style="text-align: right;">
                            <div class="actions-group" style="justify-content: flex-end;">
                                <button type="button" class="btn-action view" onclick="openDetailModal({{ json_encode($j) }}, '{{ addslashes($j->tapos->nama ?? '-') }}')" title="Lihat Detail">
                                    Detail
                                </button>

                                <button type="button" class="btn-action edit" onclick="openEditModal({{ json_encode($j) }})" title="Edit Jadwal">
                                    Edit
                                </button>

                                <form method="POST" action="{{ route('admin.jadwal.destroy', $j) }}" style="display: inline;" onsubmit="return confirm('Hapus jadwal ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-action delete" title="Hapus Jadwal">
                                        Hapus
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="text-align: center; padding: 40px 16px; color: #94a3b8;">
                            <div style="font-size: 32px; margin-bottom: 8px;">📅</div>
                            <strong style="color: #475569; font-size: 14px;">Belum Ada Jadwal Posyandu</strong>
                            <p style="margin: 4px 0 16px 0; font-size: 12px; color: #94a3b8;">Klik tombol "Buat Jadwal" untuk menerbitkan agenda kegiatan posyandu bagi kader.</p>
                            <button type="button" class="btn-primary" style="margin: 0 auto; padding: 8px 16px;" onclick="openCreateModal()">
                                + Buat Jadwal Sekarang
                            </button>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div style="margin-top: 16px;">
    {{ $jadwals->links() }}
</div>

{{-- ==================== MODAL BUAT JADWAL ==================== --}}
<div id="createModal" class="modal-backdrop" style="display: none; position: fixed; inset: 0; background: rgba(15, 23, 42, 0.6); z-index: 9999; backdrop-filter: blur(4px); align-items: center; justify-content: center;">
    <div class="modal-dialog" style="background: white; border-radius: 16px; width: 100%; max-width: 560px; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25); overflow: hidden; animation: fadeIn 0.2s ease-out; margin: 20px;">
        <div style="padding: 20px 24px; border-bottom: 1px solid #e2e8f0; display: flex; align-items: center; justify-content: space-between; background: #f8fafc;">
            <div style="display: flex; align-items: center; gap: 10px;">
                <span style="font-size: 22px;">📅</span>
                <div>
                    <h3 style="margin: 0; font-size: 16px; font-weight: 700; color: #0f172a;">BUAT JADWAL POSYANDU</h3>
                    <span style="font-size: 12px; color: #64748b;">Jadwal akan otomatis terkirim ke akun Kader Tapos terpilih</span>
                </div>
            </div>
            <button type="button" onclick="closeCreateModal()" style="background: none; border: none; font-size: 20px; color: #94a3b8; cursor: pointer;">&times;</button>
        </div>

        <form method="POST" action="{{ route('admin.jadwal.store') }}" style="padding: 24px;">
            @csrf

            {{-- Tapos --}}
            <div class="form-group" style="margin-bottom: 16px;">
                <label class="form-label" style="display: block; font-weight: 600; font-size: 13px; color: #334155; margin-bottom: 6px;">
                    Tapos <span style="color: #ef4444;">*</span>
                </label>
                <select name="tapos_id" class="form-input custom-select" required style="width: 100%; padding: 10px 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 14px;">
                    <option value="">-- Pilih Tapos Sasaran --</option>
                    @foreach($taposList as $t)
                        <option value="{{ $t->id }}" @selected(old('tapos_id') == $t->id)>
                            {{ $t->nama }} ({{ $t->kelurahan ?? 'Kelurahan Sukamaju' }})
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Jenis Kegiatan --}}
            <div class="form-group" style="margin-bottom: 16px;">
                <label class="form-label" style="display: block; font-weight: 600; font-size: 13px; color: #334155; margin-bottom: 6px;">
                    Jenis Kegiatan <span style="color: #ef4444;">*</span>
                </label>
                <select name="jenis_kegiatan" class="form-input custom-select" required style="width: 100%; padding: 10px 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 14px;">
                    <option value="Posyandu Balita">👶 Posyandu Balita & Penimbangan Rutin</option>
                    <option value="Posyandu Ibu Hamil">🤰 Posyandu Ibu Hamil & Edukasi KIA</option>
                    <option value="Posyandu Integrasi (Balita & Ibu Hamil)">🏥 Posyandu Integrasi (Balita & Ibu Hamil)</option>
                </select>
            </div>

            {{-- Tanggal --}}
            <div class="form-group" style="margin-bottom: 16px;">
                <label class="form-label" style="display: block; font-weight: 600; font-size: 13px; color: #334155; margin-bottom: 6px;">
                    Tanggal Pelaksanaan <span style="color: #ef4444;">*</span>
                </label>
                <input type="date" name="tanggal" required value="{{ old('tanggal', '2026-09-05') }}" style="width: 100%; padding: 10px 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 14px;">
            </div>

            {{-- Waktu Mulai & Selesai --}}
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px; margin-bottom: 16px;">
                <div class="form-group">
                    <label class="form-label" style="display: block; font-weight: 600; font-size: 13px; color: #334155; margin-bottom: 6px;">
                        Waktu Mulai <span style="color: #ef4444;">*</span>
                    </label>
                    <input type="time" name="waktu_mulai" required value="{{ old('waktu_mulai', '08:00') }}" style="width: 100%; padding: 10px 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 14px;">
                </div>

                <div class="form-group">
                    <label class="form-label" style="display: block; font-weight: 600; font-size: 13px; color: #334155; margin-bottom: 6px;">
                        Waktu Selesai <span style="color: #ef4444;">*</span>
                    </label>
                    <input type="time" name="waktu_selesai" required value="{{ old('waktu_selesai', '11:00') }}" style="width: 100%; padding: 10px 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 14px;">
                </div>
            </div>

            {{-- Lokasi --}}
            <div class="form-group" style="margin-bottom: 16px;">
                <label class="form-label" style="display: block; font-weight: 600; font-size: 13px; color: #334155; margin-bottom: 6px;">
                    Lokasi Pelaksanaan <span style="color: #ef4444;">*</span>
                </label>
                <input type="text" name="lokasi" required placeholder="Contoh: Balai RW 05 / Pos RW Melati" value="{{ old('lokasi', 'Balai RW 05') }}" style="width: 100%; padding: 10px 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 14px;">
            </div>

            {{-- Catatan --}}
            <div class="form-group" style="margin-bottom: 24px;">
                <label class="form-label" style="display: block; font-weight: 600; font-size: 13px; color: #334155; margin-bottom: 6px;">
                    Catatan untuk Kader (Opsional)
                </label>
                <textarea name="catatan" rows="3" placeholder="Contoh: Harap siapkan timbangan dacin, pengukur tinggi badan, dan KMS lengkap." style="width: 100%; padding: 10px 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 14px; resize: vertical;">{{ old('catatan') }}</textarea>
            </div>

            <div style="display: flex; gap: 10px; justify-content: flex-end;">
                <button type="button" onclick="closeCreateModal()" class="btn-action" style="padding: 10px 20px; font-size: 14px; border: 1px solid #cbd5e1; background: #fff; cursor: pointer; border-radius: 8px;">
                    Batal
                </button>
                <button type="submit" class="btn-primary" style="padding: 10px 24px; font-size: 14px;">
                    Simpan Jadwal
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ==================== MODAL EDIT JADWAL ==================== --}}
<div id="editModal" class="modal-backdrop" style="display: none; position: fixed; inset: 0; background: rgba(15, 23, 42, 0.6); z-index: 9999; backdrop-filter: blur(4px); align-items: center; justify-content: center;">
    <div class="modal-dialog" style="background: white; border-radius: 16px; width: 100%; max-width: 560px; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25); overflow: hidden; margin: 20px;">
        <div style="padding: 20px 24px; border-bottom: 1px solid #e2e8f0; display: flex; align-items: center; justify-content: space-between; background: #f8fafc;">
            <div style="display: flex; align-items: center; gap: 10px;">
                <span style="font-size: 22px;">✏️</span>
                <div>
                    <h3 style="margin: 0; font-size: 16px; font-weight: 700; color: #0f172a;">EDIT JADWAL POSYANDU</h3>
                    <span style="font-size: 12px; color: #64748b;">Perbarui rincian kegiatan posyandu</span>
                </div>
            </div>
            <button type="button" onclick="closeEditModal()" style="background: none; border: none; font-size: 20px; color: #94a3b8; cursor: pointer;">&times;</button>
        </div>

        <form id="editForm" method="POST" action="" style="padding: 24px;">
            @csrf
            @method('PUT')

            <div class="form-group" style="margin-bottom: 16px;">
                <label class="form-label" style="display: block; font-weight: 600; font-size: 13px; color: #334155; margin-bottom: 6px;">Tapos</label>
                <select name="tapos_id" id="edit_tapos_id" class="form-input custom-select" required style="width: 100%; padding: 10px 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 14px;">
                    @foreach($taposList as $t)
                        <option value="{{ $t->id }}">{{ $t->nama }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group" style="margin-bottom: 16px;">
                <label class="form-label" style="display: block; font-weight: 600; font-size: 13px; color: #334155; margin-bottom: 6px;">Jenis Kegiatan</label>
                <input type="text" name="jenis_kegiatan" id="edit_jenis_kegiatan" required style="width: 100%; padding: 10px 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 14px;">
            </div>

            <div class="form-group" style="margin-bottom: 16px;">
                <label class="form-label" style="display: block; font-weight: 600; font-size: 13px; color: #334155; margin-bottom: 6px;">Tanggal Pelaksanaan</label>
                <input type="date" name="tanggal" id="edit_tanggal" required style="width: 100%; padding: 10px 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 14px;">
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px; margin-bottom: 16px;">
                <div class="form-group">
                    <label class="form-label" style="display: block; font-weight: 600; font-size: 13px; color: #334155; margin-bottom: 6px;">Waktu Mulai</label>
                    <input type="time" name="waktu_mulai" id="edit_waktu_mulai" required style="width: 100%; padding: 10px 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 14px;">
                </div>

                <div class="form-group">
                    <label class="form-label" style="display: block; font-weight: 600; font-size: 13px; color: #334155; margin-bottom: 6px;">Waktu Selesai</label>
                    <input type="time" name="waktu_selesai" id="edit_waktu_selesai" required style="width: 100%; padding: 10px 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 14px;">
                </div>
            </div>

            <div class="form-group" style="margin-bottom: 16px;">
                <label class="form-label" style="display: block; font-weight: 600; font-size: 13px; color: #334155; margin-bottom: 6px;">Lokasi</label>
                <input type="text" name="lokasi" id="edit_lokasi" required style="width: 100%; padding: 10px 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 14px;">
            </div>

            <div class="form-group" style="margin-bottom: 16px;">
                <label class="form-label" style="display: block; font-weight: 600; font-size: 13px; color: #334155; margin-bottom: 6px;">Status Kegiatan</label>
                <select name="status" id="edit_status" class="form-input custom-select" required style="width: 100%; padding: 10px 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 14px;">
                    <option value="mendatang">⚡ Mendatang / Terjadwal</option>
                    <option value="selesai">🔵 Selesai Dilaksanakan</option>
                    <option value="dibatalkan">🔴 Dibatalkan</option>
                </select>
            </div>

            <div class="form-group" style="margin-bottom: 24px;">
                <label class="form-label" style="display: block; font-weight: 600; font-size: 13px; color: #334155; margin-bottom: 6px;">Catatan</label>
                <textarea name="catatan" id="edit_catatan" rows="3" style="width: 100%; padding: 10px 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 14px;"></textarea>
            </div>

            <div style="display: flex; gap: 10px; justify-content: flex-end;">
                <button type="button" onclick="closeEditModal()" class="btn-action" style="padding: 10px 20px; font-size: 14px; border: 1px solid #cbd5e1; background: #fff; cursor: pointer; border-radius: 8px;">
                    Batal
                </button>
                <button type="submit" class="btn-primary" style="padding: 10px 24px; font-size: 14px;">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ==================== MODAL DETAIL JADWAL ==================== --}}
<div id="detailModal" class="modal-backdrop" style="display: none; position: fixed; inset: 0; background: rgba(15, 23, 42, 0.6); z-index: 9999; backdrop-filter: blur(4px); align-items: center; justify-content: center;">
    <div class="modal-dialog" style="background: white; border-radius: 16px; width: 100%; max-width: 520px; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25); overflow: hidden; margin: 20px;">
        <div style="padding: 20px 24px; border-bottom: 1px solid #e2e8f0; display: flex; align-items: center; justify-content: space-between; background: #f8fafc;">
            <div style="display: flex; align-items: center; gap: 10px;">
                <span style="font-size: 22px;">📋</span>
                <div>
                    <h3 style="margin: 0; font-size: 16px; font-weight: 700; color: #0f172a;" id="dt_title">Rincian Jadwal Kegiatan</h3>
                    <span style="font-size: 12px; color: #64748b;" id="dt_tapos">Tapos</span>
                </div>
            </div>
            <button type="button" onclick="closeDetailModal()" style="background: none; border: none; font-size: 20px; color: #94a3b8; cursor: pointer;">&times;</button>
        </div>

        <div style="padding: 24px; font-size: 14px; color: #334155; line-height: 1.6;">
            <div style="margin-bottom: 12px; background: #f1f5f9; padding: 12px; border-radius: 8px;">
                <div style="font-size: 12px; color: #64748b;">Status Konfirmasi Kader:</div>
                <div id="dt_status_konfirmasi" style="font-weight: 700; margin-top: 2px;">-</div>
            </div>

            <div style="display: grid; grid-template-columns: 120px 1fr; gap: 8px; margin-bottom: 8px;">
                <span style="color: #64748b;">📅 Tanggal:</span>
                <strong id="dt_tanggal">-</strong>
            </div>

            <div style="display: grid; grid-template-columns: 120px 1fr; gap: 8px; margin-bottom: 8px;">
                <span style="color: #64748b;">⏰ Waktu:</span>
                <strong id="dt_waktu">-</strong>
            </div>

            <div style="display: grid; grid-template-columns: 120px 1fr; gap: 8px; margin-bottom: 8px;">
                <span style="color: #64748b;">📍 Lokasi:</span>
                <span id="dt_lokasi">-</span>
            </div>

            <div style="display: grid; grid-template-columns: 120px 1fr; gap: 8px; margin-bottom: 12px;">
                <span style="color: #64748b;">📝 Catatan:</span>
                <span id="dt_catatan" style="font-style: italic;">-</span>
            </div>

            <div id="dt_usulan_box" style="display: none; background: #fffbeb; border: 1px solid #fde68a; border-radius: 8px; padding: 12px; margin-top: 12px;">
                <strong style="color: #92400e; font-size: 13px;">Keterangan Usulan Kader:</strong>
                <div id="dt_alasan_perubahan" style="font-size: 13px; color: #78350f; margin-top: 4px;"></div>
            </div>
        </div>

        <div style="padding: 16px 24px; background: #f8fafc; border-top: 1px solid #e2e8f0; display: flex; justify-content: flex-end;">
            <button type="button" onclick="closeDetailModal()" class="btn-primary" style="padding: 8px 20px; font-size: 13px;">
                Tutup
            </button>
        </div>
    </div>
</div>

{{-- ==================== MODAL TOLAK USULAN ==================== --}}
<div id="rejectModal" class="modal-backdrop" style="display: none; position: fixed; inset: 0; background: rgba(15, 23, 42, 0.6); z-index: 9999; backdrop-filter: blur(4px); align-items: center; justify-content: center;">
    <div class="modal-dialog" style="background: white; border-radius: 16px; width: 100%; max-width: 480px; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25); overflow: hidden; margin: 20px;">
        <div style="padding: 20px 24px; border-bottom: 1px solid #e2e8f0; display: flex; align-items: center; justify-content: space-between; background: #fef2f2;">
            <div style="display: flex; align-items: center; gap: 10px;">
                <span style="font-size: 22px;">✕</span>
                <div>
                    <h3 style="margin: 0; font-size: 16px; font-weight: 700; color: #991b1b;">Tolak Usulan Perubahan Jadwal</h3>
                    <span style="font-size: 12px; color: #b91c1c;" id="reject_target_info">Tapos Melati</span>
                </div>
            </div>
            <button type="button" onclick="closeRejectModal()" style="background: none; border: none; font-size: 20px; color: #94a3b8; cursor: pointer;">&times;</button>
        </div>

        <form id="rejectForm" method="POST" action="" style="padding: 24px;">
            @csrf

            <div class="form-group" style="margin-bottom: 20px;">
                <label class="form-label" style="display: block; font-weight: 600; font-size: 13px; color: #334155; margin-bottom: 6px;">
                    Alasan Penolakan untuk Kader
                </label>
                <textarea name="alasan_penolakan" rows="3" required placeholder="Contoh: Jadwal tidak dapat diubah karena dokter puskesmas hanya tersedia pada tanggal tersebut." style="width: 100%; padding: 10px 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 14px; resize: vertical;">Jadwal utama tidak dapat diubah karena ketersediaan tenaga medis puskesmas.</textarea>
            </div>

            <div style="display: flex; gap: 10px; justify-content: flex-end;">
                <button type="button" onclick="closeRejectModal()" class="btn-action" style="padding: 10px 20px; font-size: 14px; border: 1px solid #cbd5e1; background: #fff; cursor: pointer; border-radius: 8px;">
                    Batal
                </button>
                <button type="submit" class="btn-action delete" style="padding: 10px 24px; font-size: 14px; background: #ef4444; color: white; border: none; border-radius: 8px; font-weight: 600;">
                    Kirim Penolakan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function openCreateModal() {
        document.getElementById('createModal').style.display = 'flex';
    }

    function closeCreateModal() {
        document.getElementById('createModal').style.display = 'none';
    }

    function openEditModal(jadwal) {
        document.getElementById('editForm').action = `/admin/jadwal/${jadwal.id}`;
        document.getElementById('edit_tapos_id').value = jadwal.tapos_id;
        document.getElementById('edit_jenis_kegiatan').value = jadwal.nama_kegiatan;
        document.getElementById('edit_tanggal').value = (jadwal.tanggal || '').substring(0, 10);
        document.getElementById('edit_waktu_mulai').value = (jadwal.waktu_mulai || '').substring(0, 5);
        document.getElementById('edit_waktu_selesai').value = (jadwal.waktu_selesai || '').substring(0, 5);
        document.getElementById('edit_lokasi').value = jadwal.lokasi || '';
        document.getElementById('edit_status').value = jadwal.status || 'mendatang';
        document.getElementById('edit_catatan').value = jadwal.catatan || '';
        document.getElementById('editModal').style.display = 'flex';
    }

    function closeEditModal() {
        document.getElementById('editModal').style.display = 'none';
    }

    function openDetailModal(jadwal, taposNama) {
        document.getElementById('dt_title').innerText = jadwal.nama_kegiatan;
        document.getElementById('dt_tapos').innerText = taposNama;
        document.getElementById('dt_tanggal').innerText = (jadwal.tanggal || '').substring(0, 10);
        document.getElementById('dt_waktu').innerText = (jadwal.waktu_mulai || '').substring(0, 5) + ' - ' + (jadwal.waktu_selesai || '').substring(0, 5) + ' WIB';
        document.getElementById('dt_lokasi').innerText = jadwal.lokasi || taposNama;
        document.getElementById('dt_catatan').innerText = jadwal.catatan ? `"${jadwal.catatan}"` : 'Tidak ada catatan khusus.';

        let statusText = '⏳ Menunggu Konfirmasi Kader';
        if (jadwal.status_konfirmasi === 'siap') {
            statusText = '🟢 Kader Siap Melaksanakan';
        } else if (jadwal.status_konfirmasi === 'usulan_perubahan') {
            statusText = '🟡 Kader Mengajukan Perubahan Jadwal';
        } else if (jadwal.status_konfirmasi === 'ditolak') {
            statusText = '🔴 Usulan Perubahan Ditolak Admin';
        }
        document.getElementById('dt_status_konfirmasi').innerText = statusText;

        if (jadwal.status_konfirmasi === 'usulan_perubahan' && jadwal.alasan_perubahan) {
            document.getElementById('dt_usulan_box').style.display = 'block';
            document.getElementById('dt_alasan_perubahan').innerText = `"${jadwal.alasan_perubahan}"`;
        } else {
            document.getElementById('dt_usulan_box').style.display = 'none';
        }

        document.getElementById('detailModal').style.display = 'flex';
    }

    function closeDetailModal() {
        document.getElementById('detailModal').style.display = 'none';
    }

    function openRejectModal(id, taposNama, kegiatan) {
        document.getElementById('rejectForm').action = `/admin/jadwal/${id}/tolak-perubahan`;
        document.getElementById('reject_target_info').innerText = `${taposNama} — ${kegiatan}`;
        document.getElementById('rejectModal').style.display = 'flex';
    }

    function closeRejectModal() {
        document.getElementById('rejectModal').style.display = 'none';
    }

    // Close modals on clicking backdrop
    window.onclick = function(event) {
        if (event.target.classList.contains('modal-backdrop')) {
            event.target.style.display = 'none';
        }
    }
</script>

@endsection
