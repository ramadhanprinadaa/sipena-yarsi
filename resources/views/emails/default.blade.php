<!doctype html>
<html>
    <body style="margin:0;padding:0;background:#f6f8fb;font-family:Arial,Helvetica,sans-serif;color:#0f172a;">
        <div style="max-width:640px;margin:0 auto;padding:28px 16px;">
            <div style="background:linear-gradient(135deg,#2563eb,#7c3aed);border-radius:18px 18px 0 0;padding:24px;color:#fff;">
                <div style="font-size:12px;font-weight:700;letter-spacing:.12em;text-transform:uppercase;opacity:.82;">
                    SIPENA YARSI
                </div>

                <h1 style="margin:8px 0 0;font-size:24px;line-height:1.25;">
                    {{ $title ?? 'Notifikasi SIPENA' }}
                </h1>
            </div>

            <div style="background:#ffffff;border:1px solid #e5e7eb;border-top:0;border-radius:0 0 18px 18px;padding:24px;box-shadow:0 12px 32px rgba(15,23,42,.08);">

                <p style="margin:0 0 10px;font-size:15px;font-weight:700;">
                    {{ $greeting ?? 'Halo,' }}
                </p>

                <p style="margin:0 0 20px;color:#475569;font-size:14px;line-height:1.7;">
                    {{ $intro ?? '' }}
                </p>

                <table style="width:100%;border-collapse:collapse;background:#f8fafc;border:1px solid #eef2ff;border-radius:12px;overflow:hidden;">
                    @foreach($rows ?? [] as $label => $value)
                        <tr>
                            <td style="padding:10px 12px;border-bottom:1px solid #eef2ff;color:#64748b;font-size:13px;width:38%;">
                                {{ $label }}
                            </td>

                            <td style="padding:10px 12px;border-bottom:1px solid #eef2ff;color:#0f172a;font-size:13px;font-weight:600;">
                                {{ $value }}
                            </td>
                        </tr>
                    @endforeach
                </table>

                <p style="margin:20px 0 0;color:#475569;font-size:13px;line-height:1.7;">
                    {!! $note ?? '' !!}
                </p>

                <div style="margin-top:22px;padding-top:16px;border-top:1px solid #e5e7eb;color:#94a3b8;font-size:12px;">
                    Email ini dikirim otomatis oleh SIPENA. Mohon tidak membalas email ini.
                </div>

            </div>
        </div>
    </body>
</html>