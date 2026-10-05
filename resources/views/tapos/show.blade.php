@extends('layouts.app')

@section('title', 'Detail Tapos: ' . $tapo->nama)
@section('page-title', 'Detail Tapos')

@section('content')
<div class="page-heading">
    <div>
        <span class="eyebrow">DETAIL WILAYAH POSYANDU</span>
        <h1>{{ $tapo->nama }}</h1>
        <p>Kode: <strong>{{ $tapo->kode }}</strong> | Kelurahan: {{ $tapo->kelurahan ?? '-' }}, Kecamatan: {{ $tapo->kecamatan ?? '-' }}</p>
    </div>

    <div style="display: flex; gap: 8px; flex-wrap: wrap;">
        @if(auth()->user()->isAdmin())
            <button type="button" onclick="openCreateKaderModal()" class="btn-primary" style="background: #087f5b;">
                <span>+</span>
                <span>Registrasi Kader Baru</span>
            </button>
            <a href="{{ route('tapos.edit', $tapo) }}" class="btn-primary" style="background: #eab308; color: #713f12;">
                ✏️ Edit Posyandu
            </a>
        @endif
        <a href="{{ route('tapos.index') }}" class="btn-secondary">
            &larr; Kembali
        </a>
    </div>
</div>

{{-- STATS GRID --}}
<div class="stats-grid" style="grid-template-columns: repeat(3, 1fr); margin-bottom: 24px;">
    <div class="stat-card">
        <span class="stat-label">Total Balita</span>
        <strong class="stat-number" style="color: var(--primary);">{{ $tapo->balita_count }}</strong>
        <a href="{{ route('balita.index', ['tapos_id' => $tapo->id]) }}" class="stat-description" style="color: var(--primary); font-weight: 600; text-decoration: underline; margin-top: 8px; display: inline-block;">
            Lihat Balita →
        </a>
    </div>

    <div class="stat-card">
        <span class="stat-label">Total Ibu Hamil</span>
        <strong class="stat-number" style="color: #db2777;">{{ $tapo->ibu_hamil_count }}</strong>
        <a href="{{ route('ibu-hamil.index', ['tapos_id' => $tapo->id]) }}" class="stat-description" style="color: #db2777; font-weight: 600; text-decoration: underline; margin-top: 8px; display: inline-block;">
            Lihat Ibu Hamil →
        </a>
    </div>

    <div class="stat-card">
        <span class="stat-label">Petugas & Kader</span>
        <strong class="stat-number" style="color: #0284c7;">{{ $tapo->kaders_count ?? count($kaders) }}</strong>
        <span class="stat-description" style="color: #0284c7; font-weight: 600; margin-top: 8px; display: inline-block;">
            Akun Kader Aktif
        </span>
    </div>
</div>

{{-- INFORMASI LOKASI --}}
<div class="dashboard-card" style="padding: 24px; margin-bottom: 24px;">
    <h2 style="font-size: 16px; font-weight: 700; margin-bottom: 16px; color: #172b26; border-bottom: 1px solid #f1f5f9; padding-bottom: 10px;">
        Informasi Lokasi & Kepengurusan
    </h2>

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 16px; font-size: 13px;">
        <div>
            <span style="font-size: 11px; color: #64748b; font-weight: 600; text-transform: uppercase;">Nama Ketua Posyandu</span>
            <div style="font-weight: 700; color: #1e293b; margin-top: 2px;">{{ $tapo->nama_ketua ?? '-' }}</div>
        </div>

        <div>
            <span style="font-size: 11px; color: #64748b; font-weight: 600; text-transform: uppercase;">No. Telepon / HP</span>
            <div style="font-weight: 600; margin-top: 2px;">{{ $tapo->no_hp ?? '-' }}</div>
        </div>

        <div>
            <span style="font-size: 11px; color: #64748b; font-weight: 600; text-transform: uppercase;">Alamat Lengkap</span>
            <div style="font-weight: 600; margin-top: 2px;">{{ $tapo->alamat ?? '-' }}</div>
        </div>

        <div>
            <span style="font-size: 11px; color: #64748b; font-weight: 600; text-transform: uppercase;">Status Operasional</span>
            <div style="margin-top: 4px;">
                <span class="badge-status {{ $tapo->status === 'aktif' ? 'aktif' : 'tidak_aktif' }}">
                    {{ $tapo->status === 'aktif' ? '🟢 Aktif' : '🔴 Tidak Aktif' }}
                </span>
            </div>
        </div>
    </div>
</div>

{{-- SECTION PETUGAS / KADER POSYANDU --}}
<div class="dashboard-card" style="padding: 0; overflow: hidden; margin-bottom: 24px;">
    <div style="padding: 20px 24px; border-bottom: 1px solid #f1f5f9; display: flex; justify-content: space-between; align-items: center; flex-wrap: gap-3;">
        <div>
            <span class="eyebrow" style="color: #087f5b; font-size: 10.5px;">MANAJEMEN PENGGUNA TAPOS</span>
            <h2 style="font-size: 17px; font-weight: 700; color: #0f172a; margin: 2px 0 0 0;">
                👥 Daftar Petugas & Kader Posyandu
            </h2>
            <p style="font-size: 12.5px; color: #64748b; margin-top: 2px;">
                Akun kader terdaftar yang memiliki hak akses operasional dan pencatatan untuk <strong>{{ $tapo->nama }}</strong>.
            </p>
        </div>

        @if(auth()->user()->isAdmin())
            <button type="button" onclick="openCreateKaderModal()" class="btn-primary" style="padding: 8px 16px; font-size: 13px;">
                <span>+</span>
                <span>Registrasi Kader Baru</span>
            </button>
        @endif
    </div>

    {{-- INFO CALLOUT --}}
    <div style="padding: 14px 24px; background: #f8fafc; border-bottom: 1px solid #f1f5f9; font-size: 12px; color: #334155; display: flex; align-items: flex-start; gap: 10px;">
        <span style="font-size: 16px; line-height: 1;">ℹ️</span>
        <div>
            <strong>Registrasi Petugas Terpusat:</strong> Petugas Posyandu / Kader tidak dapat mendaftar mandiri di form login. Akun kader didaftarkan dan dikelola secara terpusat oleh Admin Puskesmas pada halaman ini.
        </div>
    </div>

    {{-- KADER TABLE --}}
    <div class="table-responsive">
        <table class="custom-table">
            <thead>
                <tr>
                    <th style="width: 50px; text-align: center;">No</th>
                    <th>Nama Petugas / Kader</th>
                    <th>Email (Username Login)</th>
                    <th style="text-align: center;">Peran Akses</th>
                    <th>Tanggal Terdaftar</th>
                    @if(auth()->user()->isAdmin())
                        <th style="text-align: right;">Aksi</th>
                    @endif
                </tr>
            </thead>
            <tbody>
                @forelse($kaders as $index => $kader)
                    <tr>
                        <td style="text-align: center; color: #64748b; font-weight: 600;">
                            {{ $index + 1 }}
                        </td>
                        <td>
                            <div style="font-weight: 700; color: #0f172a;">
                                {{ $kader->name }}
                            </div>
                        </td>
                        <td>
                            <code style="background: #f1f5f9; color: #0f766e; padding: 3px 8px; border-radius: 6px; font-size: 12px; font-family: monospace;">
                                {{ $kader->email }}
                            </code>
                        </td>
                        <td style="text-align: center;">
                            <span class="status-pill success" style="font-size: 11px; font-weight: 600;">
                                Kader Posyandu
                            </span>
                        </td>
                        <td style="font-size: 12px; color: #64748b;">
                            {{ $kader->created_at ? $kader->created_at->translatedFormat('d F Y, H:i') : '-' }}
                        </td>
                        @if(auth()->user()->isAdmin())
                            <td style="text-align: right;">
                                <div class="actions-group">
                                    <button
                                        type="button"
                                        onclick="openEditKaderModal({{ json_encode($kader) }})"
                                        class="btn-action edit"
                                        title="Edit Data / Ganti Password"
                                    >
                                        Edit / Sandi
                                    </button>

                                    <form
                                        method="POST"
                                        action="{{ route('tapos.kader.destroy', ['tapo' => $tapo->id, 'user' => $kader->id]) }}"
                                        onsubmit="return confirm('Apakah Anda yakin ingin menghapus akun kader {{ $kader->name }}?')"
                                        style="display: inline;"
                                    >
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-action delete" title="Hapus Akun">
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>
                        @endif
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ auth()->user()->isAdmin() ? 6 : 5 }}" style="text-align: center; padding: 36px 20px; color: #94a3b8;">
                            <div style="font-size: 28px; margin-bottom: 8px;">👤</div>
                            <div style="font-weight: 600; color: #475569; margin-bottom: 4px;">Belum Ada Kader Terdaftar</div>
                            <p style="font-size: 12.5px; color: #94a3b8; max-width: 400px; margin: 0 auto 16px auto;">
                                Posyandu ini belum memiliki akun kader untuk login dan melakukan pemeriksaan balita & ibu hamil.
                            </p>
                            @if(auth()->user()->isAdmin())
                                <button type="button" onclick="openCreateKaderModal()" class="btn-primary" style="font-size: 12.5px; padding: 7px 16px;">
                                    + Daftarkan Kader Pertama
                                </button>
                            @endif
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- ==================== MODAL REGISTRASI KADER BARU ==================== --}}
@if(auth()->user()->isAdmin())
<div id="modalCreateKader" class="modal-backdrop" style="display: none; position: fixed; inset: 0; background: rgba(15, 23, 42, 0.6); z-index: 9999; backdrop-filter: blur(4px); align-items: center; justify-content: center; padding: 16px;">
    <div class="modal-dialog" style="background: white; border-radius: 20px; width: 100%; max-width: 500px; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25); overflow: hidden;">
        <div style="padding: 20px 24px; border-bottom: 1px solid #f1f5f9; display: flex; justify-content: space-between; align-items: center;">
            <div>
                <span class="eyebrow" style="color: #087f5b; font-size: 10.5px;">FORMULIR PETUGAS</span>
                <h3 style="margin: 2px 0 0 0; font-size: 17px; font-weight: 700; color: #0f172a;">
                    Registrasi Petugas/Kader Posyandu
                </h3>
            </div>
            <button type="button" onclick="closeCreateKaderModal()" style="background: none; border: none; font-size: 24px; color: #94a3b8; cursor: pointer; line-height: 1;">&times;</button>
        </div>

        <form method="POST" action="{{ route('tapos.kader.store', $tapo) }}" style="padding: 24px;">
            @csrf

            <div style="margin-bottom: 14px;">
                <label style="display: block; font-size: 11.5px; font-weight: 700; color: #334155; margin-bottom: 6px; text-transform: uppercase;">
                    Lokasi Penugasan (Tapos)
                </label>
                <input type="text" class="custom-select" value="{{ $tapo->nama }} ({{ $tapo->kode }})" readonly style="background: #f8fafc; color: #475569; width: 100%;">
            </div>

            <div style="margin-bottom: 14px;">
                <label for="create_name" style="display: block; font-size: 11.5px; font-weight: 700; color: #334155; margin-bottom: 6px; text-transform: uppercase;">
                    Nama Lengkap Kader <span style="color: #dc2626;">*</span>
                </label>
                <input
                    type="text"
                    id="create_name"
                    name="name"
                    required
                    placeholder="Contoh: Siti Aminah / Kader Melati"
                    class="custom-select"
                    style="width: 100%; border: 1px solid #cbd5e1;"
                >
            </div>

            <div style="margin-bottom: 14px;">
                <label for="create_email" style="display: block; font-size: 11.5px; font-weight: 700; color: #334155; margin-bottom: 6px; text-transform: uppercase;">
                    Email Login Kader <span style="color: #dc2626;">*</span>
                </label>
                <input
                    type="email"
                    id="create_email"
                    name="email"
                    required
                    placeholder="kader.nama@posyandusmart.test"
                    class="custom-select"
                    style="width: 100%; border: 1px solid #cbd5e1;"
                >
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 20px;">
                <div>
                    <label for="create_password" style="display: block; font-size: 11.5px; font-weight: 700; color: #334155; margin-bottom: 6px; text-transform: uppercase;">
                        Password <span style="color: #dc2626;">*</span>
                    </label>
                    <input
                        type="password"
                        id="create_password"
                        name="password"
                        required
                        placeholder="Min. 6 karakter"
                        class="custom-select"
                        style="width: 100%; border: 1px solid #cbd5e1;"
                    >
                </div>
                <div>
                    <label for="create_password_confirmation" style="display: block; font-size: 11.5px; font-weight: 700; color: #334155; margin-bottom: 6px; text-transform: uppercase;">
                        Konfirmasi Password <span style="color: #dc2626;">*</span>
                    </label>
                    <input
                        type="password"
                        id="create_password_confirmation"
                        name="password_confirmation"
                        required
                        placeholder="Ulangi password"
                        class="custom-select"
                        style="width: 100%; border: 1px solid #cbd5e1;"
                    >
                </div>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 10px; border-top: 1px solid #f1f5f9; padding-top: 16px;">
                <button type="button" onclick="closeCreateKaderModal()" class="btn-action" style="padding: 9px 18px; font-size: 13px; border: 1px solid #cbd5e1; background: #fff; cursor: pointer; border-radius: 8px;">
                    Batal
                </button>
                <button type="submit" class="btn-primary" style="padding: 9px 20px; font-size: 13px;">
                    Simpan & Daftarkan Kader
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ==================== MODAL EDIT KADER ==================== --}}
<div id="modalEditKader" class="modal-backdrop" style="display: none; position: fixed; inset: 0; background: rgba(15, 23, 42, 0.6); z-index: 9999; backdrop-filter: blur(4px); align-items: center; justify-content: center; padding: 16px;">
    <div class="modal-dialog" style="background: white; border-radius: 20px; width: 100%; max-width: 500px; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25); overflow: hidden;">
        <div style="padding: 20px 24px; border-bottom: 1px solid #f1f5f9; display: flex; justify-content: space-between; align-items: center;">
            <div>
                <span class="eyebrow" style="color: #087f5b; font-size: 10.5px;">PERBARUI AKUN KADER</span>
                <h3 style="margin: 2px 0 0 0; font-size: 17px; font-weight: 700; color: #0f172a;">
                    Edit Data / Password Kader
                </h3>
            </div>
            <button type="button" onclick="closeEditKaderModal()" style="background: none; border: none; font-size: 24px; color: #94a3b8; cursor: pointer; line-height: 1;">&times;</button>
        </div>

        <form id="formEditKader" method="POST" action="" style="padding: 24px;">
            @csrf
            @method('PUT')

            <div style="margin-bottom: 14px;">
                <label for="edit_name" style="display: block; font-size: 11.5px; font-weight: 700; color: #334155; margin-bottom: 6px; text-transform: uppercase;">
                    Nama Lengkap Kader <span style="color: #dc2626;">*</span>
                </label>
                <input
                    type="text"
                    id="edit_name"
                    name="name"
                    required
                    class="custom-select"
                    style="width: 100%; border: 1px solid #cbd5e1;"
                >
            </div>

            <div style="margin-bottom: 14px;">
                <label for="edit_email" style="display: block; font-size: 11.5px; font-weight: 700; color: #334155; margin-bottom: 6px; text-transform: uppercase;">
                    Email Login <span style="color: #dc2626;">*</span>
                </label>
                <input
                    type="email"
                    id="edit_email"
                    name="email"
                    required
                    class="custom-select"
                    style="width: 100%; border: 1px solid #cbd5e1;"
                >
            </div>

            <div style="margin-bottom: 20px;">
                <label for="edit_password" style="display: block; font-size: 11.5px; font-weight: 700; color: #334155; margin-bottom: 6px; text-transform: uppercase;">
                    Kata Sandi Baru (Opsional)
                </label>
                <input
                    type="password"
                    id="edit_password"
                    name="password"
                    placeholder="Kosongkan jika tidak ingin mengubah password"
                    class="custom-select"
                    style="width: 100%; border: 1px solid #cbd5e1;"
                >
                <span style="display: block; font-size: 11px; color: #64748b; margin-top: 4px;">Isi hanya jika ingin mereset password akun kader.</span>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 10px; border-top: 1px solid #f1f5f9; padding-top: 16px;">
                <button type="button" onclick="closeEditKaderModal()" class="btn-action" style="padding: 9px 18px; font-size: 13px; border: 1px solid #cbd5e1; background: #fff; cursor: pointer; border-radius: 8px;">
                    Batal
                </button>
                <button type="submit" class="btn-primary" style="padding: 9px 20px; font-size: 13px;">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
@endif

<script>
    function openCreateKaderModal() {
        const modal = document.getElementById('modalCreateKader');
        if (modal) modalstyleDisplay(modal, 'flex');
    }

    function closeCreateKaderModal() {
        const modal = document.getElementById('modalCreateKader');
        if (modal) modalstyleDisplay(modal, 'none');
    }

    function openEditKaderModal(kader) {
        const modal = document.getElementById('modalEditKader');
        const form = document.getElementById('formEditKader');
        if (!modal || !form) return;

        form.action = "{{ url('tapos/' . $tapo->id . '/kader') }}/" + kader.id;
        document.getElementById('edit_name').value = kader.name;
        document.getElementById('edit_email').value = kader.email;
        document.getElementById('edit_password').value = '';

        modalstyleDisplay(modal, 'flex');
    }

    function closeEditKaderModal() {
        const modal = document.getElementById('modalEditKader');
        if (modal) modalstyleDisplay(modal, 'none');
    }

    function modalstyleDisplay(el, val) {
        el.style.display = val;
    }

    window.addEventListener('click', function(e) {
        const createModal = document.getElementById('modalCreateKader');
        const editModal = document.getElementById('modalEditKader');
        if (e.target === createModal) closeCreateKaderModal();
        if (e.target === editModal) closeEditKaderModal();
    });
</script>

@endsection