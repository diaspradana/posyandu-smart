@extends('layouts.app')

@section('title', 'Validasi Pemeriksaan')
@section('page-title', 'Validasi Pemeriksaan')

@section('content')

<div class="page-heading">
    <div>
        <span class="eyebrow">SUPERVISI & VERIFIKASI NAKES</span>
        <h1>Validasi Data Pemeriksaan 🛡️</h1>
        <p>
            Verifikasi data hasil pemeriksaan dari kader Posyandu beserta telaah screening AI sebelum dijadikan data resmi Puskesmas.
        </p>
    </div>
    <div class="date-card">
        <span>Antrean Pending</span>
        <strong style="color: #dc2626;">{{ $totalPending }} Menunggu</strong>
    </div>
</div>

{{-- ALERT SUCCESS --}}
@if(session('success'))
    <div class="alert alert-success" style="background: #dcfce7; border: 1px solid #86efac; color: #166534; padding: 12px 16px; border-radius: 10px; margin-bottom: 20px;">
        ✓ {{ session('success') }}
    </div>
@endif

{{-- FILTER TABS & STATUS --}}
<div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 14px; margin-bottom: 20px;">
    {{-- CATEGORY TABS (BALITA / IBU HAMIL) --}}
    <div style="display: flex; background: #f1f5f9; padding: 4px; border-radius: 12px; gap: 4px;">
        <a href="{{ route('admin.validasi.index', ['tab' => 'balita', 'status' => $statusFilter]) }}" 
           class="button {{ $tab === 'balita' ? 'primary' : 'secondary' }}" 
           style="border-radius: 8px; font-size: 13px; padding: 8px 16px; text-decoration: none;">
            👶 Pemeriksaan Balita ({{ $pendingBalitaCount }})
        </a>
        <a href="{{ route('admin.validasi.index', ['tab' => 'ibu_hamil', 'status' => $statusFilter]) }}" 
           class="button {{ $tab === 'ibu_hamil' ? 'primary' : 'secondary' }}" 
           style="border-radius: 8px; font-size: 13px; padding: 8px 16px; text-decoration: none;">
            🤰 Pemeriksaan Ibu Hamil ({{ $pendingBumilCount }})
        </a>
    </div>

    {{-- STATUS FILTER --}}
    <div style="display: flex; gap: 8px; align-items: center;">
        <span style="font-size: 13px; color: #64748b; font-weight: 600;">Status Validasi:</span>
        <a href="{{ route('admin.validasi.index', ['tab' => $tab, 'status' => 'pending']) }}" class="status-pill {{ $statusFilter === 'pending' ? 'danger' : 'secondary' }}" style="text-decoration:none;">⏳ Pending</a>
        <a href="{{ route('admin.validasi.index', ['tab' => $tab, 'status' => 'validated']) }}" class="status-pill {{ $statusFilter === 'validated' ? 'success' : 'secondary' }}" style="text-decoration:none;">✓ Tervalidasi</a>
        <a href="{{ route('admin.validasi.index', ['tab' => $tab, 'status' => 'rejected']) }}" class="status-pill {{ $statusFilter === 'rejected' ? 'danger' : 'secondary' }}" style="text-decoration:none;">✗ Ditolak</a>
        <a href="{{ route('admin.validasi.index', ['tab' => $tab, 'status' => 'semua']) }}" class="status-pill {{ $statusFilter === 'semua' ? 'info' : 'secondary' }}" style="text-decoration:none;">Semua</a>
    </div>
</div>

{{-- TAB CONTENT BALITA --}}
@if($tab === 'balita')
<div class="dashboard-card">
    <div class="card-header">
        <div>
            <span class="card-eyebrow">DAFTAR PEMERIKSAAN BALITA</span>
            <h3>Antrean Validasi Balita</h3>
        </div>
        <span class="badge secondary">{{ $balitaList->total() }} Data</span>
    </div>

    <div class="table-responsive" style="margin-top: 16px;">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Balita & Tapos</th>
                    <th>Umur</th>
                    <th>Antropometri (BB/TB)</th>
                    <th>Screening AI</th>
                    <th>Status Validasi</th>
                    <th>Aksi Supervisi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($balitaList as $item)
                    <tr>
                        <td>{{ $item->tanggal_pemeriksaan->format('d/m/Y') }}</td>
                        <td>
                            <strong>{{ $item->balita->nama ?? '-' }}</strong>
                            <div style="font-size: 11.5px; color: #64748b;">{{ $item->balita->tapos->nama ?? '-' }}</div>
                        </td>
                        <td>{{ $item->umur_bulan ?? $item->balita->usia_bulan }} bln</td>
                        <td>
                            <div><strong>BB:</strong> {{ $item->berat_badan }} kg</div>
                            <div><strong>TB:</strong> {{ $item->tinggi_badan }} cm</div>
                        </td>
                        <td>
                            {!! $item->ai_badge !!}
                            <div style="font-size: 11px; color: #64748b; margin-top: 2px;">
                                Probabilitas: {{ $item->ai_probability ? round($item->ai_probability * 100) : '-' }}% ({{ $item->ai_model_version ?? 'stunting-v1.0' }})
                            </div>
                        </td>
                        <td>
                            {!! $item->status_validasi_badge !!}
                            @if($item->catatan_validasi)
                                <div style="font-size: 11px; color: #64748b; max-width: 160px;">{{ $item->catatan_validasi }}</div>
                            @endif
                        </td>
                        <td>
                            @if($item->status_validasi === 'pending')
                                <div style="display: flex; gap: 6px;">
                                    <form method="POST" action="{{ route('admin.validasi.balita.approve', $item) }}">
                                        @csrf
                                        <button type="submit" class="button primary small" style="background: #10b981; border: none; font-size: 11.5px; padding: 5px 10px;" title="Setujui dan jadikan data resmi">
                                            ✓ Validasi
                                        </button>
                                    </form>
                                    <button type="button" class="button secondary small" style="color: #dc2626; border-color: #fca5a5; font-size: 11.5px; padding: 5px 10px;" onclick="openRejectBalitaModal({{ $item->id }}, '{{ $item->balita->nama }}')">
                                        ✗ Tolak
                                    </button>
                                </div>
                            @else
                                <span class="text-muted" style="font-size: 12px;">Selesai diproses</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center" style="padding: 24px; color: #64748b;">
                            Tidak ada data pemeriksaan balita dalam status ini.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div style="margin-top: 16px;">
        {{ $balitaList->links() }}
    </div>
</div>

{{-- MODAL TOLAK BALITA --}}
<div id="modalRejectBalita" class="modal-backdrop" style="display: none; position: fixed; inset: 0; background: rgba(15,23,42,0.6); z-index: 9999; align-items: center; justify-content: center;">
    <div style="background: white; border-radius: 16px; width: 90%; max-width: 440px; padding: 24px;">
        <h3 style="margin: 0 0 12px 0;">Tolak Pemeriksaan Balita</h3>
        <p id="rejectBalitaName" style="font-size: 13.5px; color: #64748b; margin-bottom: 16px;"></p>
        <form id="formRejectBalita" method="POST">
            @csrf
            <div style="margin-bottom: 16px;">
                <label style="display: block; font-size: 13px; font-weight: 600; margin-bottom: 6px;">Alasan Penolakan / Catatan Evaluasi:</label>
                <textarea name="catatan_validasi" class="form-input" rows="3" required placeholder="Contoh: Pengukuran tinggi badan tidak wajar, mohon ukur ulang..." style="width: 100%; border-radius: 8px; padding: 10px; border: 1px solid #cbd5e1;"></textarea>
            </div>
            <div style="display: flex; justify-content: flex-end; gap: 8px;">
                <button type="button" class="button secondary" onclick="closeRejectBalitaModal()">Batal</button>
                <button type="submit" class="button primary" style="background: #dc2626; border: none;">Kirim Penolakan</button>
            </div>
        </form>
    </div>
</div>
@endif

{{-- TAB CONTENT IBU HAMIL --}}
@if($tab === 'ibu_hamil')
<div class="dashboard-card">
    <div class="card-header">
        <div>
            <span class="card-eyebrow">DAFTAR PEMERIKSAAN IBU HAMIL</span>
            <h3>Antrean Validasi Ibu Hamil</h3>
        </div>
        <span class="badge secondary">{{ $ibuHamilList->total() }} Data</span>
    </div>

    <div class="table-responsive" style="margin-top: 16px;">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Ibu Hamil & Tapos</th>
                    <th>Usia Hamil</th>
                    <th>Klinis (TD/Gula/HR)</th>
                    <th>Screening AI</th>
                    <th>Status Validasi</th>
                    <th>Aksi Supervisi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($ibuHamilList as $item)
                    <tr>
                        <td>{{ $item->tanggal_pemeriksaan->format('d/m/Y') }}</td>
                        <td>
                            <strong>{{ $item->ibuHamil->nama ?? '-' }}</strong>
                            <div style="font-size: 11.5px; color: #64748b;">{{ $item->ibuHamil->tapos->nama ?? '-' }}</div>
                        </td>
                        <td>{{ $item->usia_kehamilan_minggu }} mgg</td>
                        <td>
                            <div><strong>TD:</strong> {{ $item->systolic_bp ? "{$item->systolic_bp}/{$item->diastolic_bp} mmHg" : $item->tekanan_darah }}</div>
                            <div><strong>Gula:</strong> {{ $item->blood_sugar ? $item->blood_sugar . ' mmol/L' : '-' }}</div>
                            <div><strong>HR:</strong> {{ $item->heart_rate ? $item->heart_rate . ' bpm' : '-' }}</div>
                        </td>
                        <td>
                            {!! $item->risk_badge !!}
                            <div style="font-size: 11px; color: #64748b; margin-top: 2px;">
                                Probabilitas: {{ $item->ai_probability ? round($item->ai_probability * 100) : '-' }}% ({{ $item->ai_model_version ?? 'maternal-v1.0' }})
                            </div>
                        </td>
                        <td>
                            {!! $item->status_validasi_badge !!}
                            @if($item->catatan_validasi)
                                <div style="font-size: 11px; color: #64748b; max-width: 160px;">{{ $item->catatan_validasi }}</div>
                            @endif
                        </td>
                        <td>
                            @if($item->status_validasi === 'pending')
                                <div style="display: flex; gap: 6px;">
                                    <form method="POST" action="{{ route('admin.validasi.ibu-hamil.approve', $item) }}">
                                        @csrf
                                        <button type="submit" class="button primary small" style="background: #10b981; border: none; font-size: 11.5px; padding: 5px 10px;" title="Setujui dan jadikan data resmi">
                                            ✓ Validasi
                                        </button>
                                    </form>
                                    <button type="button" class="button secondary small" style="color: #dc2626; border-color: #fca5a5; font-size: 11.5px; padding: 5px 10px;" onclick="openRejectBumilModal({{ $item->id }}, '{{ $item->ibuHamil->nama }}')">
                                        ✗ Tolak
                                    </button>
                                </div>
                            @else
                                <span class="text-muted" style="font-size: 12px;">Selesai diproses</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center" style="padding: 24px; color: #64748b;">
                            Tidak ada data pemeriksaan ibu hamil dalam status ini.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div style="margin-top: 16px;">
        {{ $ibuHamilList->links() }}
    </div>
</div>

{{-- MODAL TOLAK IBU HAMIL --}}
<div id="modalRejectBumil" class="modal-backdrop" style="display: none; position: fixed; inset: 0; background: rgba(15,23,42,0.6); z-index: 9999; align-items: center; justify-content: center;">
    <div style="background: white; border-radius: 16px; width: 90%; max-width: 440px; padding: 24px;">
        <h3 style="margin: 0 0 12px 0;">Tolak Pemeriksaan Ibu Hamil</h3>
        <p id="rejectBumilName" style="font-size: 13.5px; color: #64748b; margin-bottom: 16px;"></p>
        <form id="formRejectBumil" method="POST">
            @csrf
            <div style="margin-bottom: 16px;">
                <label style="display: block; font-size: 13px; font-weight: 600; margin-bottom: 6px;">Alasan Penolakan / Catatan:</label>
                <textarea name="catatan_validasi" class="form-input" rows="3" required placeholder="Contoh: Angka tensi tidak lengkap..." style="width: 100%; border-radius: 8px; padding: 10px; border: 1px solid #cbd5e1;"></textarea>
            </div>
            <div style="display: flex; justify-content: flex-end; gap: 8px;">
                <button type="button" class="button secondary" onclick="closeRejectBumilModal()">Batal</button>
                <button type="submit" class="button primary" style="background: #dc2626; border: none;">Kirim Penolakan</button>
            </div>
        </form>
    </div>
</div>
@endif

@endsection

@section('scripts')
<script>
function openRejectBalitaModal(id, name) {
    document.getElementById('rejectBalitaName').innerText = 'Pemeriksaan Balita: ' + name;
    document.getElementById('formRejectBalita').action = '/admin/validasi/balita/' + id + '/reject';
    document.getElementById('modalRejectBalita').style.display = 'flex';
}

function closeRejectBalitaModal() {
    document.getElementById('modalRejectBalita').style.display = 'none';
}

function openRejectBumilModal(id, name) {
    document.getElementById('rejectBumilName').innerText = 'Pemeriksaan Ibu Hamil: ' + name;
    document.getElementById('formRejectBumil').action = '/admin/validasi/ibu-hamil/' + id + '/reject';
    document.getElementById('modalRejectBumil').style.display = 'flex';
}

function closeRejectBumilModal() {
    document.getElementById('modalRejectBumil').style.display = 'none';
}
</script>
@endsection
