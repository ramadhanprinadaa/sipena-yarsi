<?php

namespace App\Support;

use App\Models\Cuti;
use App\Models\LaporanHasilLembur;
use App\Models\Lembur;
use App\Models\Pegawai;
use App\Models\SuratPerintahLembur;
use App\Models\User;
use App\Notifications\WorkflowNotification;
use Illuminate\Support\Facades\Mail;
use Throwable;

class WorkflowEmail
{
    public static function notifySplPublished(SuratPerintahLembur $spl, Pegawai $pegawai, ?Pegawai $publisher = null): void
    {
        $email = self::emailForPegawai($pegawai);

        if (!$email) {
            $email = null;
        }

        $pegawai->user?->notify(new WorkflowNotification(
            'spl_diterbitkan',
            'SPL Baru',
            'Anda terdaftar dalam Surat Perintah Lembur yang baru diterbitkan.',
            [
                'Nomor Surat' => $spl->nomor_surat,
                'Kegiatan' => $spl->nama_kegiatan,
                'Tanggal Lembur' => self::formatDate($spl->tanggal_lembur),
            ],
            route('lembur')
        ));

        if (!$email) {
            return;
        }

        self::send($email, 'SPL Baru - ' . $spl->nomor_surat, [
            'title' => 'Surat Perintah Lembur Baru',
            'greeting' => 'Halo ' . ($pegawai->nama ?? 'Pegawai') . ',',
            'intro' => 'Anda terdaftar dalam Surat Perintah Lembur yang baru diterbitkan. Silakan cek detail jadwal dan tugas berikut.',
            'rows' => [
                'Nomor Surat' => $spl->nomor_surat,
                'Unit Kerja' => $spl->unitKerja->name ?? $pegawai->unit_kerja->name ?? '-',
                'Nama Kegiatan' => $spl->nama_kegiatan,
                'Jenis Hari' => $spl->jenis_hari,
                'Tanggal Lembur' => self::formatDate($spl->tanggal_lembur),
                'Jam Lembur' => self::formatTime($spl->jam_mulai) . ' - ' . self::formatTime($spl->jam_selesai),
                'Diterbitkan Oleh' => $publisher->nama ?? '-',
            ],
            'note' => sprintf(
                'Silakan buka <a href="%s">SIPENA</a> untuk mengajukan lembur tersebut.',
                config('app.url')
            ),
        ]);
    }

    public static function notifySplUpdated(SuratPerintahLembur $spl, Pegawai $pegawai, ?Pegawai $publisher = null): void
    {
        $email = self::emailForPegawai($pegawai);

        if (!$email) {
            $email = null;
        }

        $pegawai->user?->notify(new WorkflowNotification(
            'spl_diperbarui',
            'SPL Diperbarui',
            'Surat Perintah Lembur yang Anda terima telah diperbarui.',
            [
                'Nomor Surat' => $spl->nomor_surat,
                'Kegiatan' => $spl->nama_kegiatan,
                'Tanggal Lembur' => self::formatDate($spl->tanggal_lembur),
            ],
            route('lembur')
        ));

        if (!$email) {
            return;
        }

        self::send($email, 'Pembaruan SPL - ' . $spl->nomor_surat, [
            'title' => 'Surat Perintah Lembur Diperbarui',
            'greeting' => 'Halo ' . ($pegawai->nama ?? 'Pegawai'),
            'intro' => 'Surat Perintah Lembur yang Anda terima telah diperbarui. Mohon cek kembali detail jadwal dan tugas berikut.',
            'rows' => [
                'Nomor Surat' => $spl->nomor_surat,
                'Unit Kerja' => $spl->unitKerja->name ?? $pegawai->unit_kerja->name ?? '-',
                'Nama Kegiatan' => $spl->nama_kegiatan,
                'Jenis Hari' => $spl->jenis_hari,
                'Tanggal Lembur' => self::formatDate($spl->tanggal_lembur),
                'Jam Lembur' => self::formatTime($spl->jam_mulai) . ' - ' . self::formatTime($spl->jam_selesai),
                'Diperbarui Oleh' => $publisher->nama ?? '-',
            ],
            'note' => sprintf(
                'Silakan buka <a href="%s">SIPENA</a> untuk melihat pembaruan SPL tersebut.',
                config('app.url')
            ),
        ]);
    }

    public static function notifyLaporanSubmitted(Lembur $lembur, LaporanHasilLembur $laporan): void
    {
        $lembur->loadMissing(['pegawai.unit_kerja.pimpinan.user', 'pegawai.user.role', 'suratPerintahLembur']);
        $pegawai = $lembur->pegawai;
        $approver = self::approverForPegawai($pegawai);
        $email = self::emailForPegawai($approver);

        if (!$email) {
            $email = null;
        }

        $approver->user?->notify(new WorkflowNotification(
            'laporan_lembur',
            'Laporan Lembur Menunggu Verifikasi',
            'Ada laporan hasil lembur yang membutuhkan verifikasi Anda.',
            [
                'Nama Pegawai' => $pegawai->nama ?? '-',
                'Tanggal Lembur' => self::formatDate($lembur->tanggal_lembur),
            ],
            route('manajemen-lembur')
        ));

        if (!$email) {
            return;
        }

        self::send($email, 'Laporan Lembur Baru - ' . ($pegawai->nama ?? 'Pegawai'), [
            'title' => 'Laporan Lembur Menunggu Persetujuan',
            'greeting' => 'Halo ' . ($approver->nama ?? 'Bapak/Ibu') . ',',
            'intro' => 'Pegawai di bawah unit kerja Anda telah mengirimkan laporan hasil lembur.',
            'rows' => [
                'Nama Pegawai' => $pegawai->nama ?? '-',
                'NIP' => $pegawai->nip ?? '-',
                'Unit Kerja' => $pegawai->unit_kerja->name ?? '-',
                'Tanggal Lembur' => self::formatDate($lembur->tanggal_lembur),
                'Jam Aktual' => self::formatTime($laporan->jam_mulai) . ' - ' . self::formatTime($laporan->jam_selesai),
                'Hasil Pekerjaan' => $laporan->hasil_pekerjaan,
            ],
            'note' => sprintf(
                'Silakan buka <a href="%s">SIPENA</a> untuk melakukan verifikasi laporan lembur tersebut.',
                config('app.url')
            ),
        ]);
    }

    public static function notifyCutiSubmitted(Cuti $cuti): void
    {
        $cuti->loadMissing(['pegawai.unit_kerja.pimpinan.user', 'pegawai.user.role', 'jenisCuti']);
        $pegawai = $cuti->pegawai;
        $approver = self::approverForStatus($cuti->status, $pegawai);
        $email = self::emailForPegawai($approver);

        if (!$email) {
            $email = null;
        }

        $approver->user?->notify(new WorkflowNotification(
            'cuti',
            'Cuti Menunggu Verifikasi',
            'Ada pengajuan cuti yang membutuhkan verifikasi Anda.',
            [
                'Nama Pegawai' => $pegawai->nama ?? '-',
                'Jenis Cuti' => $cuti->jenisCuti->nama ?? '-',
                'Tanggal Mulai' => self::formatDate($cuti->tanggal_mulai),
            ],
            route('manajemen-cuti')
        ));

        if (!$email) {
            return;
        }

        self::send($email, 'Pengajuan Cuti Baru - ' . ($pegawai->nama ?? 'Pegawai'), [
            'title' => 'Pengajuan Cuti Menunggu Persetujuan',
            'greeting' => 'Halo ' . ($approver->nama ?? 'Bapak/Ibu') . ',',
            'intro' => 'Ada pengajuan cuti baru yang membutuhkan tindak lanjut dari Anda.',
            'rows' => [
                'Nama Pegawai' => $pegawai->nama ?? '-',
                'NIP' => $pegawai->nip ?? '-',
                'Unit Kerja' => $pegawai->unit_kerja->name ?? '-',
                'Jenis Cuti' => $cuti->jenisCuti->nama ?? '-',
                'Tanggal Mulai' => self::formatDate($cuti->tanggal_mulai),
                'Tanggal Selesai' => self::formatDate($cuti->tanggal_selesai),
                'Durasi' => $cuti->jenisCuti?->dihitung_per_jam ? (($cuti->jumlah_jam ?? 0) . ' jam') : (($cuti->jumlah_hari_cuti ?? 0) . ' hari'),
                'Alasan' => $cuti->keterangan,
            ],
            'note' => sprintf(
                'Silakan buka <a href="%s">SIPENA</a> untuk meninjau dan memproses pengajuan cuti tersebut.',
                config('app.url')
            ),
        ]);
    }

    private static function approverForPegawai(?Pegawai $pegawai): ?Pegawai
    {
        if (!$pegawai) {
            return null;
        }

        $role = $pegawai->user?->role?->name;
        $unitSdmId = (int) $pegawai->unit_kerja?->unit_sdm_id;

        if ($role === 'Pimpinan') {
            return $unitSdmId === 1
                ? self::pegawaiByRole('SDM Yayasan')
                : self::pegawaiByRole('Rektor');
        }

        if ($role === 'Rektor') {
            return self::pegawaiByRole('SDM Universitas');
        }

        if ($role === 'SDM Universitas') {
            return self::pegawaiByRole('SDM Yayasan');
        }

        $pimpinan = $pegawai->unit_kerja?->pimpinan;

        if ($pimpinan && $pimpinan->id !== $pegawai->id) {
            return $pimpinan;
        }

        return User::whereHas('role', fn ($query) => $query->where('name', 'Pimpinan'))
            ->whereHas('pegawai', fn ($query) => $query->where('unit_kerja_id', $pegawai->unit_kerja_id))
            ->with('pegawai')
            ->first()
            ?->pegawai;
    }

    private static function approverForStatus(string $status, ?Pegawai $pegawai): ?Pegawai
    {
        return match ($status) {
            'pending_atasan', 'Menunggu Verifikasi Atasan' => self::approverForPegawai($pegawai),
            'pending_rektor', 'Menunggu Verifikasi Rektor' => self::pegawaiByRole('Rektor'),
            'pending_sdm_universitas', 'Menunggu Verifikasi SDM Universitas' => self::pegawaiByRole('SDM Universitas'),
            'pending_sdm_yayasan', 'Menunggu Verifikasi SDM Yayasan' => self::pegawaiByRole('SDM Yayasan'),
            default => self::approverForPegawai($pegawai),
        };
    }

    private static function pegawaiByRole(string $role): ?Pegawai
    {
        return User::whereHas('role', fn ($query) => $query->where('name', $role))
            ->with('pegawai')
            ->first()
            ?->pegawai;
    }

    private static function emailForPegawai(?Pegawai $pegawai): ?string
    {
        if (!$pegawai) {
            return null;
        }

        return $pegawai->email_yarsi ?: $pegawai->user?->email;
    }

    private static function send(string $to, string $subject, array $payload): void
    {
        try {
            Mail::send(
                'emails.default',
                $payload,
                function ($message) use ($to, $subject) {
                    $message->to($to)
                            ->subject($subject);
                }
            );
        } catch (Throwable $exception) {
            report($exception);
        }
    }

    // private static function html(array $payload): string
    // {
    //     $rows = collect($payload['rows'] ?? [])
    //         ->map(function ($value, $label) {
    //             return '<tr>
    //                 <td style="padding:10px 12px;border-bottom:1px solid #eef2ff;color:#64748b;font-size:13px;width:38%;">' . e($label) . '</td>
    //                 <td style="padding:10px 12px;border-bottom:1px solid #eef2ff;color:#0f172a;font-size:13px;font-weight:600;">' . nl2br(e((string) $value)) . '</td>
    //             </tr>';
    //         })
    //         ->implode('');

    //     return '<!doctype html>
    //         <html>
    //         <body style="margin:0;padding:0;background:#f6f8fb;font-family:Arial,Helvetica,sans-serif;color:#0f172a;">
    //             <div style="max-width:640px;margin:0 auto;padding:28px 16px;">
    //                 <div style="background:linear-gradient(135deg,#2563eb,#7c3aed);border-radius:18px 18px 0 0;padding:24px;color:#fff;">
    //                     <div style="font-size:12px;font-weight:700;letter-spacing:.12em;text-transform:uppercase;opacity:.82;">SIPENA YARSI</div>
    //                     <h1 style="margin:8px 0 0;font-size:24px;line-height:1.25;">' . e($payload['title'] ?? 'Notifikasi SIPENA') . '</h1>
    //                 </div>
    //                 <div style="background:#ffffff;border:1px solid #e5e7eb;border-top:0;border-radius:0 0 18px 18px;padding:24px;box-shadow:0 12px 32px rgba(15,23,42,.08);">
    //                     <p style="margin:0 0 10px;font-size:15px;font-weight:700;">' . e($payload['greeting'] ?? 'Halo,') . '</p>
    //                     <p style="margin:0 0 20px;color:#475569;font-size:14px;line-height:1.7;">' . e($payload['intro'] ?? '') . '</p>
    //                     <table style="width:100%;border-collapse:collapse;background:#f8fafc;border:1px solid #eef2ff;border-radius:12px;overflow:hidden;">' . $rows . '</table>
    //                     <p style="margin:20px 0 0;color:#475569;font-size:13px;line-height:1.7;">' . e($payload['note'] ?? '') . '</p>
    //                     <div style="margin-top:22px;padding-top:16px;border-top:1px solid #e5e7eb;color:#94a3b8;font-size:12px;">
    //                         Email ini dikirim otomatis oleh SIPENA. Mohon tidak membalas email ini.
    //                     </div>
    //                 </div>
    //             </div>
    //         </body>
    //         </html>';
    // }

    private static function formatDate($date): string
    {
        return $date ? \Carbon\Carbon::parse($date)->translatedFormat('d F Y') : '-';
    }

    private static function formatTime($time): string
    {
        return $time ? substr((string) $time, 0, 5) : '-';
    }
}
