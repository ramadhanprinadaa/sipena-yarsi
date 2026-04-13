<div class="max-w-7xl mx-auto grid grid-cols-1 md:grid-cols-2 lg:grid-cols-12 gap-10">
    {{-- Kolom 1: Brand --}}
    <div class="space-y-4 lg:col-span-4">
        <div class="flex items-center gap-3">
            <img src="{{ asset('favicon.ico') }}" class="w-8 h-8">
            <h3 class="text-lg font-semibold text-white">SIPENA</h3>
        </div>
        <p class="text-xs text-gray-400 leading-relaxed">
            Sistem Informasi Pegawai dan Administrasi YARSI
        </p>
        <div class="rounded-lg overflow-hidden border border-gray-700">
            <iframe 
                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3966.7093913884783!2d106.86753037355352!3d-6.169654160458206!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e69f506b12dbf71%3A0xc934b14cf25a4d61!2sUniversitas%20YARSI!5e0!3m2!1sid!2sid!4v1774876824722!5m2!1sid!2sid"
                class="w-full"
                loading="lazy">
            </iframe>
        </div>
        <p class="text-xs text-gray-500">
            Menara Yarsi, Jakarta Pusat 10510
        </p>
    </div>

    {{-- Kolom 2: Menu --}}
    <div class="lg:col-span-2">
        <h2 class="text-sm font-semibold text-white mb-4">Menu</h2>
        <ul class="space-y-3 text-sm text-gray-400">
            <li>
                <a href="#" class="flex items-center gap-3 hover:text-white transition">
                    <i class="fa-solid fa-home text-gray-500"></i>
                    <span>Beranda</span>
                </a>
            </li>
            <li>
                <a href="#" class="flex items-center gap-3 hover:text-white transition">
                    <i class="fa-solid fa-user text-gray-500"></i>
                    <span>Kepegawaian</span>
                </a>
            </li>
            <li>
                <a href="#" class="flex items-center gap-3 hover:text-white transition">
                    <i class="fa-solid fa-clock text-gray-500"></i>
                    <span>Presensi</span>
                </a>
            </li>
            <li>
                <a href="#" class="flex items-center gap-3 hover:text-white transition">
                    <i class="fa-solid fa-mug-hot text-gray-500"></i>
                    <span>Lembur</span>
                </a>
            </li>
            <li>
                <a href="#" class="flex items-center gap-3 hover:text-white transition">
                    <i class="fa-solid fa-calendar-days text-gray-500"></i>
                    <span>Cuti</span>
                </a>
            </li>
            <li>
                <a href="#" class="flex items-center gap-3 hover:text-white transition">
                    <i class="fa-solid fa-book text-gray-500"></i>
                    <span>Daftar Modul</span>
                </a>
            </li>
        </ul>
    </div>

    {{-- Kolom 3: Kontak --}}
    <div class="lg:col-span-3">
        <h2 class="text-sm font-semibold text-white mb-4">Hubungi Kami</h2>
        <ul class="space-y-2 text-sm text-gray-400">
            <li>
                Email:
                <a href="mailto:sekretariat.sdm@yarsi.ac.id" class="hover:text-white">
                    sekretariat.sdm@yarsi.ac.id
                </a>
            </li>
            <li>
                Telepon:
                <a href="tel:+621234567890" class="hover:text-white">
                    +62 123-456-7890
                </a>
            </li>
        </ul>
        {{-- Social --}}
        <div class="flex gap-4 mt-4 text-lg text-gray-400">
            <a href="#" class="hover:text-white"><i class="fa-brands fa-instagram"></i></a>
            <a href="#" class="hover:text-white"><i class="fa-brands fa-twitter"></i></a>
            <a href="#" class="hover:text-white"><i class="fa-brands fa-tiktok"></i></a>
            <a href="#" class="hover:text-white"><i class="fa-brands fa-facebook"></i></a>
        </div>
    </div>

    {{-- Kolom 4: Tim --}}
    <div class="lg:col-span-3">
        <h2 class="text-sm font-semibold text-white mb-4">Tim Pengembang</h2>
        <ul class="space-y-2 text-sm text-gray-400">
            <li>Hilal Rizqi Akbar</li>
            <li>Muhammad Ramadhan Prinada</li>
            <li>Rafly Eryan Azis</li>
        </ul>
    </div>
</div>

{{-- Bottom --}}
<div class="border-t border-gray-100 mt-3 pt-3">
    <div class="max-w-7xl mx-auto px-6 text-center text-xs md:text-sm text-gray-500 space-y-1">
        <p>
            © {{ date('Y') }} <span class="text-white">SIPENA</span> — All Rights Reserved | Version {{ config('app.version') }}
        </p>
        <p>
            Dikembangkan oleh Tim Ngawi Empire
        </p>
    </div>
</div>