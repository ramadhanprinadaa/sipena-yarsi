<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>SIPENA | Sistem Informasi Pegawai dan Administrasi YARSI</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Lexend:wght@100..900&family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-slate-50 text-slate-900 font-lexend antialiased">
    <header class="absolute left-0 right-0 top-0 z-30">
        <nav class="mx-auto flex max-w-7xl items-center justify-between px-4 py-4 sm:px-6 lg:px-8">
            <a href="{{ route('home') }}" class="flex min-w-0 items-center gap-3 text-white">
                <img src="{{ asset('favicon.ico') }}" alt="Logo SIPENA" class="h-11 w-11 shrink-0 rounded-lg bg-white/90 p-1 shadow-sm">
                <div class="min-w-0">
                    <p class="text-lg font-bold leading-tight">SIPENA</p>
                    <p class="hidden text-xs font-medium text-white/80 sm:block">Universitas YARSI</p>
                </div>
            </a>

            <div class="hidden items-center gap-8 text-sm font-medium text-white/85 md:flex">
                <a href="#alur-kerja" class="transition hover:text-white">Alur Kerja</a>
                <a href="#layanan" class="transition hover:text-white">Layanan</a>
                <a href="#akses" class="transition hover:text-white">Akses</a>
            </div>

            <a href="{{ route('login') }}" class="inline-flex h-11 items-center gap-2 rounded-lg bg-white px-4 text-sm font-semibold text-indigo-700 shadow-sm transition hover:bg-indigo-50">
                <i class="fa-solid fa-right-to-bracket"></i>
                <span>Masuk</span>
            </a>
        </nav>
    </header>

    <main>
        <section class="relative isolate min-h-[78svh] overflow-hidden bg-slate-950">
            <img src="{{ asset('images/univ-yarsi.png') }}" alt="Gedung Universitas YARSI" class="absolute inset-0 h-full w-full object-cover">
            <div class="absolute inset-0 bg-slate-950/60"></div>
            <div class="absolute inset-x-0 bottom-0 h-40 bg-gradient-to-t from-slate-950/75 to-transparent"></div>

            <div class="relative mx-auto flex min-h-[78svh] max-w-7xl items-end px-4 pb-12 pt-28 sm:px-6 lg:px-8">
                <div class="max-w-3xl text-white">
                    <div class="mb-5 inline-flex items-center gap-2 rounded-lg border border-white/20 bg-white/10 px-3 py-2 text-xs font-semibold uppercase tracking-normal text-white/90 backdrop-blur-sm">
                        <i class="fa-solid fa-building-user text-pink-200"></i>
                        Portal pegawai dan administrasi YARSI
                    </div>

                    <h1 class="max-w-2xl text-4xl font-extrabold leading-tight sm:text-5xl lg:text-6xl">
                        SIPENA
                    </h1>
                    <p class="mt-4 max-w-2xl text-base leading-8 text-white/85 sm:text-lg">
                        Sistem Informasi Pegawai dan Administrasi yang menyatukan data kepegawaian,
                        presensi, cuti, lembur, surat, dan sumber daya kerja dalam satu alur layanan
                        internal Universitas YARSI.
                    </p>

                    <div class="mt-8 flex flex-col gap-3 sm:flex-row">
                        <a href="{{ route('login') }}" class="inline-flex h-12 items-center justify-center gap-2 rounded-lg bg-indigo-500 px-5 text-sm font-semibold text-white shadow-lg shadow-indigo-950/20 transition hover:bg-indigo-400">
                            <i class="fa-solid fa-right-to-bracket"></i>
                            <span>Masuk ke SIPENA</span>
                        </a>
                        <a href="#alur-kerja" class="inline-flex h-12 items-center justify-center gap-2 rounded-lg border border-white/25 bg-white/10 px-5 text-sm font-semibold text-white backdrop-blur-sm transition hover:bg-white/15">
                            <i class="fa-solid fa-route"></i>
                            <span>Lihat Alur Kerja</span>
                        </a>
                    </div>
                </div>
            </div>
        </section>

        <section aria-label="Ringkasan SIPENA" class="border-b border-slate-200 bg-white">
            <div class="mx-auto grid max-w-7xl grid-cols-1 gap-px bg-slate-200 px-4 sm:grid-cols-3 sm:px-6 lg:px-8">
                <div class="bg-white py-6 sm:pr-8">
                    <p class="text-sm font-semibold text-slate-500">Fokus layanan</p>
                    <p class="mt-2 text-2xl font-bold text-slate-900">Pegawai & SDM</p>
                </div>
                <div class="bg-white py-6 sm:px-8">
                    <p class="text-sm font-semibold text-slate-500">Ruang kerja</p>
                    <p class="mt-2 text-2xl font-bold text-slate-900">Beranda & Manajemen</p>
                </div>
                <div class="bg-white py-6 sm:pl-8">
                    <p class="text-sm font-semibold text-slate-500">Akses</p>
                    <p class="mt-2 text-2xl font-bold text-slate-900">Akun internal YARSI</p>
                </div>
            </div>
        </section>

        <section id="alur-kerja" class="bg-slate-50 py-16 sm:py-20">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="max-w-3xl">
                    <p class="text-sm font-bold uppercase tracking-normal text-indigo-600">Alur kerja SIPENA</p>
                    <h2 class="mt-3 text-3xl font-bold leading-tight text-slate-950 sm:text-4xl">
                        Dari data pegawai sampai persetujuan, semuanya bergerak dalam satu portal.
                    </h2>
                </div>

                <div class="mt-10 grid gap-4 md:grid-cols-2 lg:grid-cols-4">
                    <article class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                        <div class="flex h-11 w-11 items-center justify-center rounded-lg bg-indigo-50 text-indigo-600">
                            <i class="fa-solid fa-address-card"></i>
                        </div>
                        <h3 class="mt-5 text-lg font-bold text-slate-950">Kepegawaian</h3>
                        <p class="mt-2 text-sm leading-6 text-slate-600">
                            Profil, biodata, keluarga, rekening, pendidikan, dan riwayat pegawai tersusun untuk kebutuhan administrasi.
                        </p>
                    </article>

                    <article class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                        <div class="flex h-11 w-11 items-center justify-center rounded-lg bg-teal-50 text-teal-600">
                            <i class="fa-regular fa-calendar-check"></i>
                        </div>
                        <h3 class="mt-5 text-lg font-bold text-slate-950">Presensi</h3>
                        <p class="mt-2 text-sm leading-6 text-slate-600">
                            Ringkasan kehadiran, riwayat presensi, impor data, dan rekapitulasi dapat dipantau dari modul yang sama.
                        </p>
                    </article>

                    <article class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                        <div class="flex h-11 w-11 items-center justify-center rounded-lg bg-rose-50 text-rose-600">
                            <i class="fa-solid fa-plane-departure"></i>
                        </div>
                        <h3 class="mt-5 text-lg font-bold text-slate-950">Cuti & Lembur</h3>
                        <p class="mt-2 text-sm leading-6 text-slate-600">
                            Pengajuan cuti, saldo, lembur, surat perintah, dan laporan hasil kerja mengikuti jalur persetujuan.
                        </p>
                    </article>

                    <article class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                        <div class="flex h-11 w-11 items-center justify-center rounded-lg bg-amber-50 text-amber-600">
                            <i class="fa-solid fa-envelope-open-text"></i>
                        </div>
                        <h3 class="mt-5 text-lg font-bold text-slate-950">Surat & Sumber Daya</h3>
                        <p class="mt-2 text-sm leading-6 text-slate-600">
                            Kebutuhan dokumen, arsip, dan sumber daya kerja tersedia sebagai pendamping aktivitas administratif.
                        </p>
                    </article>
                </div>
            </div>
        </section>

        <section id="layanan" class="bg-white py-16 sm:py-20">
            <div class="mx-auto grid max-w-7xl gap-10 px-4 sm:px-6 lg:grid-cols-[0.9fr_1.1fr] lg:px-8">
                <div>
                    <p class="text-sm font-bold uppercase tracking-normal text-teal-600">Ruang layanan</p>
                    <h2 class="mt-3 text-3xl font-bold leading-tight text-slate-950 sm:text-4xl">
                        Tampilan kerja disusun mengikuti peran pengguna.
                    </h2>
                    <p class="mt-4 text-sm leading-7 text-slate-600 sm:text-base">
                        Pegawai melihat kebutuhan mandiri seperti presensi, cuti, profil, dan sumber daya.
                        Tim SDM dan pimpinan mendapatkan ruang manajemen untuk validasi data, rekapitulasi,
                        pengajuan, serta konfigurasi unit kerja dan kalender.
                    </p>
                </div>

                <div class="grid gap-4 sm:grid-cols-2">
                    <div class="rounded-lg border border-slate-200 bg-slate-50 p-5">
                        <div class="flex items-center gap-3">
                            <i class="fa-solid fa-house text-indigo-600"></i>
                            <h3 class="font-bold text-slate-950">Beranda Pegawai</h3>
                        </div>
                        <p class="mt-3 text-sm leading-6 text-slate-600">
                            Pusat aktivitas harian untuk profil, presensi, cuti, lembur, dan surat menyurat.
                        </p>
                    </div>

                    <div class="rounded-lg border border-slate-200 bg-slate-50 p-5">
                        <div class="flex items-center gap-3">
                            <i class="fa-solid fa-users-gear text-rose-600"></i>
                            <h3 class="font-bold text-slate-950">Manajemen SDM</h3>
                        </div>
                        <p class="mt-3 text-sm leading-6 text-slate-600">
                            Kelola pengguna, pegawai, presensi, cuti, dan lembur sesuai hak akses.
                        </p>
                    </div>

                    <div class="rounded-lg border border-slate-200 bg-slate-50 p-5">
                        <div class="flex items-center gap-3">
                            <i class="fa-solid fa-gear text-slate-700"></i>
                            <h3 class="font-bold text-slate-950">Konfigurasi</h3>
                        </div>
                        <p class="mt-3 text-sm leading-6 text-slate-600">
                            Unit kerja, unit SDM, dan kalender menjadi dasar data operasional.
                        </p>
                    </div>

                    <div class="rounded-lg border border-slate-200 bg-slate-50 p-5">
                        <div class="flex items-center gap-3">
                            <i class="fa-solid fa-book-open text-amber-600"></i>
                            <h3 class="font-bold text-slate-950">Sumber Daya</h3>
                        </div>
                        <p class="mt-3 text-sm leading-6 text-slate-600">
                            Referensi dan dokumen pendukung tersedia untuk membantu pekerjaan internal.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <section id="akses" class="bg-slate-950 py-14 text-white">
            <div class="mx-auto flex max-w-7xl flex-col gap-6 px-4 sm:px-6 md:flex-row md:items-center md:justify-between lg:px-8">
                <div class="max-w-2xl">
                    <p class="text-sm font-semibold uppercase tracking-normal text-pink-200">Siap melanjutkan pekerjaan?</p>
                    <h2 class="mt-3 text-3xl font-bold leading-tight">Masuk dengan akun internal untuk membuka ruang kerja SIPENA.</h2>
                </div>
                <a href="{{ route('login') }}" class="inline-flex h-12 w-full items-center justify-center gap-2 rounded-lg bg-white px-5 text-sm font-semibold text-indigo-700 shadow-sm transition hover:bg-indigo-50 sm:w-auto">
                    <i class="fa-solid fa-right-to-bracket"></i>
                    <span>Masuk Sekarang</span>
                </a>
            </div>
        </section>
    </main>

    <footer class="bg-white py-6">
        <div class="mx-auto flex max-w-7xl flex-col gap-3 px-4 text-sm text-slate-500 sm:px-6 md:flex-row md:items-center md:justify-between lg:px-8">
            <p>&copy; {{ date('Y') }} SIPENA Universitas YARSI.</p>
            <p>Sistem Informasi Pegawai dan Administrasi.</p>
        </div>
    </footer>
</body>

</html>
