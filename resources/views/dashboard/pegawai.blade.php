@extends('layouts.app')

@section('title', 'SIPENA | Kepegawaian')

@section('breadcrumb')
    <div class="flex flex-wrap justify-center items-center space-x-2 text-sm text-gray-500 font-medium">
        <span>Beranda</span>
        <i class="fa-solid fa-chevron-right"></i>
        <a href="{{ route('kepegawaian') }}" class="text-indigo-400">Kepegawaian</a>
    </div>
@endsection

@section('content')

<style>
    .kpg-card {
        background: linear-gradient(135deg, #e0e7ff 0%, #f3e8ff 50%, #fce7f3 100%);
        border-radius: 1rem;
        padding: 1.5rem;
        box-shadow: 0 2px 8px rgba(99,102,241,0.07);
    }
    .kpg-input {
        width: 100%;
        background: #fff;
        border: 1.5px solid #e5e7eb;
        border-radius: 0.5rem;
        padding: 0.55rem 0.85rem;
        font-size: 0.875rem;
        color: #374151;
        outline: none;
        transition: border-color 0.15s;
    }
    .kpg-input:focus { border-color: #6366f1; box-shadow: 0 0 0 3px rgba(99,102,241,0.1); }
    .kpg-input::placeholder { color: #9ca3af; }
    .kpg-label {
        display: block;
        font-size: 0.8125rem;
        font-weight: 500;
        color: #374151;
        margin-bottom: 0.35rem;
    }
    .kpg-select {
        width: 100%;
        background: #fff url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%236b7280' d='M6 8L1 3h10z'/%3E%3C/svg%3E") no-repeat right 0.75rem center;
        background-size: 10px;
        border: 1.5px solid #e5e7eb;
        border-radius: 0.5rem;
        padding: 0.55rem 2rem 0.55rem 0.85rem;
        font-size: 0.875rem;
        color: #374151;
        outline: none;
        appearance: none;
        cursor: pointer;
        transition: border-color 0.15s;
    }
    .kpg-select:focus { border-color: #6366f1; box-shadow: 0 0 0 3px rgba(99,102,241,0.1); }
    .kpg-radio-group { display: flex; align-items: center; gap: 1.25rem; flex-wrap: wrap; }
    .kpg-radio-label {
        display: flex; align-items: center; gap: 0.4rem;
        font-size: 0.875rem; color: #374151; cursor: pointer;
    }
    .kpg-radio-label input[type="radio"] { accent-color: #6366f1; width: 16px; height: 16px; cursor: pointer; }
    .kpg-check-label {
        display: flex; align-items: center; gap: 0.5rem;
        font-size: 0.875rem; color: #374151; cursor: pointer; margin-bottom: 0.4rem;
    }
    .kpg-check-label input[type="checkbox"] { accent-color: #6366f1; width: 16px; height: 16px; cursor: pointer; }
    .kpg-input-icon { position: relative; }
    .kpg-input-icon .kpg-input { padding-right: 2.5rem; }
    .kpg-input-icon .icon-right {
        position: absolute; right: 0.7rem; top: 50%; transform: translateY(-50%);
        color: #9ca3af; font-size: 0.85rem; pointer-events: none;
    }
    input[type="date"]::-webkit-calendar-picker-indicator {
        opacity: 0;
        position: absolute;
        right: 0;
        width: 2.5rem;
        height: 100%;
        cursor: pointer;
    }
    input[type="date"] { position: relative; }
    .kpg-section-title {
        font-size: 1rem; font-weight: 600; color: #4338ca;
        margin-bottom: 1rem; padding-bottom: 0.5rem;
        border-bottom: 2px solid rgba(99,102,241,0.15);
    }
    .grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; }
    .grid-3 { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1rem; }

    /* Grid luar untuk 2 card berdampingan */
    .kpg-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1.25rem;
        margin-bottom: 1.25rem;
    }
    /* Baris terakhir: card di tengah, lebar separuh */
    .kpg-row-center {
        display: flex;
        justify-content: center;
        margin-bottom: 0;
    }
    .kpg-row-center .kpg-card {
        width: 50%;
    }

    @media (max-width: 768px) {
        .kpg-row { grid-template-columns: 1fr; }
        .grid-2 { grid-template-columns: 1fr; }
        .grid-3 { grid-template-columns: 1fr; }
        .kpg-row-center .kpg-card { width: 100%; }
    }
</style>

<h1 class="text-2xl font-bold mb-6 text-gray-800">Data Kepegawaian</h1>

{{-- ============================================================ --}}
{{-- BARIS 1: Card 1 (Data Pribadi) + Card 2 (NPWP & Tanggal)    --}}
{{-- ============================================================ --}}
<div class="kpg-row">

    {{-- CARD 1: DATA PRIBADI (kiri) --}}
    <div class="kpg-card">
        <p class="kpg-section-title"><i class="fa-solid fa-user mr-2"></i>Data Pribadi</p>

        <div style="display:flex;flex-direction:column;gap:1rem;">
            <div>
                <label class="kpg-label">Nama Lengkap</label>
                <input type="text" class="kpg-input" placeholder="Your First Name"
                    name="nama_lengkap" value="{{ old('nama_lengkap', $karyawan->nama_lengkap ?? '') }}">
            </div>

            <div>
                <label class="kpg-label">Jenis Kelamin</label>
                <div class="kpg-radio-group">
                    <label class="kpg-radio-label">
                        <input type="radio" name="jenis_kelamin" value="L"
                            {{ (old('jenis_kelamin', $karyawan->jenis_kelamin ?? 'L') === 'L') ? 'checked' : '' }}>
                        Laki-laki
                    </label>
                    <label class="kpg-radio-label">
                        <input type="radio" name="jenis_kelamin" value="P"
                            {{ (old('jenis_kelamin', $karyawan->jenis_kelamin ?? '') === 'P') ? 'checked' : '' }}>
                        Wanita
                    </label>
                </div>
            </div>

            <div>
                <label class="kpg-label">Status Kawin</label>
                <div class="kpg-radio-group">
                    <label class="kpg-radio-label">
                        <input type="radio" name="status_kawin" value="kawin"
                            {{ (old('status_kawin', $karyawan->status_kawin ?? 'kawin') === 'kawin') ? 'checked' : '' }}>
                        Kawin
                    </label>
                    <label class="kpg-radio-label">
                        <input type="radio" name="status_kawin" value="belum_kawin"
                            {{ (old('status_kawin', $karyawan->status_kawin ?? '') === 'belum_kawin') ? 'checked' : '' }}>
                        Belum Kawin
                    </label>
                </div>
            </div>

            <div>
                <label class="kpg-label">Kewarganegaraan</label>
                <div class="kpg-radio-group">
                    <label class="kpg-radio-label">
                        <input type="radio" name="kewarganegaraan" value="WNI"
                            {{ (old('kewarganegaraan', $karyawan->kewarganegaraan ?? 'WNI') === 'WNI') ? 'checked' : '' }}>
                        WNI
                    </label>
                    <label class="kpg-radio-label">
                        <input type="radio" name="kewarganegaraan" value="WNA"
                            {{ (old('kewarganegaraan', $karyawan->kewarganegaraan ?? '') === 'WNA') ? 'checked' : '' }}>
                        WNA
                    </label>
                </div>
            </div>

            <div>
                <label class="kpg-label">Negara</label>
                <input type="text" class="kpg-input" placeholder="Your First Name"
                    name="negara" value="{{ old('negara', $karyawan->negara ?? '') }}">
            </div>

            <div>
                <label class="kpg-label">Kode Negara</label>
                <input type="text" class="kpg-input" placeholder="Your First Name"
                    name="kode_negara" value="{{ old('kode_negara', $karyawan->kode_negara ?? '') }}">
            </div>
        </div>
    </div>

    {{-- CARD 2: NPWP & TANGGAL (kanan) --}}
    <div class="kpg-card">
        <p class="kpg-section-title"><i class="fa-solid fa-id-card mr-2"></i>NPWP & Tanggal</p>

        <div style="display:flex;flex-direction:column;gap:1rem;">
            <div>
                <label class="kpg-label">Tanggal Mulai Kerja</label>
                <div class="kpg-input-icon">
                    <input type="date" class="kpg-input" name="tanggal_mulai_kerja"
                        value="{{ old('tanggal_mulai_kerja', isset($karyawan->tanggal_mulai_kerja) ? \Carbon\Carbon::parse($karyawan->tanggal_mulai_kerja)->format('Y-m-d') : '') }}">
                    <i class="fa-regular fa-calendar icon-right"></i>
                </div>
            </div>

            <div>
                <label class="kpg-label">Nomor NPWP</label>
                <input type="text" class="kpg-input" placeholder="Your First Name"
                    name="npwp" value="{{ old('npwp', $karyawan->npwp ?? '') }}">
            </div>

            <div>
                <label class="kpg-label">Tanggal Pembuatan</label>
                <div class="kpg-input-icon">
                    <input type="date" class="kpg-input" name="tanggal_pembuatan_npwp"
                        value="{{ old('tanggal_pembuatan_npwp', isset($karyawan->tanggal_pembuatan_npwp) ? \Carbon\Carbon::parse($karyawan->tanggal_pembuatan_npwp)->format('Y-m-d') : '') }}">
                    <i class="fa-regular fa-calendar icon-right"></i>
                </div>
            </div>

            <div>
                <label class="kpg-label">Tanggal Aktif NPWM</label>
                <div class="kpg-input-icon">
                    <input type="date" class="kpg-input" name="tanggal_aktif_npwm"
                        value="{{ old('tanggal_aktif_npwm', isset($karyawan->tanggal_aktif_npwm) ? \Carbon\Carbon::parse($karyawan->tanggal_aktif_npwm)->format('Y-m-d') : '') }}">
                    <i class="fa-regular fa-calendar icon-right"></i>
                </div>
            </div>

            <div>
                <label class="kpg-label">Tanggal Habis Berlaku</label>
                <div class="kpg-input-icon">
                    <input type="date" class="kpg-input" name="tanggal_habis_berlaku"
                        value="{{ old('tanggal_habis_berlaku', isset($karyawan->tanggal_habis_berlaku) ? \Carbon\Carbon::parse($karyawan->tanggal_habis_berlaku)->format('Y-m-d') : '') }}">
                    <i class="fa-regular fa-calendar icon-right"></i>
                </div>
            </div>
        </div>
    </div>

</div>{{-- end baris 1 --}}

{{-- ============================================================ --}}
{{-- BARIS 2: Card 3 (Status & Pajak) + Card 4 (BPJS & Gaji)     --}}
{{-- ============================================================ --}}
<div class="kpg-row">

    {{-- CARD 3: STATUS KARYAWAN & PAJAK (kiri) --}}
    <div class="kpg-card">
        <p class="kpg-section-title"><i class="fa-solid fa-briefcase mr-2"></i>Status Karyawan & Pajak</p>

        <div style="display:flex;flex-direction:column;gap:1rem;">
            <div>
                <label class="kpg-label">Status Karyawan</label>
                <div class="grid-2" style="gap:0.5rem;margin-bottom:0.5rem;">
                    <input type="text" class="kpg-input" placeholder="ID / Kode"
                        name="kode_karyawan" value="{{ old('kode_karyawan', $karyawan->kode_karyawan ?? '1') }}"
                        style="text-align:center;">
                    <input type="text" class="kpg-input" placeholder="Divisi"
                        name="divisi" value="{{ old('divisi', $karyawan->divisi ?? '11-100-01') }}"
                        style="text-align:center;">
                </div>
                <input type="text" class="kpg-input" placeholder="Jabatan / Status"
                    name="jabatan" value="{{ old('jabatan', $karyawan->jabatan ?? 'Pegawai Tetap') }}">
            </div>

            <div>
                <label class="kpg-label">Tarif Objek Pajak</label>
                <div class="grid-2" style="gap:0.5rem;margin-bottom:0.5rem;">
                    <input type="text" class="kpg-input" placeholder="Objek Pajak"
                        name="objek_pajak" value="{{ old('objek_pajak', $karyawan->objek_pajak ?? 'Objek1') }}">
                    <input type="number" class="kpg-input" placeholder="0.0"
                        name="tarif_pajak" value="{{ old('tarif_pajak', $karyawan->tarif_pajak ?? '0.0') }}" step="0.1">
                </div>
                <input type="text" class="kpg-input" placeholder="Keterangan penghasilan"
                    name="keterangan_penghasilan"
                    value="{{ old('keterangan_penghasilan', $karyawan->keterangan_penghasilan ?? 'Penghasilan yang diterima pegawai tetap') }}">
            </div>

            <div>
                <label class="kpg-label">Cara Pembayaran</label>
                <select class="kpg-select" name="cara_pembayaran">
                    <option value="bulanan"  {{ (old('cara_pembayaran', $karyawan->cara_pembayaran ?? 'bulanan') === 'bulanan') ? 'selected' : '' }}>Bulanan</option>
                    <option value="mingguan" {{ (old('cara_pembayaran', $karyawan->cara_pembayaran ?? '') === 'mingguan') ? 'selected' : '' }}>Mingguan</option>
                    <option value="harian"   {{ (old('cara_pembayaran', $karyawan->cara_pembayaran ?? '') === 'harian') ? 'selected' : '' }}>Harian</option>
                </select>
            </div>

            <div class="grid-2" style="gap:0.75rem;">
                <div>
                    <label class="kpg-label">Tipe Penghasilan</label>
                    <select class="kpg-select" name="tipe_penghasilan">
                        <option value="berkesinambungan" {{ (old('tipe_penghasilan', $karyawan->tipe_penghasilan ?? 'berkesinambungan') === 'berkesinambungan') ? 'selected' : '' }}>Berkesinambungan</option>
                        <option value="tidak_berkesinambungan">Tidak Berkesinambungan</option>
                    </select>
                </div>
                <div>
                    <label class="kpg-label">Dipotong Oleh</label>
                    <select class="kpg-select" name="dipotong_oleh">
                        <option value="satu_pemberi_kerja" {{ (old('dipotong_oleh', $karyawan->dipotong_oleh ?? 'satu_pemberi_kerja') === 'satu_pemberi_kerja') ? 'selected' : '' }}>Satu Pemberi Kerja</option>
                        <option value="lebih_satu">Lebih dari Satu</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    {{-- CARD 4: BPJS & INFORMASI GAJI (kanan) --}}
    <div class="kpg-card">
        <p class="kpg-section-title"><i class="fa-solid fa-shield-halved mr-2"></i>BPJS & Informasi Gaji</p>

        <div style="display:flex;flex-direction:column;gap:1rem;">
            <div>
                <div style="background:#fff;border-radius:0.5rem;padding:0.85rem 1rem;border:1.5px solid #e5e7eb;">
                    <label class="kpg-check-label">
                        <input type="checkbox" name="peserta_bpjs_kesehatan" value="1"
                            {{ old('peserta_bpjs_kesehatan', $karyawan->peserta_bpjs_kesehatan ?? 1) ? 'checked' : '' }}>
                        Peserta BPJS Kesehatan
                    </label>
                    <label class="kpg-check-label">
                        <input type="checkbox" name="peserta_jamsostek" value="1"
                            {{ old('peserta_jamsostek', $karyawan->peserta_jamsostek ?? 1) ? 'checked' : '' }}>
                        Peserta Jamsostek/BPJS Ketenagakerjaan
                    </label>
                    <label class="kpg-check-label" style="margin-bottom:0;">
                        <input type="checkbox" name="berhak_lembur" value="1"
                            {{ old('berhak_lembur', $karyawan->berhak_lembur ?? 1) ? 'checked' : '' }}>
                        Karyawan Berhak Mendapat Lembur/Overtime
                    </label>
                </div>
            </div>

            <div>
                <label class="kpg-label">No. BPJS Kesehatan</label>
                <input type="text" class="kpg-input" placeholder="Your First Name"
                    name="no_bpjs_kesehatan" value="{{ old('no_bpjs_kesehatan', $karyawan->no_bpjs_kesehatan ?? '') }}">
            </div>

            <div>
                <label class="kpg-label">No. Jamsostek/BPJS Ketenagakerjaan</label>
                <input type="text" class="kpg-input" placeholder="Your First Name"
                    name="no_jamsostek" value="{{ old('no_jamsostek', $karyawan->no_jamsostek ?? '') }}">
            </div>

            <div>
                <label class="kpg-label">Masa Perolehan</label>
                <div class="grid-2" style="gap:0.5rem;">
                    <div>
                        <label style="font-size:0.75rem;color:#6b7280;display:block;margin-bottom:0.25rem;">Dari</label>
                        <select class="kpg-select" name="masa_perolehan_dari">
                            @for ($i = 1; $i <= 12; $i++)
                                <option value="{{ $i }}" {{ (old('masa_perolehan_dari', $karyawan->masa_perolehan_dari ?? 1) == $i) ? 'selected' : '' }}>
                                    {{ \Carbon\Carbon::create()->month($i)->translatedFormat('F') }}
                                </option>
                            @endfor
                        </select>
                    </div>
                    <div>
                        <label style="font-size:0.75rem;color:#6b7280;display:block;margin-bottom:0.25rem;">Hingga</label>
                        <select class="kpg-select" name="masa_perolehan_hingga">
                            @for ($i = 1; $i <= 12; $i++)
                                <option value="{{ $i }}" {{ (old('masa_perolehan_hingga', $karyawan->masa_perolehan_hingga ?? 12) == $i) ? 'selected' : '' }}>
                                    {{ \Carbon\Carbon::create()->month($i)->translatedFormat('F') }}
                                </option>
                            @endfor
                        </select>
                    </div>
                </div>
            </div>

            <div>
                <label class="kpg-label">Tipe Gaji</label>
                <input type="text" class="kpg-input" placeholder="Your First Name"
                    name="tipe_gaji" value="{{ old('tipe_gaji', $karyawan->tipe_gaji ?? '') }}">
            </div>
        </div>
    </div>

</div>{{-- end baris 2 --}}

{{-- ============================================================ --}}
{{-- BARIS 3: Card 5 (Berhenti Kerja) tengah                     --}}
{{-- ============================================================ --}}
<div class="kpg-row-center">
    <div class="kpg-card">
        <p class="kpg-section-title"><i class="fa-solid fa-right-from-bracket mr-2"></i>Berhenti Kerja</p>

        <div style="display:flex;flex-direction:column;gap:1rem;">
            <div>
                <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:0.35rem;">
                    <label class="kpg-label" style="margin-bottom:0;">Tanggal Berhenti Kerja</label>
                    <label class="kpg-check-label" style="margin-bottom:0;">
                        <input type="checkbox" name="pesangon" value="1"
                            {{ old('pesangon', $karyawan->pesangon ?? 0) ? 'checked' : '' }}>
                        Pesangon
                    </label>
                </div>
                <div class="kpg-input-icon">
                    <input type="date" class="kpg-input" name="tanggal_berhenti"
                        value="{{ old('tanggal_berhenti', isset($karyawan->tanggal_berhenti) ? \Carbon\Carbon::parse($karyawan->tanggal_berhenti)->format('Y-m-d') : '') }}">
                    <i class="fa-regular fa-calendar icon-right"></i>
                </div>
            </div>

            <div class="grid-2" style="gap:0.75rem;">
                <div>
                    <label class="kpg-label">Alasan Keluar</label>
                    <select class="kpg-select" name="alasan_keluar">
                        <option value="">-- Pilih Alasan --</option>
                        <option value="resign"        {{ old('alasan_keluar', $karyawan->alasan_keluar ?? '') === 'resign' ? 'selected' : '' }}>Mengundurkan Diri</option>
                        <option value="phk"           {{ old('alasan_keluar', $karyawan->alasan_keluar ?? '') === 'phk' ? 'selected' : '' }}>PHK</option>
                        <option value="pensiun"       {{ old('alasan_keluar', $karyawan->alasan_keluar ?? '') === 'pensiun' ? 'selected' : '' }}>Pensiun</option>
                        <option value="meninggal"     {{ old('alasan_keluar', $karyawan->alasan_keluar ?? '') === 'meninggal' ? 'selected' : '' }}>Meninggal Dunia</option>
                        <option value="kontrak_habis" {{ old('alasan_keluar', $karyawan->alasan_keluar ?? '') === 'kontrak_habis' ? 'selected' : '' }}>Kontrak Habis</option>
                    </select>
                </div>
                <div>
                    <label class="kpg-label">Pajak Atas Pesangon</label>
                    <input type="number" class="kpg-input" placeholder="Nominal"
                        name="pajak_pesangon"
                        value="{{ old('pajak_pesangon', $karyawan->pajak_pesangon ?? '') }}"
                        step="0.01">
                </div>
            </div>
        </div>
    </div>
</div>{{-- end baris 3 --}}

@endsection