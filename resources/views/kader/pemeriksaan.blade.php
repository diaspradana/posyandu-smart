@extends('layouts.app')

@section('title', 'Pemeriksaan Posyandu')
@section('page-title', 'Pemeriksaan Posyandu')

@section('content')

<div class="page-heading">
    <div>
        <span class="eyebrow">INPUT OPERASIONAL & AI SCREENING REAL-TIME</span>
        <h1>Pemeriksaan Posyandu {{ $tapos->nama }} 🩺</h1>
        <p>
            Catat data pengukuran balita & ibu hamil. Sistem AI akan memberikan rekomendasi screening dan early warning seketika.
        </p>
    </div>
    <div class="date-card">
        <span>Tanggal</span>
        <strong>{{ now()->translatedFormat('l, d F Y') }}</strong>
    </div>
</div>

{{-- ALERT SUCCESS --}}
@if(session('success'))
    <div class="alert alert-success" style="background: #dcfce7; border: 1px solid #86efac; color: #166534; padding: 12px 16px; border-radius: 10px; margin-bottom: 20px;">
        ✓ {{ session('success') }}
    </div>
@endif

{{-- HASIL AI SCREENING BALITA MODAL/CARD (ITEM 8 BLUEPRINT) --}}
@if(session('ai_screening_balita'))
@php $aiB = session('ai_screening_balita'); @endphp
<div class="dashboard-card" style="background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%); border: 2px solid {{ $aiB['hasil_ai'] === 'risiko_stunting' ? '#fca5a5' : ($aiB['hasil_ai'] === 'pemantauan' ? '#fed7aa' : '#86efac') }}; border-radius: 16px; padding: 24px; margin-bottom: 24px; box-shadow: 0 10px 25px -5px rgba(0,0,0,0.08);">
    
    <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 12px; margin-bottom: 16px; border-bottom: 1px solid #e2e8f0; padding-bottom: 12px;">
        <div>
            <span class="card-eyebrow" style="color: #2563eb; font-weight: 800;">HASIL SCREENING AI — BALITA</span>
            <h2 style="margin: 4px 0 0 0; color: #0f172a; font-size: 20px;">{{ $aiB['nama'] }}</h2>
        </div>
        <div style="text-align: right;">
            <span class="status-pill warning" style="font-size: 11.5px;">⏳ Status: Menunggu Validasi Puskesmas</span>
            <div style="font-size: 11px; color: #64748b; margin-top: 4px;">Model: {{ $aiB['model_version'] }}</div>
        </div>
    </div>

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 16px; margin-bottom: 16px;">
        
        {{-- RISK LEVEL & PROBABILITY --}}
        <div style="background: white; border: 1px solid #e2e8f0; border-radius: 12px; padding: 16px;">
            <span style="font-size: 12px; color: #64748b; font-weight: 600;">Status Risiko Pertumbuhan:</span>
            <div style="margin: 8px 0;">
                @if($aiB['hasil_ai'] === 'risiko_stunting')
                    <span class="status-pill danger" style="font-size: 14px; padding: 6px 14px;">🔴 RISIKO STUNTING</span>
                @elseif($aiB['hasil_ai'] === 'pemantauan')
                    <span class="status-pill warning" style="font-size: 14px; padding: 6px 14px;">🟡 PEMANTAUAN</span>
                @else
                    <span class="status-pill success" style="font-size: 14px; padding: 6px 14px;">🟢 PERTUMBUHAN NORMAL</span>
                @endif
            </div>

            <div style="margin-top: 12px;">
                <div style="display: flex; justify-content: space-between; font-size: 12px; color: #475569; margin-bottom: 4px;">
                    <span>Keyakinan Probabilitas:</span>
                    <strong>{{ round($aiB['probability'] * 100) }}%</strong>
                </div>
                <div style="width: 100%; height: 8px; background: #e2e8f0; border-radius: 4px; overflow: hidden;">
                    <div style="width: {{ round($aiB['probability'] * 100) }}%; height: 100%; background: {{ $aiB['hasil_ai'] === 'risiko_stunting' ? '#dc2626' : ($aiB['hasil_ai'] === 'pemantauan' ? '#ea580c' : '#16a34a') }};"></div>
                </div>
            </div>

            <div style="margin-top: 14px; padding-top: 10px; border-top: 1px dashed #e2e8f0;">
                <span style="font-size: 12px; color: #64748b;">Tren Longitudinal Pasien:</span>
                <div style="margin-top: 4px; font-size: 14px;">
                    {!! $aiB['sequence_html'] !!}
                    <span style="margin-left: 8px;">{!! $aiB['trend_badge'] !!}</span>
                </div>
            </div>
        </div>

        {{-- CLINICAL INDICATORS & RECOMMENDATION --}}
        <div style="background: white; border: 1px solid #e2e8f0; border-radius: 12px; padding: 16px; display: flex; flex-direction: column; justify-content: space-between;">
            <div>
                <span style="font-size: 12px; color: #64748b; font-weight: 600;">Indikator Pemeriksaan:</span>
                <ul style="margin: 8px 0 12px 18px; padding: 0; font-size: 13px; color: #334155; line-height: 1.6;">
                    @foreach($aiB['indicators'] as $ind)
                        <li>✓ {{ $ind }}</li>
                    @endforeach
                </ul>
            </div>

            <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 10px 12px;">
                <strong style="font-size: 12px; color: #0f172a; display: block; margin-bottom: 3px;">💡 Rekomendasi Tindak Lanjut:</strong>
                <span style="font-size: 12.5px; color: #475569;">{{ $aiB['recommendation'] }}</span>
            </div>
        </div>

    </div>

    {{-- DISCLAIMER --}}
    <div style="background: #eff6ff; border: 1px solid #bfdbfe; border-radius: 8px; padding: 10px 14px; font-size: 12px; color: #1e40af; display: flex; align-items: center; gap: 8px;">
        <span style="font-size: 16px;">⚠️</span>
        <span><strong>Disclaimer:</strong> {{ $aiB['disclaimer'] }}</span>
    </div>
</div>
@endif

{{-- HASIL AI SCREENING IBU HAMIL MODAL/CARD (ITEM 3 BLUEPRINT) --}}
@if(session('ai_screening_maternal'))
@php $aiM = session('ai_screening_maternal'); @endphp
<div class="dashboard-card" style="background: linear-gradient(135deg, #ffffff 0%, #fff5f8 100%); border: 2px solid {{ $aiM['risk_level'] === 'high' ? '#fca5a5' : ($aiM['risk_level'] === 'medium' ? '#fed7aa' : '#86efac') }}; border-radius: 16px; padding: 24px; margin-bottom: 24px; box-shadow: 0 10px 25px -5px rgba(0,0,0,0.08);">
    
    <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 12px; margin-bottom: 16px; border-bottom: 1px solid #e2e8f0; padding-bottom: 12px;">
        <div>
            <span class="card-eyebrow" style="color: #db2777; font-weight: 800;">HASIL SCREENING AI — IBU HAMIL</span>
            <h2 style="margin: 4px 0 0 0; color: #0f172a; font-size: 20px;">{{ $aiM['nama'] }}</h2>
        </div>
        <div style="text-align: right;">
            <span class="status-pill warning" style="font-size: 11.5px;">⏳ Status: Menunggu Validasi Puskesmas</span>
            <div style="font-size: 11px; color: #64748b; margin-top: 4px;">Model: {{ $aiM['model_version'] }}</div>
        </div>
    </div>

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 16px; margin-bottom: 16px;">
        
        {{-- RISK LEVEL & PROBABILITY DISTRIBUTION --}}
        <div style="background: white; border: 1px solid #e2e8f0; border-radius: 12px; padding: 16px;">
            <span style="font-size: 12px; color: #64748b; font-weight: 600;">Risiko Kesehatan Maternal:</span>
            <div style="margin: 8px 0;">
                @if($aiM['risk_level'] === 'high')
                    <span class="status-pill danger" style="font-size: 14px; padding: 6px 14px;">🔴 TINGGI (HIGH RISK)</span>
                @elseif($aiM['risk_level'] === 'medium')
                    <span class="status-pill warning" style="font-size: 14px; padding: 6px 14px;">🟡 SEDANG (MEDIUM RISK)</span>
                @else
                    <span class="status-pill success" style="font-size: 14px; padding: 6px 14px;">🟢 RENDAH (LOW RISK)</span>
                @endif
            </div>

            {{-- PROBABILITY BREAKDOWN --}}
            <div style="margin-top: 12px;">
                <span style="font-size: 12px; color: #64748b; font-weight: 600;">Distribusi Probabilitas Model:</span>
                <div style="display: flex; flex-direction: column; gap: 4px; margin-top: 6px; font-size: 12px;">
                    <div style="display: flex; justify-content: space-between;">
                        <span style="color: #dc2626;">• High Risk:</span>
                        <strong>{{ round(($aiM['probabilities']['high'] ?? 0) * 100) }}%</strong>
                    </div>
                    <div style="display: flex; justify-content: space-between;">
                        <span style="color: #d97706;">• Medium Risk:</span>
                        <strong>{{ round(($aiM['probabilities']['medium'] ?? 0) * 100) }}%</strong>
                    </div>
                    <div style="display: flex; justify-content: space-between;">
                        <span style="color: #16a34a;">• Low Risk:</span>
                        <strong>{{ round(($aiM['probabilities']['low'] ?? 0) * 100) }}%</strong>
                    </div>
                </div>
            </div>

            <div style="margin-top: 14px; padding-top: 10px; border-top: 1px dashed #e2e8f0;">
                <span style="font-size: 12px; color: #64748b;">Tren Riwayat Maternal:</span>
                <div style="margin-top: 4px; font-size: 14px;">
                    {!! $aiM['sequence_html'] !!}
                    <span style="margin-left: 8px;">{!! $aiM['trend_badge'] !!}</span>
                </div>
            </div>
        </div>

        {{-- CLINICAL INDICATORS & RECOMMENDATION --}}
        <div style="background: white; border: 1px solid #e2e8f0; border-radius: 12px; padding: 16px; display: flex; flex-direction: column; justify-content: space-between;">
            <div>
                <span style="font-size: 12px; color: #64748b; font-weight: 600;">Indikator Pemeriksaan Terdeteksi:</span>
                <ul style="margin: 8px 0 12px 18px; padding: 0; font-size: 13px; color: #334155; line-height: 1.6;">
                    @foreach($aiM['indicators'] as $ind)
                        <li>✓ {{ $ind }}</li>
                    @endforeach
                </ul>
            </div>

            <div style="background: #fdf2f8; border: 1px solid #fbcfe8; border-radius: 8px; padding: 10px 12px;">
                <strong style="font-size: 12px; color: #831843; display: block; margin-bottom: 3px;">💡 Rekomendasi Sistem:</strong>
                <span style="font-size: 12.5px; color: #701a75;">{{ $aiM['recommendation'] }}</span>
            </div>
        </div>

    </div>

    {{-- DISCLAIMER --}}
    <div style="background: #fff1f2; border: 1px solid #fecdd3; border-radius: 8px; padding: 10px 14px; font-size: 12px; color: #9f1239; display: flex; align-items: center; gap: 8px;">
        <span style="font-size: 16px;">⚠️</span>
        <span><strong>Disclaimer:</strong> {{ $aiM['disclaimer'] }}</span>
    </div>
</div>
@endif

{{-- TAB SWITCHER --}}
<div style="display: flex; background: #f1f5f9; padding: 4px; border-radius: 12px; width: fit-content; gap: 4px; margin-bottom: 20px;">
    <a href="{{ route('kader.kegiatan.pemeriksaan', ['tab' => 'balita']) }}" 
       class="button {{ $tab === 'balita' ? 'primary' : 'secondary' }}" 
       style="border-radius: 8px; font-size: 13.5px; padding: 8px 20px; text-decoration: none;">
        👶 Form Pemeriksaan Balita
    </a>
    <a href="{{ route('kader.kegiatan.pemeriksaan', ['tab' => 'ibu_hamil']) }}" 
       class="button {{ $tab === 'ibu_hamil' ? 'primary' : 'secondary' }}" 
       style="border-radius: 8px; font-size: 13.5px; padding: 8px 20px; text-decoration: none;">
        🤰 Form Pemeriksaan Ibu Hamil
    </a>
</div>

{{-- 1. FORM PEMERIKSAAN BALITA --}}
@if($tab === 'balita')
<div class="dashboard-card">
    <div class="card-header">
        <div>
            <span class="card-eyebrow">FORMULIR REKAM MEDIK POSYANDU</span>
            <h3>Pemeriksaan & Pengukuran Balita</h3>
        </div>
    </div>

    <form method="POST" action="{{ route('kader.kegiatan.pemeriksaan.store') }}" style="margin-top: 20px;">
        @csrf
        <input type="hidden" name="kategori" value="balita">

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 16px; margin-bottom: 16px;">
            {{-- PILIH BALITA --}}
            <div>
                <label style="display: block; font-size: 13px; font-weight: 600; margin-bottom: 6px; color: #334155;">Pilih Balita <span style="color:#dc2626;">*</span></label>
                <select name="balita_id" class="form-input" required style="width: 100%; padding: 9px 12px; border-radius: 8px; border: 1px solid #cbd5e1;">
                    <option value="">-- Pilih Balita Terdaftar --</option>
                    @foreach($balitaList as $b)
                        <option value="{{ $b->id }}" {{ ($selectedBalita && $selectedBalita->id == $b->id) ? 'selected' : '' }}>
                            {{ $b->nama }} (NIK: {{ $b->nik }}) - {{ $b->umur_jk }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- TANGGAL PEMERIKSAAN --}}
            <div>
                <label style="display: block; font-size: 13px; font-weight: 600; margin-bottom: 6px; color: #334155;">Tanggal Pemeriksaan <span style="color:#dc2626;">*</span></label>
                <input type="date" name="tanggal_pemeriksaan" value="{{ now()->format('Y-m-d') }}" class="form-input" required style="width: 100%; padding: 9px 12px; border-radius: 8px; border: 1px solid #cbd5e1;">
            </div>

            {{-- BERAT BADAN --}}
            <div>
                <label style="display: block; font-size: 13px; font-weight: 600; margin-bottom: 6px; color: #334155;">Berat Badan (kg) <span style="color:#dc2626;">*</span></label>
                <input type="number" step="0.01" name="berat_badan" placeholder="Contoh: 9.40" class="form-input" required style="width: 100%; padding: 9px 12px; border-radius: 8px; border: 1px solid #cbd5e1;">
            </div>

            {{-- TINGGI BADAN --}}
            <div>
                <label style="display: block; font-size: 13px; font-weight: 600; margin-bottom: 6px; color: #334155;">Tinggi Badan (cm) <span style="color:#dc2626;">*</span></label>
                <input type="number" step="0.1" name="tinggi_badan" placeholder="Contoh: 76.5" class="form-input" required style="width: 100%; padding: 9px 12px; border-radius: 8px; border: 1px solid #cbd5e1;">
            </div>

            {{-- LINGKAR KEPALA --}}
            <div>
                <label style="display: block; font-size: 13px; font-weight: 600; margin-bottom: 6px; color: #334155;">Lingkar Kepala (cm)</label>
                <input type="number" step="0.1" name="lingkar_kepala" placeholder="Contoh: 46.0" class="form-input" style="width: 100%; padding: 9px 12px; border-radius: 8px; border: 1px solid #cbd5e1;">
            </div>

            {{-- STATUS IMUNISASI --}}
            <div>
                <label style="display: block; font-size: 13px; font-weight: 600; margin-bottom: 6px; color: #334155;">Status Imunisasi</label>
                <select name="status_imunisasi" class="form-input" style="width: 100%; padding: 9px 12px; border-radius: 8px; border: 1px solid #cbd5e1;">
                    <option value="lengkap">✓ Lengkap Sesuai Usia</option>
                    <option value="belum_lengkap">Belum Lengkap</option>
                    <option value="tertunda">Tertunda</option>
                </select>
            </div>
        </div>

        <div style="margin-bottom: 16px;">
            <label style="display: block; font-size: 13px; font-weight: 600; margin-bottom: 6px; color: #334155;">Catatan Perkembangan Balita:</label>
            <textarea name="catatan" rows="2" class="form-input" placeholder="Catatan asupan makan, nafsu makan, kondisi kesehatan anak..." style="width: 100%; padding: 9px 12px; border-radius: 8px; border: 1px solid #cbd5e1;"></textarea>
        </div>

        <div style="display: flex; align-items: center; justify-content: space-between; margin-top: 20px; border-top: 1px solid #e2e8f0; padding-top: 16px;">
            <label style="display: flex; align-items: center; gap: 8px; font-size: 13px; color: #475569; cursor: pointer;">
                <input type="checkbox" name="kehadiran" checked>
                <span>Hadir dalam kegiatan Posyandu hari ini</span>
            </label>

            <button type="submit" class="button primary" style="padding: 10px 24px; font-size: 14px;">
                🚀 Jalankan AI Screening & Simpan Pemeriksaan
            </button>
        </div>
    </form>
</div>
@endif

{{-- 2. FORM PEMERIKSAAN IBU HAMIL --}}
@if($tab === 'ibu_hamil')
<div class="dashboard-card">
    <div class="card-header">
        <div>
            <span class="card-eyebrow">FORMULIR REKAM MEDIK MATERNAL</span>
            <h3>Pemeriksaan Kesehatan Ibu Hamil</h3>
        </div>
    </div>

    <form method="POST" action="{{ route('kader.kegiatan.pemeriksaan.store') }}" style="margin-top: 20px;">
        @csrf
        <input type="hidden" name="kategori" value="ibu_hamil">

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 16px; margin-bottom: 16px;">
            {{-- PILIH IBU HAMIL --}}
            <div>
                <label style="display: block; font-size: 13px; font-weight: 600; margin-bottom: 6px; color: #334155;">Pilih Ibu Hamil <span style="color:#dc2626;">*</span></label>
                <select name="ibu_hamil_id" class="form-input" required style="width: 100%; padding: 9px 12px; border-radius: 8px; border: 1px solid #cbd5e1;">
                    <option value="">-- Pilih Ibu Hamil Terdaftar --</option>
                    @foreach($ibuHamilList as $bm)
                        <option value="{{ $bm->id }}" {{ ($selectedBumil && $selectedBumil->id == $bm->id) ? 'selected' : '' }}>
                            {{ $bm->nama }} (NIK: {{ $bm->nik }}) - Usia Hamil: {{ $bm->usia_kehamilan_minggu ?? '-' }} mgg
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- TANGGAL PEMERIKSAAN --}}
            <div>
                <label style="display: block; font-size: 13px; font-weight: 600; margin-bottom: 6px; color: #334155;">Tanggal Pemeriksaan <span style="color:#dc2626;">*</span></label>
                <input type="date" name="tanggal_pemeriksaan" value="{{ now()->format('Y-m-d') }}" class="form-input" required style="width: 100%; padding: 9px 12px; border-radius: 8px; border: 1px solid #cbd5e1;">
            </div>

            {{-- USIA KEHAMILAN --}}
            <div>
                <label style="display: block; font-size: 13px; font-weight: 600; margin-bottom: 6px; color: #334155;">Usia Kehamilan (Minggu)</label>
                <input type="number" name="usia_kehamilan_minggu" placeholder="Contoh: 24" class="form-input" style="width: 100%; padding: 9px 12px; border-radius: 8px; border: 1px solid #cbd5e1;">
            </div>

            {{-- BERAT BADAN --}}
            <div>
                <label style="display: block; font-size: 13px; font-weight: 600; margin-bottom: 6px; color: #334155;">Berat Badan Ibu (kg) <span style="color:#dc2626;">*</span></label>
                <input type="number" step="0.1" name="berat_badan" placeholder="Contoh: 58.5" class="form-input" required style="width: 100%; padding: 9px 12px; border-radius: 8px; border: 1px solid #cbd5e1;">
            </div>

            {{-- TEKANAN DARAH SISTOLIK --}}
            <div>
                <label style="display: block; font-size: 13px; font-weight: 600; margin-bottom: 6px; color: #334155;">Sistolik BP (mmHg) <span style="color:#dc2626;">*</span></label>
                <input type="number" name="systolic_bp" placeholder="Contoh: 120" class="form-input" required style="width: 100%; padding: 9px 12px; border-radius: 8px; border: 1px solid #cbd5e1;">
            </div>

            {{-- TEKANAN DARAH DIASTOLIK --}}
            <div>
                <label style="display: block; font-size: 13px; font-weight: 600; margin-bottom: 6px; color: #334155;">Diastolik BP (mmHg) <span style="color:#dc2626;">*</span></label>
                <input type="number" name="diastolic_bp" placeholder="Contoh: 80" class="form-input" required style="width: 100%; padding: 9px 12px; border-radius: 8px; border: 1px solid #cbd5e1;">
            </div>

            {{-- KADAR GULA DARAH --}}
            <div>
                <label style="display: block; font-size: 13px; font-weight: 600; margin-bottom: 6px; color: #334155;">Gula Darah (mmol/L)</label>
                <input type="number" step="0.1" name="blood_sugar" placeholder="Contoh: 7.2" class="form-input" style="width: 100%; padding: 9px 12px; border-radius: 8px; border: 1px solid #cbd5e1;">
            </div>

            {{-- SUHU TUBUH --}}
            <div>
                <label style="display: block; font-size: 13px; font-weight: 600; margin-bottom: 6px; color: #334155;">Suhu Tubuh (°F)</label>
                <input type="number" step="0.1" name="body_temp" value="98.6" placeholder="Contoh: 98.6" class="form-input" style="width: 100%; padding: 9px 12px; border-radius: 8px; border: 1px solid #cbd5e1;">
            </div>

            {{-- HEART RATE --}}
            <div>
                <label style="display: block; font-size: 13px; font-weight: 600; margin-bottom: 6px; color: #334155;">Denyut Jantung (bpm)</label>
                <input type="number" name="heart_rate" placeholder="Contoh: 76" class="form-input" style="width: 100%; padding: 9px 12px; border-radius: 8px; border: 1px solid #cbd5e1;">
            </div>
        </div>

        <div style="margin-bottom: 16px;">
            <label style="display: block; font-size: 13px; font-weight: 600; margin-bottom: 6px; color: #334155;">Catatan Keluhan / Kondisi Kehamilan:</label>
            <textarea name="catatan" rows="2" class="form-input" placeholder="Keluhan pusing, mual, bengkak kaki, konsumsi vitamin..." style="width: 100%; padding: 9px 12px; border-radius: 8px; border: 1px solid #cbd5e1;"></textarea>
        </div>

        <div style="display: flex; align-items: center; justify-content: space-between; margin-top: 20px; border-top: 1px solid #e2e8f0; padding-top: 16px;">
            <label style="display: flex; align-items: center; gap: 8px; font-size: 13px; color: #475569; cursor: pointer;">
                <input type="checkbox" name="kehadiran" checked>
                <span>Hadir dalam pemeriksaan Posyandu hari ini</span>
            </label>

            <button type="submit" class="button primary" style="padding: 10px 24px; font-size: 14px; background: #db2777; border: none;">
                🚀 Jalankan AI Maternal Screening & Simpan
            </button>
        </div>
    </form>
</div>
@endif

@endsection
