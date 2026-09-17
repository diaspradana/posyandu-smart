@extends('layouts.app')

@section('title', 'Jadwal Kegiatan Posyandu - Kader')
@section('page-title', 'Jadwal Kegiatan Posyandu')

@section('content')

{{-- PAGE HEADER --}}
<div class="page-heading">
    <div>
        <span class="eyebrow">AGENDA & KOORDINASI POSYANDU</span>
        <h1>📅 Jadwal Kegiatan Posyandu — {{ $tapos->nama }}</h1>
        <p>Jadwal resmi diterbitkan oleh Admin Puskesmas. Konfirmasikan kesiapan atau ajukan penyesuaian bila terdapat kendala teknis/lokasi.</p>
    </div>

    <div style="display: flex; gap: 8px; flex-wrap: wrap;">
        <a href="{{ route('kader.kegiatan.pemeriksaan') }}" class="btn-primary">
            <span>🩺</span>
            <span>Mulai Pemeriksaan</span>
        </a>
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

{{-- WORKFLOW BANNER GUIDE --}}
<div style="background: linear-gradient(135deg, #f0fdf4 0%, #e0f2fe 100%); border: 1px solid #bae6fd; border-radius: 12px; padding: 16px 20px; margin-bottom: 24px;">
    <div style="display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 12px;">
        <div style="display: flex; align-items: center; gap: 12px;">
            <div style="background: #0284c7; color: white; width: 40px; height: 40px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                🏥
            </div>
            <div>
                <strong style="color: #0369a1; font-size: 14px;">Alur Koordinasi Puskesmas & Kader Posyandu</strong>
                <div style="font-size: 12px; color: #475569; margin-top: 2px;">
                    Jadwal utama ditentukan Puskesmas ➜ Kader melakukan konfirmasi kesiapan atau mengajukan usulan penyesuaian jika lokasi/tanggal bentrok.
                </div>
            </div>
        </div>

        @if($pendingConfirmationCount > 0)
            <div style="background: #fef3c7; border: 1px solid #fde68a; color: #92400e; padding: 6px 14px; border-radius: 20px; font-size: 12px; font-weight: 700; display: flex; align-items: center; gap: 6px;">
                <span>⏳</span>
                <span>{{ $pendingConfirmationCount }} Jadwal Perlu Konfirmasi</span>
            </div>
        @else
            <div style="background: #dcfce7; border: 1px solid #86efac; color: #166534; padding: 6px 14px; border-radius: 20px; font-size: 12px; font-weight: 700; display: flex; align-items: center; gap: 6px;">
                <span>🟢</span>
                <span>Semua Jadwal Terkonfirmasi</span>
            </div>
        @endif
    </div>
</div>

{{-- STATUS TABS FILTER --}}
<div style="display: flex; gap: 8px; flex-wrap: wrap; margin-bottom: 20px;">
    <a href="{{ route('kader.kegiatan.jadwal') }}" class="filter-tab {{ !request('status') || request('status') === 'semua' ? 'active' : '' }}" style="text-decoration: none; padding: 8px 16px;">
        Semua Jadwal
    </a>
    <a href="{{ route('kader.kegiatan.jadwal', ['status' => 'menunggu_konfirmasi']) }}" class="filter-tab {{ request('status') === 'menunggu_konfirmasi' ? 'active' : '' }}" style="text-decoration: none; padding: 8px 16px;">
        ⏳ Perlu Konfirmasi
    </a>
    <a href="{{ route('kader.kegiatan.jadwal', ['status' => 'siap']) }}" class="filter-tab {{ request('status') === 'siap' ? 'active' : '' }}" style="text-decoration: none; padding: 8px 16px;">
        🟢 Kader Siap
    </a>
    <a href="{{ route('kader.kegiatan.jadwal', ['status' => 'usulan_perubahan']) }}" class="filter-tab {{ request('status') === 'usulan_perubahan' ? 'active' : '' }}" style="text-decoration: none; padding: 8px 16px;">
        🟡 Usulan Perubahan
    </a>
</div>

{{-- JADWAL CARDS GRID --}}
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 20px; margin-bottom: 30px;">
    @forelse($jadwals as $j)
        <div class="dashboard-card" style="padding: 0; overflow: hidden; display: flex; flex-direction: column; justify-content: space-between; border: 1px solid #e2e8f0; border-top: 4px solid {{ $j->status_konfirmasi === 'siap' ? '#10b981' : ($j->status_konfirmasi === 'usulan_perubahan' ? '#f59e0b' : '#3b82f6') }};">
            
            {{-- Card Header --}}
            <div style="padding: 16px 20px; border-bottom: 1px solid #f1f5f9; display: flex; justify-content: space-between; align-items: flex-start; gap: 8px; background: #fafafa;">
                <div>
                    <span style="font-size: 11px; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px;">
                        {{ $tapos->nama }}
                    </span>
                    <h3 style="margin: 4px 0 0 0; font-size: 16px; font-weight: 700; color: #0f172a;">
                        {{ $j->nama_kegiatan }}
                    </h3>
                </div>

                <div>
                    @if($j->status === 'selesai')
                        <span class="badge-status" style="background: #e0f2fe; color: #0369a1;">🔵 Selesai</span>
                    @elseif($j->status_konfirmasi === 'siap')
                        <span class="badge-status aktif">🟢 Kader Siap</span>
                    @elseif($j->status_konfirmasi === 'usulan_perubahan')
                        <span class="badge-status" style="background: #fef3c7; color: #b45309; border: 1px solid #fde68a;">🟡 Menunggu Review</span>
                    @elseif($j->status_konfirmasi === 'ditolak')
                        <span class="badge-status" style="background: #fee2e2; color: #b91c1c; border: 1px solid #fecaca;">🔴 Usulan Ditolak</span>
                    @else
                        <span class="badge-status" style="background: #eff6ff; color: #1e40af; border: 1px solid #bfdbfe;">🟢 Dijadwalkan</span>
                    @endif
                </div>
            </div>

            {{-- Card Body --}}
            <div style="padding: 18px 20px; flex: 1;">
                <div style="display: flex; flex-direction: column; gap: 10px; font-size: 13px; color: #334155;">
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <span style="font-size: 16px;">📅</span>
                        <div>
                            <strong>{{ \Carbon\Carbon::parse($j->tanggal)->isoFormat('dddd, D MMMM Y') }}</strong>
                        </div>
                    </div>

                    <div style="display: flex; align-items: center; gap: 10px;">
                        <span style="font-size: 16px;">⏰</span>
                        <div>
                            <span>{{ substr($j->waktu_mulai, 0, 5) }} – {{ substr($j->waktu_selesai, 0, 5) }} WIB</span>
                        </div>
                    </div>

                    <div style="display: flex; align-items: center; gap: 10px;">
                        <span style="font-size: 16px;">📍</span>
                        <div>
                            <span>{{ $j->lokasi ?? $tapos->nama }}</span>
                        </div>
                    </div>

                    @if($j->catatan)
                        <div style="background: #f8fafc; border-radius: 8px; padding: 10px 12px; border-left: 3px solid #94a3b8; font-size: 12px; color: #475569; margin-top: 4px;">
                            <strong>Catatan Puskesmas:</strong><br>
                            "{{ $j->catatan }}"
                        </div>
                    @endif

                    {{-- Callout jika kader mengajukan usulan perubahan --}}
                    @if($j->status_konfirmasi === 'usulan_perubahan')
                        <div style="background: #fffbeb; border: 1px solid #fde68a; border-radius: 8px; padding: 10px 12px; margin-top: 6px;">
                            <div style="font-weight: 700; color: #92400e; font-size: 12px; margin-bottom: 2px;">
                                ⚠️ Usulan Perubahan Telah Dikirim ke Puskesmas:
                            </div>
                            <div style="font-size: 12px; color: #78350f;">
                                "{{ $j->alasan_perubahan }}"
                            </div>
                            @if($j->usulan_tanggal)
                                <div style="font-size: 11px; color: #b45309; margin-top: 4px;">
                                    Usulan: {{ \Carbon\Carbon::parse($j->usulan_tanggal)->isoFormat('D MMMM Y') }} ({{ substr($j->usulan_waktu_mulai ?? $j->waktu_mulai, 0, 5) }} - {{ substr($j->usulan_waktu_selesai ?? $j->waktu_selesai, 0, 5) }})
                                </div>
                            @endif
                        </div>
                    @endif

                    {{-- Callout jika usulan ditolak admin --}}
                    @if($j->status_konfirmasi === 'ditolak' && $j->alasan_penolakan)
                        <div style="background: #fef2f2; border: 1px solid #fecaca; border-radius: 8px; padding: 10px 12px; margin-top: 6px;">
                            <div style="font-weight: 700; color: #991b1b; font-size: 12px; margin-bottom: 2px;">
                                🔴 Tanggapan Puskesmas:
                            </div>
                            <div style="font-size: 12px; color: #7f1d1d;">
                                "{{ $j->alasan_penolakan }}"
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Card Footer Actions --}}
            <div style="padding: 14px 20px; background: #f8fafc; border-top: 1px solid #f1f5f9; display: flex; flex-direction: column; gap: 8px;">
                @if($j->status_konfirmasi === 'siap')
                    <div style="display: flex; align-items: center; justify-content: space-between; gap: 8px;">
                        <span style="font-size: 12px; color: #166534; font-weight: 600; display: flex; align-items: center; gap: 4px;">
                            <span>🟢</span>
                            <span>Kader siap melaksanakan</span>
                        </span>

                        <a href="{{ route('kader.kegiatan.pemeriksaan') }}" class="btn-action edit" style="background: #10b981; color: white; border-color: #10b981; text-decoration: none; padding: 6px 14px; font-weight: 600; font-size: 12px;">
                            🩺 Buka Pemeriksaan
                        </a>
                    </div>

                    <div style="text-align: right; margin-top: 4px;">
                        <button type="button" onclick="openRescheduleModal({{ json_encode($j) }})" style="background: none; border: none; font-size: 11px; color: #64748b; text-decoration: underline; cursor: pointer;">
                            Ajukan perubahan bila ada kendala mendadak
                        </button>
                    </div>
                @elseif($j->status_konfirmasi === 'usulan_perubahan')
                    <div style="display: flex; align-items: center; justify-content: space-between; gap: 8px;">
                        <span style="font-size: 12px; color: #92400e; font-weight: 600;">
                            ⏳ Menunggu review Admin Puskesmas
                        </span>

                        <button type="button" onclick="openDetailModalKader({{ json_encode($j) }})" class="btn-action view" style="padding: 6px 12px; font-size: 12px;">
                            Lihat Detail
                        </button>
                    </div>
                @else
                    {{-- Status: Dijadwalkan / Menunggu Konfirmasi --}}
                    <div style="display: flex; gap: 8px; align-items: center; justify-content: space-between; flex-wrap: wrap;">
                        <form method="POST" action="{{ route('kader.kegiatan.jadwal.konfirmasi', $j) }}" style="flex: 1; min-width: 140px;">
                            @csrf
                            <button type="submit" class="btn-primary" style="width: 100%; justify-content: center; padding: 8px 12px; font-size: 13px; background: #10b981; border-color: #10b981;">
                                <span>✅</span>
                                <span>Konfirmasi Kesiapan</span>
                            </button>
                        </form>

                        <button type="button" onclick="openRescheduleModal({{ json_encode($j) }})" class="btn-action" style="padding: 8px 12px; font-size: 13px; border: 1px solid #cbd5e1; background: #ffffff; color: #475569; border-radius: 8px; font-weight: 500;">
                            <span>📝</span>
                            <span>Ajukan Perubahan</span>
                        </button>
                    </div>
                @endif
            </div>

        </div>
    @empty
        <div style="grid-column: 1 / -1; background: white; border-radius: 12px; padding: 48px 20px; text-align: center; border: 1px dashed #cbd5e1;">
            <div style="font-size: 36px; margin-bottom: 8px;">📅</div>
            <strong style="color: #334155; font-size: 15px;">Belum Ada Jadwal Posyandu</strong>
            <p style="color: #94a3b8; font-size: 13px; margin: 4px 0 0 0;">
                Admin Puskesmas belum menerbitkan agenda kegiatan untuk {{ $tapos->nama }}.
            </p>
        </div>
    @endforelse
</div>

<div style="margin-top: 16px;">
    {{ $jadwals->links() }}
</div>

{{-- ==================== MODAL AJUKAN PERUBAHAN JADWAL ==================== --}}
<div id="rescheduleModal" class="modal-backdrop" style="display: none; position: fixed; inset: 0; background: rgba(15, 23, 42, 0.6); z-index: 9999; backdrop-filter: blur(4px); align-items: center; justify-content: center;">
    <div class="modal-dialog" style="background: white; border-radius: 16px; width: 100%; max-width: 520px; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25); overflow: hidden; margin: 20px;">
        <div style="padding: 20px 24px; border-bottom: 1px solid #e2e8f0; display: flex; align-items: center; justify-content: space-between; background: #fffbeb;">
            <div style="display: flex; align-items: center; gap: 10px;">
                <span style="font-size: 22px;">📝</span>
                <div>
                    <h3 style="margin: 0; font-size: 16px; font-weight: 700; color: #92400e;">Ajukan Usulan Perubahan Jadwal</h3>
                    <span style="font-size: 12px; color: #b45309;" id="reschedule_title">Tapos Melati</span>
                </div>
            </div>
            <button type="button" onclick="closeRescheduleModal()" style="background: none; border: none; font-size: 20px; color: #94a3b8; cursor: pointer;">&times;</button>
        </div>

        <form id="rescheduleForm" method="POST" action="" style="padding: 24px;">
            @csrf

            <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 12px 14px; margin-bottom: 18px; font-size: 12px; color: #475569;">
                <div style="font-weight: 700; color: #1e293b; margin-bottom: 4px;">Informasi Jadwal Saat Ini:</div>
                <div id="cur_schedule_info" style="color: #64748b;">-</div>
            </div>

            {{-- Alasan Perubahan (Wajib) --}}
            <div class="form-group" style="margin-bottom: 16px;">
                <label class="form-label" style="display: block; font-weight: 600; font-size: 13px; color: #334155; margin-bottom: 6px;">
                    Alasan Perubahan Jadwal <span style="color: #ef4444;">*</span>
                </label>
                <textarea name="alasan_perubahan" id="alasan_perubahan" rows="3" required placeholder="Contoh: Kegiatan Tapos Melati pada 5 September tidak dapat dilaksanakan karena balai desa digunakan untuk kegiatan pemilihan ketua RW." style="width: 100%; padding: 10px 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 14px; resize: vertical;"></textarea>
                <span style="font-size: 11px; color: #64748b; margin-top: 4px; display: block;">Jelaskan kendala di lapangan secara detail agar Puskesmas dapat memvalidasi.</span>
            </div>

            {{-- Usulan Tanggal Baru --}}
            <div class="form-group" style="margin-bottom: 16px;">
                <label class="form-label" style="display: block; font-weight: 600; font-size: 13px; color: #334155; margin-bottom: 6px;">
                    Usulan Tanggal Pengganti (Opsional)
                </label>
                <input type="date" name="usulan_tanggal" id="usulan_tanggal" style="width: 100%; padding: 10px 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 14px;">
            </div>

            {{-- Usulan Waktu Mulai & Selesai --}}
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px; margin-bottom: 16px;">
                <div class="form-group">
                    <label class="form-label" style="display: block; font-weight: 600; font-size: 13px; color: #334155; margin-bottom: 6px;">
                        Usulan Jam Mulai
                    </label>
                    <input type="time" name="usulan_waktu_mulai" id="usulan_waktu_mulai" style="width: 100%; padding: 10px 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 14px;">
                </div>

                <div class="form-group">
                    <label class="form-label" style="display: block; font-weight: 600; font-size: 13px; color: #334155; margin-bottom: 6px;">
                        Usulan Jam Selesai
                    </label>
                    <input type="time" name="usulan_waktu_selesai" id="usulan_waktu_selesai" style="width: 100%; padding: 10px 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 14px;">
                </div>
            </div>

            {{-- Usulan Lokasi Baru --}}
            <div class="form-group" style="margin-bottom: 24px;">
                <label class="form-label" style="display: block; font-weight: 600; font-size: 13px; color: #334155; margin-bottom: 6px;">
                    Usulan Lokasi Pengganti (Opsional)
                </label>
                <input type="text" name="usulan_lokasi" id="usulan_lokasi" placeholder="Contoh: Rumah Ibu RT 02 / Gedung PAUD Melati" style="width: 100%; padding: 10px 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 14px;">
            </div>

            <div style="display: flex; gap: 10px; justify-content: flex-end;">
                <button type="button" onclick="closeRescheduleModal()" class="btn-action" style="padding: 10px 20px; font-size: 14px; border: 1px solid #cbd5e1; background: #fff; cursor: pointer; border-radius: 8px;">
                    Batal
                </button>
                <button type="submit" class="btn-primary" style="padding: 10px 24px; font-size: 14px; background: #f59e0b; border-color: #f59e0b;">
                    Kirim Usulan ke Puskesmas
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ==================== MODAL DETAIL JADWAL KADER ==================== --}}
<div id="detailModalKader" class="modal-backdrop" style="display: none; position: fixed; inset: 0; background: rgba(15, 23, 42, 0.6); z-index: 9999; backdrop-filter: blur(4px); align-items: center; justify-content: center;">
    <div class="modal-dialog" style="background: white; border-radius: 16px; width: 100%; max-width: 500px; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25); overflow: hidden; margin: 20px;">
        <div style="padding: 20px 24px; border-bottom: 1px solid #e2e8f0; display: flex; align-items: center; justify-content: space-between; background: #f8fafc;">
            <div style="display: flex; align-items: center; gap: 10px;">
                <span style="font-size: 22px;">📋</span>
                <div>
                    <h3 style="margin: 0; font-size: 16px; font-weight: 700; color: #0f172a;" id="kader_dt_title">Rincian Jadwal</h3>
                    <span style="font-size: 12px; color: #64748b;">{{ $tapos->nama }}</span>
                </div>
            </div>
            <button type="button" onclick="closeDetailModalKader()" style="background: none; border: none; font-size: 20px; color: #94a3b8; cursor: pointer;">&times;</button>
        </div>

        <div style="padding: 24px; font-size: 14px; color: #334155; line-height: 1.6;">
            <div style="display: grid; grid-template-columns: 120px 1fr; gap: 8px; margin-bottom: 8px;">
                <span style="color: #64748b;">📅 Tanggal:</span>
                <strong id="kader_dt_tanggal">-</strong>
            </div>

            <div style="display: grid; grid-template-columns: 120px 1fr; gap: 8px; margin-bottom: 8px;">
                <span style="color: #64748b;">⏰ Waktu:</span>
                <strong id="kader_dt_waktu">-</strong>
            </div>

            <div style="display: grid; grid-template-columns: 120px 1fr; gap: 8px; margin-bottom: 8px;">
                <span style="color: #64748b;">📍 Lokasi:</span>
                <span id="kader_dt_lokasi">-</span>
            </div>

            <div style="display: grid; grid-template-columns: 120px 1fr; gap: 8px; margin-bottom: 12px;">
                <span style="color: #64748b;">📝 Catatan:</span>
                <span id="kader_dt_catatan" style="font-style: italic;">-</span>
            </div>

            <div id="kader_dt_usulan_box" style="display: none; background: #fffbeb; border: 1px solid #fde68a; border-radius: 8px; padding: 12px; margin-top: 12px;">
                <strong style="color: #92400e; font-size: 13px;">Usulan yang Anda Kirimkan:</strong>
                <div id="kader_dt_alasan" style="font-size: 13px; color: #78350f; margin-top: 4px;"></div>
            </div>
        </div>

        <div style="padding: 16px 24px; background: #f8fafc; border-top: 1px solid #e2e8f0; display: flex; justify-content: flex-end;">
            <button type="button" onclick="closeDetailModalKader()" class="btn-primary" style="padding: 8px 20px; font-size: 13px;">
                Tutup
            </button>
        </div>
    </div>
</div>

<script>
    function openRescheduleModal(jadwal) {
        document.getElementById('rescheduleForm').action = `/kader/kegiatan/jadwal/${jadwal.id}/ajukan-perubahan`;
        document.getElementById('reschedule_title').innerText = `${jadwal.nama_kegiatan}`;
        
        let tgl = (jadwal.tanggal || '').substring(0, 10);
        let jam = (jadwal.waktu_mulai || '').substring(0, 5) + ' - ' + (jadwal.waktu_selesai || '').substring(0, 5) + ' WIB';
        let lok = jadwal.lokasi || '{{ $tapos->nama }}';
        document.getElementById('cur_schedule_info').innerText = `${tgl} | ${jam} | Lokasi: ${lok}`;
        
        document.getElementById('alasan_perubahan').value = jadwal.alasan_perubahan || '';
        document.getElementById('usulan_tanggal').value = jadwal.usulan_tanggal ? jadwal.usulan_tanggal.substring(0, 10) : '';
        document.getElementById('usulan_waktu_mulai').value = jadwal.usulan_waktu_mulai ? jadwal.usulan_waktu_mulai.substring(0, 5) : (jadwal.waktu_mulai || '').substring(0, 5);
        document.getElementById('usulan_waktu_selesai').value = jadwal.usulan_waktu_selesai ? jadwal.usulan_waktu_selesai.substring(0, 5) : (jadwal.waktu_selesai || '').substring(0, 5);
        document.getElementById('usulan_lokasi').value = jadwal.usulan_lokasi || '';
        
        document.getElementById('rescheduleModal').style.display = 'flex';
    }

    function closeRescheduleModal() {
        document.getElementById('rescheduleModal').style.display = 'none';
    }

    function openDetailModalKader(jadwal) {
        document.getElementById('kader_dt_title').innerText = jadwal.nama_kegiatan;
        document.getElementById('kader_dt_tanggal').innerText = (jadwal.tanggal || '').substring(0, 10);
        document.getElementById('kader_dt_waktu').innerText = (jadwal.waktu_mulai || '').substring(0, 5) + ' - ' + (jadwal.waktu_selesai || '').substring(0, 5) + ' WIB';
        document.getElementById('kader_dt_lokasi').innerText = jadwal.lokasi || '{{ $tapos->nama }}';
        document.getElementById('kader_dt_catatan').innerText = jadwal.catatan ? `"${jadwal.catatan}"` : 'Tidak ada catatan khusus.';

        if (jadwal.status_konfirmasi === 'usulan_perubahan' && jadwal.alasan_perubahan) {
            document.getElementById('kader_dt_usulan_box').style.display = 'block';
            document.getElementById('kader_dt_alasan').innerText = `"${jadwal.alasan_perubahan}"`;
        } else {
            document.getElementById('kader_dt_usulan_box').style.display = 'none';
        }

        document.getElementById('detailModalKader').style.display = 'flex';
    }

    function closeDetailModalKader() {
        document.getElementById('detailModalKader').style.display = 'none';
    }

    window.onclick = function(event) {
        if (event.target.classList.contains('modal-backdrop')) {
            event.target.style.display = 'none';
        }
    }
</script>

@endsection
