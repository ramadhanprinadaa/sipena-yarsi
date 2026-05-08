@extends('layouts.app')

@section('title', 'SIPENA | Presensi')

@section('breadcrumb')
    <div class="flex flex-wrap justify-center items-center space-x-2 text-sm text-gray-500 font-medium">
        <span>Beranda</span>
        <i class="fa-solid fa-chevron-right"></i>
        <a href="{{ route('presensi') }}" class="text-indigo-400">Presensi</a>
    </div>
@endsection

@section('content')

{{-- Riwayat Presensi Harian --}}
<div style="background: linear-gradient(135deg, #dde4ff 0%, #ede9fb 40%, #fce4f0 100%); border-radius: 20px; padding: 28px; margin-bottom: 24px; box-shadow: 0 2px 12px rgba(99,102,241,0.07);">
    <div style="display:flex; flex-wrap:wrap; justify-content:space-between; align-items:center; margin-bottom:24px; gap:12px;">
        <h2 style="font-size:22px; font-weight:700; color:#1e1b4b; margin:0;">Riwayat Presensi Harian</h2>
        <div style="display:flex; align-items:center; gap:12px;">

            {{-- Single Date Picker --}}
            <div class="relative" id="wrap-cal-riwayat">
                <button type="button" id="btn-cal-riwayat"
                    style="display:flex;align-items:center;gap:8px;background:#fff;border:1.5px solid #e5e7eb;border-radius:10px;padding:8px 14px;font-size:13px;color:#4b5563;box-shadow:0 1px 4px rgba(0,0,0,0.06);cursor:pointer;transition:border-color .2s;">
                    <i class="fa-regular fa-calendar" style="color:#a5b4fc;"></i>
                    <span id="cal-riwayat-label" style="font-weight:500;">31 Okt 2026</span>
                    <i class="fa-solid fa-chevron-down" style="font-size:10px;color:#9ca3af;"></i>
                </button>
                <div id="cal-riwayat"
                    style="display:none;position:absolute;right:0;top:calc(100% + 8px);z-index:9999;background:#fff;border:1px solid #e5e7eb;border-radius:12px;box-shadow:0 10px 25px rgba(0,0,0,0.12);padding:16px;width:280px;">
                    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:12px;">
                        <button type="button" id="prev-cal-riwayat"
                            style="width:28px;height:28px;border-radius:8px;border:none;background:none;cursor:pointer;color:#6b7280;font-size:11px;">&#8249;</button>
                        <span id="cal-riwayat-month" style="font-size:14px;font-weight:600;color:#374151;"></span>
                        <button type="button" id="next-cal-riwayat"
                            style="width:28px;height:28px;border-radius:8px;border:none;background:none;cursor:pointer;color:#6b7280;font-size:11px;">&#8250;</button>
                    </div>
                    <div style="display:grid;grid-template-columns:repeat(7,1fr);text-align:center;font-size:11px;color:#9ca3af;font-weight:500;margin-bottom:6px;">
                        <span>Min</span><span>Sen</span><span>Sel</span><span>Rab</span><span>Kam</span><span>Jum</span><span>Sab</span>
                    </div>
                    <div id="cal-riwayat-days" style="display:grid;grid-template-columns:repeat(7,1fr);gap:2px;"></div>
                </div>
            </div>

            <a href="{{ route('presensi.export.riwayat') }}"
                style="display:flex;align-items:center;gap:8px;background:#22c55e;color:#fff;font-size:13px;font-weight:600;padding:8px 18px;border-radius:10px;box-shadow:0 2px 8px rgba(34,197,94,0.25);text-decoration:none;transition:background .2s;"
                onmouseover="this.style.background='#16a34a'" onmouseout="this.style.background='#22c55e'">
                <i class="fa-solid fa-download" style="font-size:12px;"></i>
                Export Excel
            </a>
        </div>
    </div>

    <div style="overflow-x:auto;">
        <table style="width:100%; border-collapse:collapse; font-size:13.5px;">
            <thead>
                <tr style="border-bottom:1.5px solid #e5e7eb;">
                    <th style="padding:12px 16px; text-align:left; color:#6b7280; font-weight:600; white-space:nowrap;">Tanggal</th>
                    <th style="padding:12px 16px; text-align:center; color:#6b7280; font-weight:600; white-space:nowrap;">Jam Masuk</th>
                    <th style="padding:12px 16px; text-align:center; color:#6b7280; font-weight:600; white-space:nowrap;">Jam Keluar</th>
                    <th style="padding:12px 16px; text-align:center; color:#6b7280; font-weight:600; white-space:nowrap;">Total Jam</th>
                    <th style="padding:12px 16px; text-align:center; color:#6b7280; font-weight:600; white-space:nowrap;">Status</th>
                    <th style="padding:12px 16px; text-align:center; color:#6b7280; font-weight:600; white-space:nowrap;">Status Lembur</th>
                    <th style="padding:12px 16px; text-align:center; color:#6b7280; font-weight:600; white-space:nowrap;">Jam Lembur</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $riwayat = [
                        ['tanggal' => '23 October 2026', 'masuk' => '9:00', 'keluar' => '9:00', 'total' => '9j 10m', 'status' => 'Tepat Waktu', 'lembur' => 'Disetujui', 'jam_lembur' => '3j'],
                        ['tanggal' => '23 October 2026', 'masuk' => '9:00', 'keluar' => '9:00', 'total' => '9j 10m', 'status' => 'Tepat Waktu', 'lembur' => 'Disetujui', 'jam_lembur' => '3j'],
                        ['tanggal' => '23 October 2026', 'masuk' => '9:00', 'keluar' => '9:00', 'total' => '9j 10m', 'status' => 'Tepat Waktu', 'lembur' => 'Disetujui', 'jam_lembur' => '3j'],
                        ['tanggal' => '23 October 2026', 'masuk' => '9:00', 'keluar' => '9:00', 'total' => '9j 10m', 'status' => 'Tepat Waktu', 'lembur' => 'Disetujui', 'jam_lembur' => '3j'],
                    ];
                @endphp
                @foreach ($riwayat as $row)
                <tr style="border-bottom:1px solid #f0f0f5; background:rgba(255,255,255,0.6); transition:background .15s;" onmouseover="this.style.background='rgba(255,255,255,0.95)'" onmouseout="this.style.background='rgba(255,255,255,0.6)'">
                    <td style="padding:13px 16px; color:#374151; font-weight:500;">{{ $row['tanggal'] }}</td>
                    <td style="padding:13px 16px; text-align:center; color:#6366f1; font-weight:600;">{{ $row['masuk'] }}</td>
                    <td style="padding:13px 16px; text-align:center; color:#f87171; font-weight:600;">{{ $row['keluar'] }}</td>
                    <td style="padding:13px 16px; text-align:center; color:#4b5563; font-weight:500;">{{ $row['total'] }}</td>
                    <td style="padding:13px 16px; text-align:center;">
                        <span style="display:inline-flex;align-items:center;gap:6px;background:#dcfce7;color:#16a34a;font-size:12px;font-weight:600;padding:4px 14px;border-radius:999px;">
                            <span style="width:6px;height:6px;border-radius:50%;background:#22c55e;flex-shrink:0;"></span>
                            {{ $row['status'] }}
                        </span>
                    </td>
                    <td style="padding:13px 16px; text-align:center;">
                        <span style="display:inline-flex;align-items:center;gap:6px;color:#16a34a;font-size:12px;font-weight:600;">
                            <span style="width:8px;height:8px;border-radius:50%;background:#4ade80;flex-shrink:0;"></span>
                            {{ $row['lembur'] }}
                        </span>
                    </td>
                    <td style="padding:13px 16px; text-align:center; color:#4b5563; font-weight:500;">{{ $row['jam_lembur'] }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    <div style="display:flex;flex-wrap:wrap;justify-content:space-between;align-items:center;margin-top:18px;gap:8px;font-size:13px;color:#9ca3af;">
        <span>Menampilkan 1 - 5 dari 10.000 riwayat lembur</span>
        <div style="display:flex;align-items:center;gap:4px;">
            <button style="width:32px;height:32px;display:flex;align-items:center;justify-content:center;border-radius:8px;border:none;background:transparent;cursor:pointer;color:#9ca3af;" onmouseover="this.style.background='#fff'" onmouseout="this.style.background='transparent'">
                <i class="fa-solid fa-chevron-left" style="font-size:11px;"></i>
            </button>
            <button style="width:32px;height:32px;display:flex;align-items:center;justify-content:center;border-radius:8px;border:none;background:#6366f1;color:#fff;font-weight:700;cursor:pointer;box-shadow:0 2px 6px rgba(99,102,241,0.3);">1</button>
            <button style="width:32px;height:32px;display:flex;align-items:center;justify-content:center;border-radius:8px;border:none;background:transparent;color:#374151;font-weight:500;cursor:pointer;" onmouseover="this.style.background='#fff'" onmouseout="this.style.background='transparent'">2</button>
            <button style="width:32px;height:32px;display:flex;align-items:center;justify-content:center;border-radius:8px;border:none;background:transparent;color:#374151;font-weight:500;cursor:pointer;" onmouseover="this.style.background='#fff'" onmouseout="this.style.background='transparent'">3</button>
            <span style="padding:0 4px;color:#9ca3af;">...</span>
            <button style="width:32px;height:32px;display:flex;align-items:center;justify-content:center;border-radius:8px;border:none;background:transparent;color:#374151;font-weight:500;cursor:pointer;" onmouseover="this.style.background='#fff'" onmouseout="this.style.background='transparent'">999</button>
            <button style="width:32px;height:32px;display:flex;align-items:center;justify-content:center;border-radius:8px;border:none;background:transparent;cursor:pointer;color:#9ca3af;" onmouseover="this.style.background='#fff'" onmouseout="this.style.background='transparent'">
                <i class="fa-solid fa-chevron-right" style="font-size:11px;"></i>
            </button>
        </div>
    </div>
</div>

{{-- Rekapitulasi Presensi --}}
<div style="background: linear-gradient(135deg, #dde4ff 0%, #ede9fb 40%, #fce4f0 100%); border-radius: 20px; padding: 28px; box-shadow: 0 2px 12px rgba(99,102,241,0.07);">
    <div style="display:flex; flex-wrap:wrap; justify-content:space-between; align-items:center; margin-bottom:24px; gap:12px;">
        <h2 style="font-size:22px; font-weight:700; color:#1e1b4b; margin:0;">Rekapitulasi Presensi</h2>
        <div style="display:flex; align-items:center; gap:12px;">

            {{-- Range Date Picker --}}
            <div class="relative" id="wrap-cal-rekap">
                <button type="button" id="btn-cal-rekap"
                    style="display:flex;align-items:center;gap:8px;background:#fff;border:1.5px solid #e5e7eb;border-radius:10px;padding:8px 14px;font-size:13px;color:#4b5563;box-shadow:0 1px 4px rgba(0,0,0,0.06);cursor:pointer;transition:border-color .2s;">
                    <i class="fa-regular fa-calendar" style="color:#a5b4fc;"></i>
                    <span id="cal-rekap-label" style="font-weight:500;">01 Okt - 31 Okt 2026</span>
                    <i class="fa-solid fa-chevron-down" style="font-size:10px;color:#9ca3af;"></i>
                </button>
                <div id="cal-rekap"
                    style="display:none;position:absolute;right:0;top:calc(100% + 8px);z-index:9999;background:#fff;border:1px solid #e5e7eb;border-radius:12px;box-shadow:0 10px 25px rgba(0,0,0,0.12);padding:16px;width:280px;">
                    <p id="cal-rekap-hint" style="font-size:11px;color:#9ca3af;text-align:center;margin:0 0 8px;">Pilih tanggal awal</p>
                    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:12px;">
                        <button type="button" id="prev-cal-rekap"
                            style="width:28px;height:28px;border-radius:8px;border:none;background:none;cursor:pointer;color:#6b7280;font-size:11px;">&#8249;</button>
                        <span id="cal-rekap-month" style="font-size:14px;font-weight:600;color:#374151;"></span>
                        <button type="button" id="next-cal-rekap"
                            style="width:28px;height:28px;border-radius:8px;border:none;background:none;cursor:pointer;color:#6b7280;font-size:11px;">&#8250;</button>
                    </div>
                    <div style="display:grid;grid-template-columns:repeat(7,1fr);text-align:center;font-size:11px;color:#9ca3af;font-weight:500;margin-bottom:6px;">
                        <span>Min</span><span>Sen</span><span>Sel</span><span>Rab</span><span>Kam</span><span>Jum</span><span>Sab</span>
                    </div>
                    <div id="cal-rekap-days" style="display:grid;grid-template-columns:repeat(7,1fr);gap:2px;"></div>
                </div>
            </div>

            <a href="{{ route('presensi.export.rekap') }}"
                style="display:flex;align-items:center;gap:8px;background:#22c55e;color:#fff;font-size:13px;font-weight:600;padding:8px 18px;border-radius:10px;box-shadow:0 2px 8px rgba(34,197,94,0.25);text-decoration:none;transition:background .2s;"
                onmouseover="this.style.background='#16a34a'" onmouseout="this.style.background='#22c55e'">
                <i class="fa-solid fa-download" style="font-size:12px;"></i>
                Export Excel
            </a>
        </div>
    </div>

    <div style="overflow-x:auto;">
        <table style="width:100%; border-collapse:collapse; font-size:13.5px;">
            <thead>
                <tr style="border-bottom:1.5px solid #e5e7eb;">
                    <th style="padding:12px 16px; text-align:center; color:#6b7280; font-weight:600; white-space:nowrap;">Hadir (Jumlah)</th>
                    <th style="padding:12px 16px; text-align:center; color:#6b7280; font-weight:600; white-space:nowrap;">Tidak Hadir (Jumlah)</th>
                    <th style="padding:12px 16px; text-align:center; color:#6b7280; font-weight:600; white-space:nowrap;">Total Jam</th>
                    <th style="padding:12px 16px; text-align:center; color:#6b7280; font-weight:600; white-space:nowrap;">Total Lembur</th>
                    <th style="padding:12px 16px; text-align:center; color:#6b7280; font-weight:600; white-space:nowrap;">Total Jam Lembur</th>
                    <th style="padding:12px 16px; text-align:center; color:#6b7280; font-weight:600; white-space:nowrap;">Izin</th>
                    <th style="padding:12px 16px; text-align:center; color:#6b7280; font-weight:600; white-space:nowrap;">Sakit</th>
                    <th style="padding:12px 16px; text-align:center; color:#6b7280; font-weight:600; white-space:nowrap;">Cuti</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $rekap = [
                        ['hadir' => 27, 'tidak_hadir' => 5, 'total_jam' => '9j 10m', 'total_lembur' => 10, 'jam_lembur' => '3j', 'izin' => 1, 'sakit' => 5, 'cuti' => 11],
                        ['hadir' => 27, 'tidak_hadir' => 5, 'total_jam' => '9j 10m', 'total_lembur' => 10, 'jam_lembur' => '3j', 'izin' => 1, 'sakit' => 5, 'cuti' => 11],
                        ['hadir' => 27, 'tidak_hadir' => 5, 'total_jam' => '9j 10m', 'total_lembur' => 10, 'jam_lembur' => '3j', 'izin' => 1, 'sakit' => 5, 'cuti' => 11],
                        ['hadir' => 27, 'tidak_hadir' => 5, 'total_jam' => '9j 10m', 'total_lembur' => 10, 'jam_lembur' => '3j', 'izin' => 1, 'sakit' => 5, 'cuti' => 11],
                    ];
                @endphp
                @foreach ($rekap as $row)
                <tr style="border-bottom:1px solid #f0f0f5; background:rgba(255,255,255,0.6); transition:background .15s;" onmouseover="this.style.background='rgba(255,255,255,0.95)'" onmouseout="this.style.background='rgba(255,255,255,0.6)'">
                    <td style="padding:13px 16px; text-align:center;">
                        <span style="display:inline-block;background:#4ade80;color:#fff;font-weight:700;font-size:13px;min-width:40px;padding:5px 14px;border-radius:8px;">{{ $row['hadir'] }}</span>
                    </td>
                    <td style="padding:13px 16px; text-align:center;">
                        <span style="display:inline-block;background:#f87171;color:#fff;font-weight:700;font-size:13px;min-width:40px;padding:5px 14px;border-radius:8px;">{{ $row['tidak_hadir'] }}</span>
                    </td>
                    <td style="padding:13px 16px; text-align:center; color:#6366f1; font-weight:600;">{{ $row['total_jam'] }}</td>
                    <td style="padding:13px 16px; text-align:center; color:#6366f1; font-weight:600;">{{ $row['total_lembur'] }}</td>
                    <td style="padding:13px 16px; text-align:center; color:#4b5563; font-weight:500;">{{ $row['jam_lembur'] }}</td>
                    <td style="padding:13px 16px; text-align:center; color:#818cf8; font-weight:600;">{{ $row['izin'] }}</td>
                    <td style="padding:13px 16px; text-align:center; color:#818cf8; font-weight:600;">{{ $row['sakit'] }}</td>
                    <td style="padding:13px 16px; text-align:center; color:#818cf8; font-weight:600;">{{ $row['cuti'] }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    <div style="display:flex;flex-wrap:wrap;justify-content:space-between;align-items:center;margin-top:18px;gap:8px;font-size:13px;color:#9ca3af;">
        <span>Menampilkan 1 - 5 dari 10.000 riwayat lembur</span>
        <div style="display:flex;align-items:center;gap:4px;">
            <button style="width:32px;height:32px;display:flex;align-items:center;justify-content:center;border-radius:8px;border:none;background:transparent;cursor:pointer;color:#9ca3af;" onmouseover="this.style.background='#fff'" onmouseout="this.style.background='transparent'">
                <i class="fa-solid fa-chevron-left" style="font-size:11px;"></i>
            </button>
            <button style="width:32px;height:32px;display:flex;align-items:center;justify-content:center;border-radius:8px;border:none;background:#6366f1;color:#fff;font-weight:700;cursor:pointer;box-shadow:0 2px 6px rgba(99,102,241,0.3);">1</button>
            <button style="width:32px;height:32px;display:flex;align-items:center;justify-content:center;border-radius:8px;border:none;background:transparent;color:#374151;font-weight:500;cursor:pointer;" onmouseover="this.style.background='#fff'" onmouseout="this.style.background='transparent'">2</button>
            <button style="width:32px;height:32px;display:flex;align-items:center;justify-content:center;border-radius:8px;border:none;background:transparent;color:#374151;font-weight:500;cursor:pointer;" onmouseover="this.style.background='#fff'" onmouseout="this.style.background='transparent'">3</button>
            <span style="padding:0 4px;color:#9ca3af;">...</span>
            <button style="width:32px;height:32px;display:flex;align-items:center;justify-content:center;border-radius:8px;border:none;background:transparent;color:#374151;font-weight:500;cursor:pointer;" onmouseover="this.style.background='#fff'" onmouseout="this.style.background='transparent'">999</button>
            <button style="width:32px;height:32px;display:flex;align-items:center;justify-content:center;border-radius:8px;border:none;background:transparent;cursor:pointer;color:#9ca3af;" onmouseover="this.style.background='#fff'" onmouseout="this.style.background='transparent'">
                <i class="fa-solid fa-chevron-right" style="font-size:11px;"></i>
            </button>
        </div>
    </div>
</div>

{{-- ===== KALENDER SCRIPT — langsung di dalam @section('content') ===== --}}
<script>
(function () {
    'use strict';

    var BULAN       = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
    var BULAN_SHORT = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agt','Sep','Okt','Nov','Des'];

    /* ---- helpers ---- */
    function sameDay(a, b) {
        return a && b
            && a.getFullYear() === b.getFullYear()
            && a.getMonth()    === b.getMonth()
            && a.getDate()     === b.getDate();
    }
    function fmt(d) {
        return d.getDate() + ' ' + BULAN_SHORT[d.getMonth()] + ' ' + d.getFullYear();
    }

    /* ---- state ---- */
    var rw = { year: 2026, month: 9, sel: new Date(2026, 9, 31) };
    var rk = { year: 2026, month: 9, start: new Date(2026, 9, 1), end: new Date(2026, 9, 31), picking: 'start' };

    /* ---- render single ---- */
    function renderRw() {
        document.getElementById('cal-riwayat-month').textContent = BULAN[rw.month] + ' ' + rw.year;
        var first = new Date(rw.year, rw.month, 1).getDay();
        var total = new Date(rw.year, rw.month + 1, 0).getDate();
        var box   = document.getElementById('cal-riwayat-days');
        box.innerHTML = '';

        for (var i = 0; i < first; i++) box.innerHTML += '<span></span>';

        for (var d = 1; d <= total; d++) {
            var isSel = rw.sel && rw.sel.getFullYear() === rw.year && rw.sel.getMonth() === rw.month && rw.sel.getDate() === d;
            var cell  = document.createElement('div');
            cell.style.cssText = 'display:flex;justify-content:center;align-items:center;';
            var span  = document.createElement('span');
            span.textContent  = d;
            span.style.cssText = 'width:30px;height:30px;display:flex;align-items:center;justify-content:center;border-radius:50%;cursor:pointer;font-size:12px;'
                + (isSel ? 'background:#6366f1;color:#fff;font-weight:600;' : 'color:#374151;');
            span.setAttribute('data-d', d);
            span.addEventListener('mouseenter', function() { if (!this.style.background || this.style.background === 'rgba(0, 0, 0, 0)' || this.style.background === 'transparent') { this.style.background = '#e0e7ff'; this.style.color = '#4338ca'; } });
            span.addEventListener('mouseleave', function() { if (this.style.background === 'rgb(224, 231, 255)') { this.style.background = ''; this.style.color = '#374151'; } });
            span.addEventListener('click', function() {
                var day = parseInt(this.getAttribute('data-d'));
                rw.sel  = new Date(rw.year, rw.month, day);
                document.getElementById('cal-riwayat-label').textContent = fmt(rw.sel);
                document.getElementById('cal-riwayat').style.display = 'none';
                renderRw();
            });
            cell.appendChild(span);
            box.appendChild(cell);
        }
    }

    /* ---- render range ---- */
    function renderRk() {
        document.getElementById('cal-rekap-month').textContent = BULAN[rk.month] + ' ' + rk.year;
        document.getElementById('cal-rekap-hint').textContent  = rk.picking === 'start' ? 'Pilih tanggal awal' : 'Pilih tanggal akhir';
        var first = new Date(rk.year, rk.month, 1).getDay();
        var total = new Date(rk.year, rk.month + 1, 0).getDate();
        var box   = document.getElementById('cal-rekap-days');
        box.innerHTML = '';

        for (var i = 0; i < first; i++) box.innerHTML += '<span></span>';

        for (var d = 1; d <= total; d++) {
            var date    = new Date(rk.year, rk.month, d);
            var isStart = sameDay(date, rk.start);
            var isEnd   = sameDay(date, rk.end);
            var inRange = rk.start && rk.end && date > rk.start && date < rk.end;

            var cell = document.createElement('div');
            cell.style.cssText = 'display:flex;justify-content:center;align-items:center;';
            var span = document.createElement('span');
            span.textContent = d;
            var baseBg, baseColor;
            if (isStart || isEnd) { baseBg = '#6366f1'; baseColor = '#fff'; }
            else if (inRange)     { baseBg = '#e0e7ff'; baseColor = '#4338ca'; }
            else                  { baseBg = '';         baseColor = '#374151'; }
            span.style.cssText = 'width:30px;height:30px;display:flex;align-items:center;justify-content:center;border-radius:50%;cursor:pointer;font-size:12px;background:' + baseBg + ';color:' + baseColor + ';' + ((isStart || isEnd) ? 'font-weight:600;' : '');
            span.setAttribute('data-d', d);
            span.addEventListener('mouseenter', function() {
                if (!this.style.background || this.style.background === 'rgba(0, 0, 0, 0)' || this.style.background === 'transparent' || this.style.background === '') {
                    this.style.background = '#e0e7ff'; this.style.color = '#4338ca';
                }
            });
            span.addEventListener('mouseleave', function() {
                this.style.background = this._bg || ''; this.style.color = this._color || '#374151';
            });
            span._bg    = baseBg;
            span._color = baseColor;
            span.addEventListener('click', function() {
                var day  = parseInt(this.getAttribute('data-d'));
                var date = new Date(rk.year, rk.month, day);
                if (rk.picking === 'start') {
                    rk.start   = date;
                    rk.end     = null;
                    rk.picking = 'end';
                } else {
                    if (date < rk.start) { rk.end = rk.start; rk.start = date; }
                    else                 { rk.end = date; }
                    rk.picking = 'start';
                    document.getElementById('cal-rekap-label').textContent = fmt(rk.start) + ' - ' + fmt(rk.end);
                    document.getElementById('cal-rekap').style.display = 'none';
                }
                renderRk();
            });
            cell.appendChild(span);
            box.appendChild(cell);
        }
    }

    /* ---- toggle buka/tutup ---- */
    function bindToggle(btnId, dropId, renderFn) {
        var btn  = document.getElementById(btnId);
        var drop = document.getElementById(dropId);
        btn.addEventListener('click', function(e) {
            e.stopPropagation();
            var open = drop.style.display === 'block';
            // tutup semua dulu
            document.getElementById('cal-riwayat').style.display = 'none';
            document.getElementById('cal-rekap').style.display   = 'none';
            if (!open) { drop.style.display = 'block'; renderFn(); }
        });
        drop.addEventListener('click', function(e) { e.stopPropagation(); });
    }

    /* ---- navigasi bulan ---- */
    function bindNav(prevId, nextId, state, renderFn) {
        document.getElementById(prevId).addEventListener('click', function(e) {
            e.stopPropagation();
            state.month--; if (state.month < 0) { state.month = 11; state.year--; }
            renderFn();
        });
        document.getElementById(nextId).addEventListener('click', function(e) {
            e.stopPropagation();
            state.month++; if (state.month > 11) { state.month = 0; state.year++; }
            renderFn();
        });
    }

    /* ---- tutup saat klik luar ---- */
    document.addEventListener('click', function() {
        document.getElementById('cal-riwayat').style.display = 'none';
        document.getElementById('cal-rekap').style.display   = 'none';
    });

    /* ---- init ---- */
    bindToggle('btn-cal-riwayat', 'cal-riwayat', renderRw);
    bindToggle('btn-cal-rekap',   'cal-rekap',   renderRk);
    bindNav('prev-cal-riwayat', 'next-cal-riwayat', rw, renderRw);
    bindNav('prev-cal-rekap',   'next-cal-rekap',   rk, renderRk);

}());
</script>

@endsection