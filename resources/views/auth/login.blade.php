<x-layouts.app active="login" title="Masuk - EduInfra Tracker">
    <div class="max-w-md mx-auto mt-12">
        <div class="bg-white rounded-3xl border border-slate-200/80 p-8 shadow-sm">
            <h1 class="text-xl font-extrabold text-[#14171F] mb-1">Masuk ke EduInfra Tracker</h1>
            <p class="text-xs text-slate-500 mb-6">Masukkan email dan password akun Anda.</p>

            @if (session('status'))
                <div class="mb-4">
                    <x-ui.alert variant="success">{{ session('status') }}</x-ui.alert>
                </div>
            @endif

            <form method="POST" action="{{ route('login.store') }}" class="flex flex-col gap-4">
                @csrf

                <x-ui.input
                    name="email"
                    type="email"
                    label="Email"
                    placeholder="nama@email.com"
                    :error="$errors->first('email')"
                />

                <x-ui.input
                    name="password"
                    type="password"
                    label="Password"
                    placeholder="••••••••"
                    :error="$errors->first('password')"
                />

                <label class="flex items-center gap-2 text-xs text-slate-600">
                    <input type="checkbox" name="remember" value="1" class="rounded border-slate-300">
                    Ingat saya
                </label>

                <x-ui.button type="submit" variant="primary" size="lg" class="w-full">
                    Masuk
                </x-ui.button>
            </form>

            @if (Route::has('password.request'))
                <p class="mt-4 text-center text-xs">
                    <a href="{{ route('password.request') }}" class="text-[#EA4A18] font-semibold hover:underline">Lupa password?</a>
                </p>
            @endif
        </div>
    </div>
</x-layouts.app>
