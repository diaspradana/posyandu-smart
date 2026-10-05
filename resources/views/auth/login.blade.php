<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Masuk | Posyandu Smart</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-gradient-to-br from-slate-50 via-emerald-50/40 to-teal-50 flex items-center justify-center p-4" style="font-family: 'Inter', sans-serif;">

    <div class="w-full max-w-md">

        {{-- MAIN CARD --}}
        <div class="bg-white rounded-3xl shadow-xl shadow-slate-200/60 border border-slate-100 p-8 sm:p-10">

            {{-- LOGO & HEADER --}}
            <div class="text-center mb-8">
                <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-2xl bg-gradient-to-tr from-emerald-600 to-teal-500 text-white text-2xl font-black shadow-lg shadow-emerald-600/30">
                    PS
                </div>

                <h1 class="text-2xl font-extrabold text-slate-800 tracking-tight">
                    Posyandu Smart
                </h1>

                <p class="mt-1.5 text-xs sm:text-sm text-slate-500">
                    Community Health & Maternal-Child Monitoring Platform
                </p>
            </div>

            {{-- FLASH / ERROR MESSAGES --}}
            @if (session('success'))
                <div class="mb-5 rounded-xl bg-emerald-50 border border-emerald-200 p-4 text-xs sm:text-sm text-emerald-800 flex items-start gap-2.5">
                    <span class="text-emerald-600 font-bold text-base leading-none">✓</span>
                    <div>{{ session('success') }}</div>
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-5 rounded-xl bg-rose-50 border border-rose-200 p-4 text-xs sm:text-sm text-rose-700">
                    <div class="font-semibold mb-1 flex items-center gap-1.5">
                        <span>⚠️</span>
                        <span>Perhatian:</span>
                    </div>
                    <ul class="list-disc list-inside space-y-0.5 text-xs text-rose-600">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- LOGIN FORM --}}
            <form method="POST" action="{{ route('login.process') }}" class="space-y-4">
                @csrf

                <div>
                    <label for="email" class="mb-1.5 block text-xs font-semibold uppercase tracking-wider text-slate-700">
                        Alamat Email
                    </label>

                    <input
                        type="email"
                        id="email"
                        name="email"
                        value="{{ old('email') }}"
                        required
                        autofocus
                        placeholder="contoh@posyandusmart.test"
                        class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-3 text-sm text-slate-800 outline-none transition focus:border-emerald-500 focus:bg-white focus:ring-4 focus:ring-emerald-500/10"
                    >
                </div>

                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label for="password" class="block text-xs font-semibold uppercase tracking-wider text-slate-700">
                            Kata Sandi
                        </label>
                    </div>

                    <input
                        type="password"
                        id="password"
                        name="password"
                        required
                        placeholder="••••••••"
                        class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-3 text-sm text-slate-800 outline-none transition focus:border-emerald-500 focus:bg-white focus:ring-4 focus:ring-emerald-500/10"
                    >
                </div>

                <div class="flex items-center justify-between pt-1">
                    <label class="flex items-center gap-2 cursor-pointer select-none">
                        <input
                            type="checkbox"
                            id="remember"
                            name="remember"
                            value="1"
                            class="h-4 w-4 rounded border-slate-300 text-emerald-600 focus:ring-emerald-500"
                        >
                        <span class="text-xs text-slate-600">Ingat sesi saya</span>
                    </label>
                </div>

                <button
                    type="submit"
                    class="w-full mt-2 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 px-4 py-3.5 font-semibold text-white shadow-lg shadow-emerald-600/25 transition hover:from-emerald-700 hover:to-teal-700 focus:outline-none focus:ring-4 focus:ring-emerald-500/20 active:scale-[0.99]"
                >
                    Masuk ke Sistem
                </button>
            </form>

            {{-- MENU REGISTER KHUSUS ADMIN --}}
            <div class="mt-6 pt-5 border-t border-slate-100 text-center">
                <p class="text-xs text-slate-500 mb-2">
                    Belum memiliki akun Admin Puskesmas?
                </p>
                <a
                    href="{{ route('register') }}"
                    class="inline-flex items-center justify-center gap-2 w-full rounded-xl border border-emerald-600/30 bg-emerald-50/70 px-4 py-2.5 text-xs font-bold text-emerald-700 transition hover:bg-emerald-100 hover:text-emerald-800 hover:border-emerald-600/50"
                >
                    <span>🏥</span>
                    <span>Registrasi Khusus Admin Puskesmas</span>
                </a>
            </div>

            {{-- INFO REGISTER PETUGAS/KADER --}}
            <div class="mt-4 rounded-2xl bg-blue-50/70 border border-blue-100 p-3.5 text-xs text-blue-900">
                <div class="flex items-start gap-2.5">
                    <span class="text-base leading-none">ℹ️</span>
                    <div class="leading-relaxed">
                        <p class="font-bold text-blue-900 mb-0.5">Petugas Posyandu / Kader:</p>
                        <p class="text-blue-700 text-[11.5px]">
                            Akun kader/petugas tapos didaftarkan langsung oleh <strong>Admin Puskesmas</strong> melalui menu <strong>Master Data Tapos</strong>.
                        </p>
                    </div>
                </div>
            </div>

            {{-- DEMO ACCOUNTS --}}
            <div class="mt-5 rounded-2xl bg-slate-50 border border-slate-100 p-4 text-[11px] text-slate-500">
                <div class="font-bold text-slate-700 mb-1.5 flex items-center justify-between">
                    <span>⚡ Akun Demo Pengujian:</span>
                    <span class="text-[10px] bg-slate-200 text-slate-700 px-1.5 py-0.5 rounded font-mono">dev</span>
                </div>
                <div class="space-y-1">
                    <div class="flex justify-between">
                        <span class="text-slate-600 font-medium">Admin:</span>
                        <code class="font-mono text-emerald-700">admin@posyandusmart.test</code>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-600 font-medium">Kader:</span>
                        <code class="font-mono text-emerald-700">kader@posyandusmart.test</code>
                    </div>
                    <div class="flex justify-between pt-1 border-t border-slate-200/60 text-slate-400">
                        <span>Password:</span>
                        <code class="font-mono text-slate-600 font-semibold">password123</code>
                    </div>
                </div>
            </div>

        </div>

        {{-- FOOTER NOTE --}}
        <p class="mt-6 text-center text-xs text-slate-400">
            &copy; {{ date('Y') }} Posyandu Smart — Sistem Informasi Posyandu Terpadu
        </p>

    </div>

</body>

</html>