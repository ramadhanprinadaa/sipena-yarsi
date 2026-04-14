<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $code ?? 'Error' }} - {{ config('app.name') }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&family=Lexend:wght@100..900&family=Open+Sans:ital,wght@0,300..800;1,300..800&family=Roboto:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet" />

    @vite('resources/css/app.css')
</head>
<body class="min-h-screen max-h-screen overflow-hidden font-lexend
             bg-gradient-to-br from-pink-300 to-blue-300 
             flex items-center justify-center">

    <div class="w-full max-w-2xl mx-auto px-6">
        
        <div class="bg-white/80 backdrop-blur-md rounded-2xl shadow-xl p-8 text-center">

            {{-- Gambar --}}
            @isset($image)
                <img 
                    src="{{ $image }}"
                    alt="Error illustration"
                    class="w-64 mx-auto mb-6"
                >
            @else
                <img 
                    src="https://illustrations.popsy.co/gray/web-error.svg"
                    alt="Error illustration"
                    class="w-64 mx-auto mb-6"
                >
            @endisset

            <h1 class="text-6xl font-bold text-gray-800">
                {{ $code ?? 'Error' }}
            </h1>

            <p class="mt-3 text-gray-600 text-lg">
                {{ $message ?? 'Terjadi kesalahan pada sistem' }}
            </p>

            <div class="mt-6 flex justify-center gap-3">
                <button
                    onclick="history.back()"
                    class="px-5 py-2.5 rounded-lg bg-gray-800 text-white 
                           hover:bg-gray-700 transition cursor-pointer">
                    Kembali
                </button>
            </div>
        </div>
    </div>
</body>
</html>