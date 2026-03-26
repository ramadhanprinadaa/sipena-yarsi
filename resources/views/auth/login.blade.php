<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>SIPENA | Login</title>
    @vite(['resources/css/app.css','resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <script defer src="https://unpkg.com/@alpinejs/persist@3.x.x/dist/cdn.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.15.8/dist/cdn.min.js"></script>
</head>

<body class="bg-gray-500 min-h-screen flex justify-center items-center">
<div class="w-full max-w-5xl min-h-[550px] flex flex-col md:flex-row bg-white rounded-3xl shadow-2xl overflow-hidden">
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
            <h2 class="text-3xl font-bold text-cyan-700 mb-2 text-center">
                SIPENA
            </h2>
            <p class="text-gray-600 text-xl mb-8 text-center">
                Sistem Informasi Pegawai dan Administrasi Universitas YARSI
            </p>
            {{-- Form --}}
            <form class="space-y-5">
                {{-- Email / Username --}}
                <div>
                    <label class="text-sm text-gray-600">Email / Username</label>
                    <input type="text"
                        placeholder="Masukkan email atau username"
                        class="w-full mt-1 px-4 py-3 rounded-xl 
                        bg-white/60 backdrop-blur-md
                        border border-gray-300
                        focus:outline-none focus:ring-2 focus:ring-cyan-400">
                </div>
                {{-- Password --}}
                <div x-data="{ showPassword: false }">
                    <label class="text-sm text-gray-600">Password</label>
                    <div class="relative mt-1">
                        <input id="password"
                            :type = "showPassword ? 'text' : 'password'"
                            placeholder="Masukkan password"
                            class="w-full mt-1 px-4 py-3 rounded-xl
                            bg-white/60 backdrop-blur-md
                            border border-gray-300
                            focus:outline-none focus:ring-2 focus:ring-cyan-400">
                        <button 
                            type="button"
                            @click="showPassword = !showPassword"
                            class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-500 hover:text-gray-700 cursor-pointer"
                        >
                            <i id="toggleIcon" :class="showPassword ? 'fa-eye-slash' : 'fa-eye'" class="fa-solid"></i>
                        </button>
                    </div>
                </div>
                {{-- Remember --}}
                <div class="flex w-full items-center justify-between text-sm">
                    <label class="flex items-center gap-2">
                        <input type="checkbox" class="accent-cyan-600 size-4 cursor-pointer">
                        Ingat Saya
                    </label>
                </div>
                {{-- Login Button --}}
                <button type="submit"
                    class="cursor-pointer w-full py-3 rounded-xl bg-cyan-600 text-white font-semibold hover:bg-cyan-700 transition">
                    Masuk
                </button>
            </form>
            {{-- Back to Dashboard --}}
            <div class="mt-6 text-center">
                <a href="/dashboard"
                    class="text-sm text-cyan-600 hover:underline">
                    ← Kembali ke Dashboard
                </a>
            </div>
        </div>
    </div>
</div>

<script>

    // function togglePassword(){
    //     const password = document.getElementById("password");
    //     const icon = document.getElementById("toggleIcon");
    //     if(password.type === "password"){
    //         password.type = "text";
    //         icon.classList.remove("fa-eye");
    //         icon.classList.add("fa-eye-slash");
    //     }else{
    //         password.type = "password";
    //         icon.classList.remove("fa-eye-slash");
    //         icon.classList.add("fa-eye");
    //     }
    // }

</script>

</body>
</html>

