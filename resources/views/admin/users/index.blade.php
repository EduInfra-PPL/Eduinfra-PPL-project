@php
    use Illuminate\Support\Carbon;

    $tabs = [
        'all' => 'Semua',
        'approved' => 'Disetujui',
        'pending' => 'Menunggu',
        'rejected' => 'Ditolak',
    ];
@endphp

<x-layouts.app title="Daftar Pengguna">
    <div class="max-w-[1520px] mx-auto px-4 lg:px-6 pb-10">
        <div class="bg-white rounded-2xl lg:rounded-3xl p-5 lg:p-6 shadow-sm border border-slate-100">

            <div class="flex flex-wrap items-center justify-between gap-3">
                <div>
                    <p class="text-[10px] font-bold tracking-wide text-slate-400 uppercase">Administrasi Pengguna</p>
                    <h1 class="text-xl font-extrabold text-[#14171F]">Daftar Pengguna &amp; Aktivitas Login</h1>
                </div>

                <nav class="flex flex-wrap items-center gap-1.5 p-1 bg-[#F8FAFC] border border-slate-200/60 rounded-full">
                    @foreach ($tabs as $key => $label)
                        <a href="{{ route('admin.users.index', array_filter(['status' => $key === 'all' ? null : $key, 'q' => $search])) }}"
                           class="px-4 py-2 text-xs font-semibold rounded-full {{ $status === $key ? 'bg-[#14171F] text-white' : 'text-slate-600 hover:bg-white' }}">
                            {{ $label }} ({{ $counts[$key] }})
                        </a>
                    @endforeach
                </nav>
            </div>

            <form method="GET" action="{{ route('admin.users.index') }}" class="mt-4 flex gap-2">
                @if ($status !== 'all')
                    <input type="hidden" name="status" value="{{ $status }}">
                @endif
                <input type="text" name="q" value="{{ $search }}" placeholder="Cari nama, email, atau NIP/NIK"
                       class="w-full sm:w-80 px-4 py-2 text-xs rounded-full bg-[#F8FAFC] border border-slate-200/70 focus:border-[#FF5C2A] focus:bg-white focus:outline-none">
                <button type="submit" class="px-4 py-2 text-xs font-semibold rounded-full bg-[#14171F] text-white">Cari</button>
            </form>

            <div class="mt-5 overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="text-[10px] font-bold uppercase tracking-wide text-slate-400 border-b border-slate-100">
                            <th class="py-3 pr-4">Pengguna</th>
                            <th class="py-3 pr-4">NIP/NIK</th>
                            <th class="py-3 pr-4">Peran</th>
                            <th class="py-3 pr-4">Wilayah &amp; Satker</th>
                            <th class="py-3 pr-4">Status</th>
                            <th class="py-3 pr-4">Mendaftar</th>
                            <th class="py-3 pr-4">Terakhir Login</th>
                            <th class="py-3">Jml Login</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($users as $user)
                            <tr class="align-top">
                                <td class="py-3 pr-4">
                                    <div class="font-bold text-[#14171F]">{{ $user->name }}</div>
                                    <div class="text-slate-500">{{ $user->email }}</div>
                                </td>
                                <td class="py-3 pr-4 font-mono text-slate-700">{{ $user->identity_number }}</td>
                                <td class="py-3 pr-4">
                                    <div class="font-semibold text-slate-700">
                                        {{ $user->registration_role ? (\App\Enums\RegistrationRole::tryFrom($user->registration_role)?->title() ?? $user->registration_role) : '-' }}
                                    </div>
                                    <div class="text-slate-400">{{ $user->role_enum ?? 'belum ditetapkan' }}</div>
                                </td>
                                <td class="py-3 pr-4">
                                    <div class="text-slate-700">{{ $user->region_name ?? '-' }}</div>
                                    <div class="text-slate-400">{{ $user->organization }}</div>
                                </td>
                                <td class="py-3 pr-4">
                                    @if ($user->is_verified)
                                        <span class="px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-700 font-semibold">Disetujui</span>
                                    @elseif ($user->rejected_at)
                                        <span class="px-2 py-0.5 rounded-full bg-red-50 text-red-600 font-semibold">Ditolak</span>
                                    @else
                                        <span class="px-2 py-0.5 rounded-full bg-amber-50 text-amber-700 font-semibold">Menunggu</span>
                                    @endif
                                </td>
                                <td class="py-3 pr-4 text-slate-500 whitespace-nowrap">{{ $user->created_at?->format('d M Y H:i') }}</td>
                                <td class="py-3 pr-4 text-slate-500 whitespace-nowrap">
                                    {{ $user->last_login_at ? Carbon::parse($user->last_login_at)->format('d M Y H:i') : 'Belum pernah' }}
                                </td>
                                <td class="py-3 font-semibold text-slate-700">{{ $user->login_count }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="py-10 text-center text-slate-400">Tidak ada pengguna yang cocok.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">{{ $users->links() }}</div>
        </div>
    </div>
</x-layouts.app>