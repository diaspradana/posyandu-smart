<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Login | Posyandu Smart</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-gray-100 flex items-center justify-center">

    <div class="w-full max-w-md px-6">

        <div class="bg-white rounded-2xl shadow-lg p-8">

            <div class="text-center mb-8">

                <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-2xl bg-emerald-600 text-white text-2xl font-bold">
                    PS
                </div>

                <h1 class="text-2xl font-bold text-gray-800">
                    Posyandu Smart
                </h1>

                <p class="mt-2 text-sm text-gray-500">
                    Community Health Monitoring Platform
                </p>

            </div>


            @if ($errors->any())

                <div class="mb-5 rounded-lg bg-red-50 p-4 text-sm text-red-700">

                    @foreach ($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach

                </div>

            @endif


            <form method="POST" action="{{ route('login.process') }}">

                @csrf

                <div class="mb-5">

                    <label
                        for="email"
                        class="mb-2 block text-sm font-medium text-gray-700">
                        Email
                    </label>

                    <input
                        type="email"
                        id="email"
                        name="email"
                        value="{{ old('email') }}"
                        required
                        autofocus
                        class="w-full rounded-lg border border-gray-300 px-4 py-3 outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200">

                </div>


                <div class="mb-5">

                    <label
                        for="password"
                        class="mb-2 block text-sm font-medium text-gray-700">
                        Password
                    </label>

                    <input
                        type="password"
                        id="password"
                        name="password"
                        required
                        class="w-full rounded-lg border border-gray-300 px-4 py-3 outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200">

                </div>


                <div class="mb-6 flex items-center gap-2">

                    <input
                        type="checkbox"
                        id="remember"
                        name="remember"
                        value="1"
                        class="h-4 w-4 rounded border-gray-300 text-emerald-600">

                    <label
                        for="remember"
                        class="text-sm text-gray-600">
                        Ingat saya
                    </label>

                </div>


                <button
                    type="submit"
                    class="w-full rounded-lg bg-emerald-600 px-4 py-3 font-semibold text-white transition hover:bg-emerald-700">

                    Masuk

                </button>

            </form>


            <div class="mt-6 rounded-lg bg-gray-50 p-4 text-xs text-gray-500">

                <p class="font-semibold mb-2">
                    Akun Demo
                </p>

                <p>
                    Admin: admin@posyandusmart.test
                </p>

                <p>
                    Kader: kader@posyandusmart.test
                </p>

                <p class="mt-1">
                    Password: password123
                </p>

            </div>

        </div>

    </div>

</body>

</html>