<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>SIPENA | Login</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <script src="https://unpkg.com/@popperjs/core@2"></script>
    <script src="https://unpkg.com/tippy.js@6"></script>
    <script defer src="https://unpkg.com/@alpinejs/persist@3.x.x/dist/cdn.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.15.8/dist/cdn.min.js"></script>

    <script src="https://unpkg.com/boxicons@2.1.4/dist/boxicons.js"></script>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&family=Lexend:wght@100..900&family=Open+Sans:ital,wght@0,300..800;1,300..800&family=Roboto:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>

<body class="bg-gradient-to-br from-pink-300 to-blue-300 font-lexend min-h-screen flex justify-center items-center">

    {{-- Error Message --}}
    @error('login')
        <div 
            x-data="{ open: true }"
            x-show="open"
            class="fixed inset-0 flex items-center justify-center bg-black/50 z-50"
        >
            <div 
                x-show="open"
                x-transition
                class="bg-white rounded-2xl shadow-xl p-6 w-120 text-center"
            >
                <h2 class="text-lg font-semibold text-red-600 mb-2">
                    Login Gagal
                </h2>
                <p class="text-gray-600 mb-4">
                    {{ $message }}
                </p>
                <button 
                    @click="open = false"
                    class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 cursor-pointer"
                >
                    Tutup
                </button>
            </div>
        </div>
    @enderror
    
    {{-- Form Login --}}
    <div class="w-full max-w-5xl flex flex-col md:flex-row bg-white/20 backdrop-blur-xl rounded-xl shadow-2xl overflow-hidden">
        {{-- Left Side Image Background --}}
        <div class="hidden md:flex w-1/2 relative bg-cover bg-center"
            style="background-image: url('{{ asset('images/univ-yarsi.png') }}');">
            <div class="absolute inset-0 bg-black/20"></div>
            <div class="relative text-white flex flex-col justify-center items-center text-center">
            </div>
        </div>
        {{-- Right Side Login Form --}}
        <div class="w-full md:w-1/2 flex items-center justify-center p-10">
            <div class="w-full max-w-sm">
                {{-- Title --}}
                <h2 class="text-3xl font-bold text-indigo-500 mb-2 text-center font-lexend">
                    SIPENA
                </h2>
                <p class="text-gray-700 text-xl mb-8 text-center">
                    Sistem Informasi Pegawai dan Administrasi Universitas YARSI
                </p>

                {{-- Form --}}
                <form class="space-y-5" action="{{ route('auth.handle.login') }}" method="POST">
                    @csrf
                    {{-- Username --}}
                    <div>
                        <label for="username" 
                        class="block text-sm text-gray-700">
                            Username / Email
                        </label>

                        @error('username')
                            <small class="text-red-500">
                                {{ $message }}
                            </small>
                        @enderror

                        <input 
                            name="username" 
                            id="username" 
                            type="text" 
                            placeholder="Masukkan Username atau Email" 
                            value="{{ old('username') }}"
                            class="w-full mt-1 px-4 py-3 rounded-xl 
                            bg-white/60 backdrop-blur-md
                            border border-gray-300
                            focus:outline-none focus:ring-2 focus:ring-indigo-400"
                        >
                    </div>

                    {{-- Password --}}
                    <div x-data="{ showPassword: false }">
                        <label for="password" class="block text-sm text-gray-700">
                            Password
                        </label>
                        @error('password')
                            <small class="text-red-500">
                                {{ $message }}
                            </small>
                        @enderror
                        <div class="relative mt-1">
                            <input 
                                id="password" 
                                name="password" 
                                type="password"
                                :type = "showPassword ? 'text' : 'password'"
                                placeholder="Masukkan password"
                                class="w-full mt-1 px-4 py-3 rounded-xl
                                bg-white/60 backdrop-blur-md
                                border border-gray-300
                                focus:outline-none focus:ring-2 focus:ring-indigo-400">
                            <button 
                                type="button"
                                @click="showPassword = !showPassword"
                                class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-500 hover:text-gray-700 cursor-pointer"
                            >
                                <i id="toggleIcon" :class="showPassword ? 'fa-eye-slash' : 'fa-eye'" class="fa-solid"></i>
                            </button>
                        </div>
                    </div>

                    {{-- Login Button --}}
                    <button type="submit"
                        class="cursor-pointer w-full py-3 rounded-xl bg-indigo-400 text-white font-semibold hover:bg-indigo-500 transition">
                        Masuk
                    </button>
                </form>

                {{-- Back to Dashboard --}}
                <div class="mt-6 text-center">
                    <a href="/dashboard"
                        class="text-sm text-indigo-400 hover:underline">
                        ← Kembali ke Dashboard
                    </a>
                </div>
            </div>
        </div>
    </div>

</body>
</html>

