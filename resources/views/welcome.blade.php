<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIPENA | Sistem Informasi Pegawai dan Administrasi YARSI</title>
    <link rel="icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&family=Lexend:wght@100..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body {
            font-family: "Lexend", ui-sans-serif, system-ui, sans-serif;
        }

        :root {
            --nav-height: clamp(72px, 9vh, 92px);
        }

        html {
            scroll-padding-top: 0;
            scroll-snap-type: y proximity;
        }

        .nav-shell {
            min-height: var(--nav-height);
            padding-inline: clamp(1rem, 4vw, 4rem);
        }

        .nav-menu-shell {
            padding-inline: clamp(1rem, 4vw, 4rem);
        }

        .section-anchor {
            min-height: 100vh;
            min-height: 100svh;
            scroll-margin-top: 0;
            scroll-snap-align: start;
            padding-inline: clamp(1rem, 5vw, 5rem);
            padding-top: calc(var(--nav-height) + clamp(1rem, 4vh, 3rem));
            padding-bottom: clamp(2rem, 6vh, 5rem);
        }

        .section-container {
            width: min(100%, 92vw);
            max-width: 80rem;
            margin-inline: auto;
        }

        @media (min-width: 1536px) {
            .section-container {
                max-width: min(88rem, 90vw);
            }
        }

        @media (max-height: 760px) {
            .section-anchor {
                padding-top: calc(var(--nav-height) + 1rem);
                padding-bottom: 2rem;
            }
        }

        ::selection {
            background-color: #62A6FF;
            color: #ffffff;
        }

        @keyframes fade-up {
            from {
                opacity: 0;
                transform: translateY(18px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-fade-up {
            animation: fade-up 0.7s ease-out both;
        }

        .feature-card {
            transition: transform 0.25s ease, box-shadow 0.25s ease, border-color 0.25s ease;
        }

        .feature-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 18px 34px -22px rgba(43, 118, 255, 0.5);
            border-color: rgba(43, 118, 255, 0.45);
        }

        .watermark-text {
            -webkit-text-stroke: 1px rgba(255, 255, 255, 0.1);
            color: transparent;
        }
    </style>
</head>
<body class="min-h-screen bg-gradient-to-br from-[#62A6FF] via-[#E4B4FF] to-[#D8E9FF] text-gray-800 antialiased">

    {{-- Navigation --}}
    <nav class="fixed left-0 right-0 top-0 z-50 w-full">
        <div class="border-b border-white/70 bg-white/80 shadow-md backdrop-blur-sm">
            <div class="nav-shell flex items-center justify-between">
                <a href="#beranda" class="flex min-w-0 items-center gap-3">
                    <img src="{{ asset('favicon.ico') }}" alt="Logo SIPENA" class="h-11 w-11 shrink-0 shadow-sm">
                    <div class="min-w-0">
                        <span class="block text-base font-bold leading-tight text-gray-700 sm:text-xl">SIPENA</span>
                    </div>
                </a>

                <ul class="hidden items-center gap-2 text-sm font-medium text-gray-600 lg:flex uppercase">
                    <li><a href="#beranda" class="rounded-xl px-4 py-2 transition hover:text-[#2B76FF]">Beranda</a></li>
                    <li><a href="#tentang" class="rounded-xl px-4 py-2 transition hover:text-[#2B76FF]">Tentang</a></li>
                    <li><a href="#fitur" class="rounded-xl px-4 py-2 transition hover:text-[#2B76FF]">Fitur</a></li>
                    <li><a href="#tim" class="rounded-xl px-4 py-2 transition hover:text-[#2B76FF]">Tim Pengembang</a></li>
                </ul>

                <div class="hidden lg:block">
                    <a href="{{ route('login') }}" class="inline-flex items-center gap-2 rounded-md bg-gradient-to-r from-[#2B76FF] to-[#A8C7FF] px-5 py-2.5 text-sm font-semibold text-white shadow-md transition hover:shadow-lg uppercase">
                        <i class="fa-solid fa-right-to-bracket"></i>
                        Login
                    </a>
                </div>

                <button data-collapse-toggle="navbar-menu" type="button"
                    class="inline-flex h-10 w-10 items-center justify-center rounded-xl text-gray-700 transition hover:bg-white/80 lg:hidden"
                    aria-controls="navbar-menu" aria-expanded="false">
                    <span class="sr-only">Buka menu utama</span>
                    <i class="fa-solid fa-bars text-lg"></i>
                </button>
            </div>

            <div class="nav-menu-shell hidden border-t border-white/70 pb-4 lg:hidden" id="navbar-menu">
                <ul class="flex flex-col gap-1 pt-3 text-sm font-medium text-gray-600">
                    <li><a href="#beranda" class="block rounded-xl px-3 py-2.5 transition hover:bg-white/80 hover:text-[#2B76FF]">Beranda</a></li>
                    <li><a href="#tentang" class="block rounded-xl px-3 py-2.5 transition hover:bg-white/80 hover:text-[#2B76FF]">Tentang</a></li>
                    <li><a href="#fitur" class="block rounded-xl px-3 py-2.5 transition hover:bg-white/80 hover:text-[#2B76FF]">Fitur</a></li>
                    <li><a href="#tim" class="block rounded-xl px-3 py-2.5 transition hover:bg-white/80 hover:text-[#2B76FF]">Tim Pengembang</a></li>
                    <li>
                        <a href="{{ route('login') }}" class="mt-2 flex items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-[#2B76FF] to-[#A8C7FF] px-4 py-3 font-semibold text-white shadow-md transition hover:shadow-lg">
                            <i class="fa-solid fa-right-to-bracket"></i>
                            Login
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <main>
        {{-- beranda --}}
        <section id="beranda" class="section-anchor flex items-center">
            <div class="section-container grid items-center gap-12 lg:grid-cols-[1.02fr_0.98fr]">
                <div class="animate-fade-up">
                    <span class="inline-flex items-center gap-2 rounded-xl border border-white/70 bg-white/70 px-4 py-2 text-xs font-semibold uppercase tracking-wide text-[#2B76FF] shadow-sm backdrop-blur-sm">
                        <i class="fa-solid fa-building-columns"></i>
                        Universitas YARSI
                    </span>
                    <h1 class="mt-4 text-5xl font-extrabold leading-tight tracking-normal text-gray-800 sm:text-6xl lg:text-7xl">
                        SIPENA
                    </h1>
                    <p class="mt-2 max-w-2xl text-xl font-semibold leading-snug text-gray-800 sm:text-2xl">
                        Sistem Informasi Pegawai dan Administrasi YARSI.
                    </p>
                    <p class="mt-4 max-w-xl text-base leading-relaxed text-gray-700 sm:text-lg">
                        Kelola presensi, cuti, lembur, data pegawai, dan layanan administrasi SDM dalam satu dashboard yang rapi, cepat, dan mudah digunakan.
                    </p>

                    <div class="mt-8 flex flex-wrap gap-3">
                        <a href="{{ route('login') }}" class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-[#2B76FF] to-[#A8C7FF] px-6 py-3.5 font-semibold text-white shadow-lg shadow-blue-500/20 transition hover:shadow-xl">
                            Masuk ke SIPENA
                            <i class="fa-solid fa-arrow-right text-sm"></i>
                        </a>
                        <a href="#fitur" class="inline-flex items-center gap-2 rounded-xl border border-white/80 bg-white/70 px-6 py-3.5 font-semibold text-gray-700 shadow-sm backdrop-blur-sm transition hover:bg-white">
                            Lihat Fitur
                        </a>
                    </div>

                    <div class="mt-10 grid max-w-xl grid-cols-1 gap-3 sm:grid-cols-3">
                        <div class="rounded-xl border border-white/70 bg-white/60 p-4 shadow-sm backdrop-blur-sm">
                            <p class="text-3xl font-bold text-[#2B76FF]">13</p>
                            <p class="mt-1 text-xs font-medium text-gray-600">Modul layanan</p>
                        </div>
                        <div class="rounded-xl border border-white/70 bg-white/60 p-4 shadow-sm backdrop-blur-sm">
                            <p class="text-3xl font-bold text-[#2B76FF]">1</p>
                            <p class="mt-1 text-xs font-medium text-gray-600">Dashboard terpadu</p>
                        </div>
                        <div class="rounded-xl border border-white/70 bg-white/60 p-4 shadow-sm backdrop-blur-sm">
                            <p class="text-3xl font-bold text-[#2B76FF]">24/7</p>
                            <p class="mt-1 text-xs font-medium text-gray-600">Akses digital</p>
                        </div>
                    </div>
                </div>

                <div class="animate-fade-up lg:pl-4" style="animation-delay: 0.12s">
                    <div class="overflow-hidden rounded-xl border border-white/70 bg-white/55 p-3 shadow-2xl shadow-blue-900/15 backdrop-blur-sm">
                        <img src="{{ asset('images/univ-yarsi.png') }}" alt="Universitas YARSI" class="aspect-[4/3] w-full rounded-lg object-cover">
                    </div>
                </div>
            </div>
        </section>

        {{-- tentang --}}
        <section id="tentang" class="section-anchor flex items-center bg-white/80 backdrop-blur-sm">
            <div class="section-container grid items-center gap-12 lg:grid-cols-12">
                <div class="lg:col-span-5">
                    <span class="text-xs font-semibold uppercase tracking-widest text-[#2B76FF]">Tentang Sistem</span>
                    <h2 class="mt-3 text-3xl font-bold leading-tight text-gray-800 sm:text-4xl">
                        Pengelolaan administrasi pegawai yang lebih tertata.
                    </h2>
                    <p class="mt-6 leading-relaxed text-gray-600">
                        SIPENA adalah platform digital Universitas YARSI untuk mengelola proses kepegawaian, mulai dari data induk pegawai, presensi, cuti, lembur, hingga kebutuhan administrasi lainnya.
                    </p>
                    <p class="mt-4 leading-relaxed text-gray-600">
                        Tampilan dan alur kerjanya dibuat konsisten dengan dashboard SIPENA agar pengguna dapat berpindah dari landing page ke aplikasi tanpa terasa masuk ke produk yang berbeda.
                    </p>
                </div>

                <div class="grid gap-4 sm:grid-cols-2 lg:col-span-7">
                    <div class="rounded-xl border border-blue-100 bg-white/85 p-6 shadow-sm">
                        <i class="fa-solid fa-layer-group text-xl text-[#2B76FF]"></i>
                        <h3 class="mt-4 font-semibold text-gray-800">Terintegrasi</h3>
                        <p class="mt-2 text-sm leading-relaxed text-gray-600">Semua proses administrasi berada dalam satu sistem yang sama.</p>
                    </div>
                    <div class="rounded-xl border border-blue-100 bg-white/85 p-6 shadow-sm">
                        <i class="fa-solid fa-clock-rotate-left text-xl text-[#2B76FF]"></i>
                        <h3 class="mt-4 font-semibold text-gray-800">Real-time</h3>
                        <p class="mt-2 text-sm leading-relaxed text-gray-600">Aktivitas pegawai dapat dipantau dengan data yang selalu diperbarui.</p>
                    </div>
                    <div class="rounded-xl border border-blue-100 bg-white/85 p-6 shadow-sm">
                        <i class="fa-solid fa-lock text-xl text-[#2B76FF]"></i>
                        <h3 class="mt-4 font-semibold text-gray-800">Tertib</h3>
                        <p class="mt-2 text-sm leading-relaxed text-gray-600">Riwayat dan dokumen kepegawaian tersimpan lebih rapi dan mudah ditelusuri.</p>
                    </div>
                    <div class="rounded-xl border border-blue-100 bg-white/85 p-6 shadow-sm">
                        <i class="fa-solid fa-people-arrows text-xl text-[#2B76FF]"></i>
                        <h3 class="mt-4 font-semibold text-gray-800">Mudah Diakses</h3>
                        <p class="mt-2 text-sm leading-relaxed text-gray-600">Pegawai, atasan, dan unit SDM memakai portal yang konsisten.</p>
                    </div>
                </div>
            </div>
        </section>

        {{-- Fitur --}}
        <section id="fitur" class="section-anchor flex items-center bg-gradient-to-br from-white via-[#F4FAFF] to-[#FFF7FD]">
            <div class="section-container">
                <div class="mx-auto mb-14 max-w-2xl text-center">
                    <span class="text-xs font-semibold uppercase tracking-widest text-[#2B76FF]">Layanan SIPENA</span>
                    <h2 class="mt-3 text-3xl font-bold text-gray-800 sm:text-4xl">
                        13 modul dalam satu dashboard.
                    </h2>
                    <p class="mt-4 text-gray-600">
                        Setiap tahap perjalanan kepegawaian tersedia sebagai modul yang saling terhubung.
                    </p>
                </div>

                @php
                    $fiturList = [
                        ['icon' => 'fa-id-card', 'nama' => 'Database Pegawai', 'deskripsi' => 'Kepegawaian dan data induk pegawai'],
                        ['icon' => 'fa-clock', 'nama' => 'Presensi', 'deskripsi' => 'Kehadiran harian pegawai'],
                        ['icon' => 'fa-mug-hot', 'nama' => 'Lembur', 'deskripsi' => 'Pengajuan dan rekap jam lembur'],
                        ['icon' => 'fa-calendar-days', 'nama' => 'Cuti', 'deskripsi' => 'Pengajuan dan persetujuan cuti'],
                        ['icon' => 'fa-envelope-open-text', 'nama' => 'Surat Menyurat', 'deskripsi' => 'Administrasi persuratan'],
                        ['icon' => 'fa-user-plus', 'nama' => 'Rekrutmen', 'deskripsi' => 'Proses penerimaan pegawai baru'],
                        ['icon' => 'fa-user-check', 'nama' => 'Pengangkatan Pegawai Tetap', 'deskripsi' => 'Proses pegawai menjadi tetap'],
                        ['icon' => 'fa-arrow-up-right-dots', 'nama' => 'Penyesuaian Golongan', 'deskripsi' => 'Kenaikan dan perubahan golongan'],
                        ['icon' => 'fa-briefcase', 'nama' => 'Penyesuaian Jabatan Fungsional', 'deskripsi' => 'Perubahan jabatan fungsional'],
                        ['icon' => 'fa-graduation-cap', 'nama' => 'Tugas Belajar', 'deskripsi' => 'Administrasi tugas belajar'],
                        ['icon' => 'fa-sitemap', 'nama' => 'Jabatan Struktural', 'deskripsi' => 'Penempatan jabatan struktural'],
                        ['icon' => 'fa-star', 'nama' => 'Penilaian', 'deskripsi' => 'Evaluasi kinerja pegawai'],
                        ['icon' => 'fa-door-open', 'nama' => 'Pemberhentian Pegawai', 'deskripsi' => 'Administrasi purna tugas'],
                    ];
                @endphp

                <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                    @foreach ($fiturList as $fitur)
                        <div class="feature-card rounded-xl border border-blue-100 bg-white/90 p-6 shadow-sm">
                            <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-gradient-to-br from-[#62A6FF] to-[#E4B4FF] text-white shadow-sm">
                                <i class="fa-solid {{ $fitur['icon'] }}"></i>
                            </div>
                            <h3 class="mt-4 font-semibold leading-snug text-gray-800">{{ $fitur['nama'] }}</h3>
                            <p class="mt-1.5 text-xs leading-relaxed text-gray-500">{{ $fitur['deskripsi'] }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        {{-- Tim Pengembang --}}
        <section id="tim" class="section-anchor relative flex items-center overflow-hidden bg-gray-950">
            <p class="watermark-text absolute inset-x-0 top-1/2 -translate-y-1/2 select-none text-center text-[18vw] font-black leading-none tracking-normal sm:text-[13vw]">
                SIPENA
            </p>

            <div class="section-container relative">
                <div class="mx-auto mb-14 max-w-2xl text-center">
                    <span class="text-xs font-semibold uppercase tracking-widest text-[#A8C7FF]">Di Balik Layar</span>
                    <h2 class="mt-3 text-3xl font-bold text-white sm:text-4xl">
                        Tim Pengembang
                    </h2>
                    <p class="mt-4 text-gray-400">
                        SIPENA dibangun dan dirawat oleh Tim Ngawi Empire untuk Universitas YARSI.
                    </p>
                </div>

                @php
                    $timList = [
                        ['nama' => 'Hilal Rizqi Akbar', 'npm' => '1402022023', 'peran' => 'UI/UX Designer'],
                        ['nama' => 'Muhammad Ramadhan Prinada', 'npm' => '1402022043', 'peran' => 'Backend Developer'],
                        ['nama' => 'Rafly Eryan Azis', 'npm' => '1402022051', 'peran' => 'Frontend Developer'],
                    ];
                @endphp

                <div class="mx-auto grid w-5xl gap-12 sm:grid-cols-3">
                    @foreach ($timList as $anggota)
                        <div class="rounded-xl border border-white/10 bg-white/[0.08] p-6 text-center shadow-sm backdrop-blur-sm">
                            <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-xl bg-gradient-to-br from-[#62A6FF] to-[#E4B4FF] text-xl font-bold text-white">
                                {{ collect(explode(' ', $anggota['nama']))->map(fn($word) => $word[0])->take(2)->implode('') }}
                            </div>
                            <h3 class="mt-4 font-semibold text-white">{{ $anggota['nama'] }}</h3>
                            <h4 class="mt-1 text-white">{{ $anggota['npm']}}</h4>
                            <p class="mt-1 text-xs uppercase tracking-wide text-gray-400">{{ $anggota['peran'] }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    </main>

    <footer id="kontak" class="bg-black/80 px-4 pb-6 pt-16 text-gray-300 sm:px-6">
        <div class="mx-auto grid max-w-7xl grid-cols-1 gap-10 md:grid-cols-2 lg:grid-cols-12">
            <div class="space-y-4 lg:col-span-4">
                <div class="flex items-center gap-3">
                    <img src="{{ asset('favicon.ico') }}" alt="Logo SIPENA" class="h-10 w-10 rounded-xl">
                    <div>
                        <h3 class="text-lg font-semibold text-white">SIPENA</h3>
                        <p class="text-xs text-gray-400">Sistem Informasi Pegawai dan Administrasi YARSI</p>
                    </div>
                </div>
                <div class="overflow-hidden rounded-xl border border-white/10">
                    <iframe
                        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3966.7093913884783!2d106.86753037355352!3d-6.169654160458206!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e69f506b12dbf71%3A0xc934b14cf25a4d61!2sUniversitas%20YARSI!5e0!3m2!1sid!2sid!4v1774876824722!5m2!1sid!2sid"
                        class="h-40 w-full"
                        loading="lazy"
                        referrerpolicy="no-referrer-when-downgrade">
                    </iframe>
                </div>
                <p class="text-xs text-gray-500">Menara YARSI, Jakarta Pusat 10510</p>
            </div>

            <div class="lg:col-span-2">
                <h2 class="mb-4 text-sm font-semibold text-white">Menu</h2>
                <ul class="space-y-3 text-sm text-gray-400">
                    <li>
                        <a href="#beranda" class="flex items-center gap-3 transition hover:text-white">
                            <i class="fa-solid fa-home text-gray-500"></i>
                            <span>Beranda</span>
                        </a>
                    </li>
                    <li>
                        <a href="#tentang" class="flex items-center gap-3 transition hover:text-white">
                            <i class="fa-solid fa-circle-info text-gray-500"></i>
                            <span>Tentang</span>
                        </a>
                    </li>
                    <li>
                        <a href="#fitur" class="flex items-center gap-3 transition hover:text-white">
                            <i class="fa-solid fa-book text-gray-500"></i>
                            <span>Fitur</span>
                        </a>
                    </li>
                    <li>
                        <a href="#tim" class="flex items-center gap-3 transition hover:text-white">
                            <i class="fa-solid fa-users text-gray-500"></i>
                            <span>Tim Pengembang</span>
                        </a>
                    </li>
                </ul>
            </div>

            <div class="lg:col-span-3">
                <h2 class="mb-4 text-sm font-semibold text-white">Hubungi Kami</h2>
                <ul class="space-y-2 text-sm text-gray-400">
                    <li>
                        Email:
                        <a href="mailto:sekretariat.sdm@yarsi.ac.id" class="transition hover:text-white">
                            sekretariat.sdm@yarsi.ac.id
                        </a>
                    </li>
                    <li>
                        Telepon:
                        <a href="tel:+621234567890" class="transition hover:text-white">
                            +62 123-456-7890
                        </a>
                    </li>
                </ul>
                <div class="mt-4 flex gap-4 text-lg text-gray-400">
                    <a href="#" aria-label="Instagram" class="transition hover:text-white"><i class="fa-brands fa-instagram"></i></a>
                    <a href="#" aria-label="Twitter" class="transition hover:text-white"><i class="fa-brands fa-twitter"></i></a>
                    <a href="#" aria-label="TikTok" class="transition hover:text-white"><i class="fa-brands fa-tiktok"></i></a>
                    <a href="#" aria-label="Facebook" class="transition hover:text-white"><i class="fa-brands fa-facebook"></i></a>
                </div>
            </div>

            <div class="lg:col-span-3">
                <h2 class="mb-4 text-sm font-semibold text-white">Tim Pengembang</h2>
                <ul class="space-y-2 text-sm text-gray-400">
                    <li>Hilal Rizqi Akbar</li>
                    <li>Muhammad Ramadhan Prinada</li>
                    <li>Rafly Eryan Azis</li>
                </ul>
            </div>
        </div>

        <div class="mt-10 border-t border-white/10 pt-6">
            <div class="mx-auto max-w-7xl space-y-1 text-center text-xs text-gray-500 md:text-sm">
                <p>
                    &copy; {{ date('Y') }} <span class="text-white">SIPENA</span> - All Rights Reserved | Version {{ config('app.version') }}
                </p>
                <p>Dikembangkan oleh Tim Ngawi Empire</p>
            </div>
        </div>
    </footer>

</body>
</html>
