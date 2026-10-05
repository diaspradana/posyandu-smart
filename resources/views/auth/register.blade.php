<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Registrasi Admin Puskesmas | Posyandu Smart</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-gradient-to-br from-slate-50 via-emerald-50/40 to-teal-50 flex items-center justify-center p-4 py-8" style="font-family: 'Inter', sans-serif;">

    <div class="w-full max-w-lg">

        {{-- MAIN CARD --}}
        <div class="bg-white rounded-3xl shadow-xl shadow-slate-200/60 border border-slate-100 p-8 sm:p-10">

            {{-- LOGO & HEADER --}}
            <div class="text-center mb-6">
                <div class="mx-auto mb-3.5 flex h-14 w-14 items-center justify-center rounded-2xl bg-gradient-to-tr from-emerald-600 to-teal-500 text-white text-xl font-black shadow-lg shadow-emerald-600/30">
                    PS
                </div>

                <div class="inline-block px-3 py-1 mb-2 rounded-full bg-emerald-100 text-emerald-800 text-[11px] font-bold tracking-wide uppercase">
                    Khusus Admin Puskesmas
                </div>

                <h1 class="text-2xl font-extrabold text-slate-800 tracking-tight">
                    Pendaftaran Admin Puskesmas
                </h1>

                <p class="mt-1 text-xs sm:text-sm text-slate-500">
                    Daftarkan akun administrator untuk mengelola posyandu & data kesehatan wilayah.
                </p>
            </div>

            {{-- IMPORTANT NOTICE --}}
            <div class="mb-6 rounded-2xl bg-amber-50/80 border border-amber-200/80 p-4 text-xs text-amber-900 leading-relaxed">
                <div class="flex items-start gap-2.5">
                    <span class="text-lg leading-none">📌</span>
                    <div>
                        <strong class="font-bold text-amber-950 block mb-1">Ketentuan Hak Akses & Registrasi:</strong>
                        <ul class="list-disc list-inside space-y-1 text-amber-800 text-[11.5px]">
                            <li>Formulir mandiri ini <strong>hanya untuk Admin / Tenaga Kesehatan Puskesmas</strong>.</li>
                            <li>Akun <strong>Petugas Tapos / Kader Posyandu</strong> didaftarkan langsung oleh Admin Puskesmas melalui <strong>Master Data Tapos</strong>.</li>
                        </ul>
                    </div>
                </div>
            </div>

            {{-- ERROR MESSAGES --}}
            @if ($errors->any())
                <div class="mb-5 rounded-xl bg-rose-50 border border-rose-200 p-4 text-xs sm:text-sm text-rose-700">
                    <div class="font-semibold mb-1 flex items-center gap-1.5">
                        <span>⚠️</span>
                        <span>Periksa kembali data formulir:</span>
                    </div>
                    <ul class="list-disc list-inside space-y-0.5 text-xs text-rose-600">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- REGISTRATION FORM --}}
            <form method="POST" action="{{ route('register.process') }}" class="space-y-4">
                @csrf

                {{-- NAMA LENGKAP --}}
                <div>
                    <label for="name" class="mb-1.5 block text-xs font-semibold uppercase tracking-wider text-slate-700">
                        Nama Lengkap Admin <span class="text-rose-500">*</span>
                    </label>

                    <input
                        type="text"
                        id="name"
                        name="name"
                        value="{{ old('name') }}"
                        required
                        autofocus
                        placeholder="Contoh: dr. Andi Pratama / Admin Puskesmas"
                        class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-3 text-sm text-slate-800 outline-none transition focus:border-emerald-500 focus:bg-white focus:ring-4 focus:ring-emerald-500/10"
                    >
                </div>

                {{-- EMAIL --}}
                <div>
                    <label for="email" class="mb-1.5 block text-xs font-semibold uppercase tracking-wider text-slate-700">
                        Alamat Email Dinas / Akun <span class="text-rose-500">*</span>
                    </label>

                    <input
                        type="email"
                        id="email"
                        name="email"
                        value="{{ old('email') }}"
                        required
                        placeholder="admin.puskesmas@domain.id"
                        class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-3 text-sm text-slate-800 outline-none transition focus:border-emerald-500 focus:bg-white focus:ring-4 focus:ring-emerald-500/10"
                    >
                </div>

                {{-- PUSKESMAS SELECTION --}}
                <div>
                    <label for="puskesmas_id" class="mb-1.5 block text-xs font-semibold uppercase tracking-wider text-slate-700">
                        Puskesmas Wilayah Kerja
                    </label>

                    @if(isset($puskesmasList) && $puskesmasList->count() > 0)
                        <select
                            id="puskesmas_id"
                            name="puskesmas_id"
                            class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-3 text-sm text-slate-800 outline-none transition focus:border-emerald-500 focus:bg-white focus:ring-4 focus:ring-emerald-500/10"
                            onchange="toggleNewPuskesmasInput(this.value)"
                        >
                            @foreach($puskesmasList as $pusk)
                                <option value="{{ $pusk->id }}" @selected(old('puskesmas_id') == $pusk->id)>
                                    {{ $pusk->nama }} ({{ $pusk->kecamatan ?? 'Wilayah' }})
                                </option>
                            @endforeach
                            <option value="__new__" @selected(old('puskesmas_id') === '__new__')>
                                ➕ Daftarkan Puskesmas Baru...
                            </option>
                        </select>
                    @endif

                    <div id="newPuskesmasWrap" class="{{ (isset($puskesmasList) && $puskesmasList->count() > 0 && old('puskesmas_id') !== '__new__') ? 'hidden' : '' }} mt-2">
                        <input
                            type="text"
                            id="puskesmas_nama"
                            name="puskesmas_nama"
                            value="{{ old('puskesmas_nama') }}"
                            placeholder="Ketik Nama Puskesmas Baru (Contoh: Puskesmas Sukamaju)"
                            class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-3 text-sm text-slate-800 outline-none transition focus:border-emerald-500 focus:bg-white focus:ring-4 focus:ring-emerald-500/10"
                        >
                        <span class="text-[11px] text-slate-400 mt-1 block">Puskesmas baru akan otomatis dibuatkan jika belum ada di daftar.</span>
                    </div>
                </div>

                {{-- PASSWORD --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                    <div>
                        <label for="password" class="mb-1.5 block text-xs font-semibold uppercase tracking-wider text-slate-700">
                            Kata Sandi <span class="text-rose-500">*</span>
                        </label>

                        <input
                            type="password"
                            id="password"
                            name="password"
                            required
                            placeholder="Min. 8 karakter"
                            class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-3 text-sm text-slate-800 outline-none transition focus:border-emerald-500 focus:bg-white focus:ring-4 focus:ring-emerald-500/10"
                        >
                    </div>

                    <div>
                        <label for="password_confirmation" class="mb-1.5 block text-xs font-semibold uppercase tracking-wider text-slate-700">
                            Ulangi Sandi <span class="text-rose-500">*</span>
                        </label>

                        <input
                            type="password"
                            id="password_confirmation"
                            name="password_confirmation"
                            required
                            placeholder="Ulangi kata sandi"
                            class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-3 text-sm text-slate-800 outline-none transition focus:border-emerald-500 focus:bg-white focus:ring-4 focus:ring-emerald-500/10"
                        >
                    </div>
                </div>

                {{-- SUBMIT BUTTON --}}
                <button
                    type="submit"
                    class="w-full mt-3 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 px-4 py-3.5 font-semibold text-white shadow-lg shadow-emerald-600/25 transition hover:from-emerald-700 hover:to-teal-700 focus:outline-none focus:ring-4 focus:ring-emerald-500/20 active:scale-[0.99]"
                >
                    Daftar Sebagai Admin Puskesmas
                </button>
            </form>

            {{-- BACK TO LOGIN --}}
            <div class="mt-6 pt-5 border-t border-slate-100 text-center">
                <p class="text-xs text-slate-500">
                    Sudah memiliki akun terdaftar?
                    <a href="{{ route('login') }}" class="font-bold text-emerald-700 hover:text-emerald-800 underline underline-offset-2 ml-1">
                        Masuk ke Sistem &rarr;
                    </a>
                </p>
            </div>

        </div>

        {{-- FOOTER NOTE --}}
        <p class="mt-6 text-center text-xs text-slate-400">
            &copy; {{ date('Y') }} Posyandu Smart — Sistem Informasi Posyandu Terpadu
        </p>

    </div>

    <script>
        function toggleNewPuskesmasInput(val) {
            const wrap = document.getElementById('newPuskesmasWrap');
            const input = document.getElementById('puskesmas_nama');
            if (val === '__new__') {
                wrap.classList.remove('hidden');
                input.focus();
            } else {
                wrap.classList.add('hidden');
                input.value = '';
            }
        }
    </script>

</body>

</html>
