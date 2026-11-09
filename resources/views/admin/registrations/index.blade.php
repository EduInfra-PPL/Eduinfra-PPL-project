<x-layouts.app title="Persetujuan Registrasi">
    <div class="max-w-[1520px] mx-auto px-4 lg:px-6 pb-10">
        <div class="bg-white rounded-2xl lg:rounded-3xl p-5 lg:p-6 shadow-sm border border-slate-100">

            <div class="flex flex-wrap items-center justify-between gap-3">
                <div>
                    <p class="text-[10px] font-bold tracking-wide text-slate-400 uppercase">Administrasi Pengguna</p>
                    <h1 class="text-xl font-extrabold text-[#14171F]">Persetujuan Registrasi Akun</h1>
                </div>

                <nav class="flex items-center gap-1.5 p-1 bg-[#F8FAFC] border border-slate-200/60 rounded-full">
                    <a href="{{ route('admin.registrations.index') }}"
                       class="px-4 py-2 text-xs font-semibold rounded-full {{ $status === 'pending' ? 'bg-[#14171F] text-white' : 'text-slate-600 hover:bg-white' }}">
                        Menunggu ({{ $counts['pending'] }})
                    </a>
                    <a href="{{ route('admin.registrations.index', ['status' => 'rejected']) }}"
                       class="px-4 py-2 text-xs font-semibold rounded-full {{ $status === 'rejected' ? 'bg-[#14171F] text-white' : 'text-slate-600 hover:bg-white' }}">
                        Ditolak ({{ $counts['rejected'] }})
                    </a>
                </nav>
            </div>

            @if (session('status'))
                <div class="mt-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-700 text-xs font-medium px-4 py-3">
                    {{ session('status') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="mt-4 rounded-xl bg-red-50 border border-red-200 text-red-700 text-xs font-medium px-4 py-3">
                    {{ $errors->first() }}
                </div>
            @endif

            <div class="mt-5 overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="text-[10px] font-bold uppercase tracking-wide text-slate-400 border-b border-slate-100">
                            <th class="py-3 pr-4">Pendaftar</th>
                            <th class="py-3 pr-4">NIP/NIK</th>
                            <th class="py-3 pr-4">Peran</th>
                            <th class="py-3 pr-4">Wilayah &amp; Satker</th>
                            <th class="py-3 pr-4">Mendaftar</th>
                            <th class="py-3">{{ $status === 'rejected' ? 'Alasan Penolakan' : 'Tindakan' }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($applicants as $applicant)
                            <tr class="align-top">
                                <td class="py-3 pr-4">
                                    <div class="font-bold text-[#14171F]">{{ $applicant->name }}</div>
                                    <div class="text-slate-500">{{ $applicant->email }}</div>
                                    <div class="text-slate-400">{{ $applicant->phone }}</div>
                                </td>
                                <td class="py-3 pr-4 font-mono text-slate-700">{{ $applicant->identity_number }}</td>
                                <td class="py-3 pr-4">
                                    <div class="font-semibold text-slate-700">
                                        {{ \App\Enums\RegistrationRole::tryFrom($applicant->registration_role)?->title() ?? $applicant->registration_role }}
                                    </div>
                                    <div class="text-slate-400">{{ $applicant->role_enum }}</div>
                                </td>
                                <td class="py-3 pr-4">
                                    <div class="text-slate-700">{{ $applicant->region_name ?? '-' }}</div>
                                    <div class="text-slate-400">{{ $applicant->organization }}</div>
                                </td>
                                <td class="py-3 pr-4 text-slate-500 whitespace-nowrap">{{ $applicant->created_at?->format('d M Y H:i') }}</td>
                                <td class="py-3">
                                    @if ($status === 'pending')
                                        <div class="flex flex-col gap-2 min-w-[240px]">
                                            <form method="POST" action="{{ route('admin.registrations.approve', $applicant) }}">
                                                @csrf
                                                <button type="submit"
                                                        class="w-full px-3 py-2 rounded-lg bg-emerald-600 text-white font-semibold hover:bg-emerald-700 transition-colors">
                                                    Setujui
                                                </button>
                                            </form>
                                            <form method="POST" action="{{ route('admin.registrations.reject', $applicant) }}" class="flex gap-2">
                                                @csrf
                                                <input type="text" name="rejection_reason" required minlength="5" maxlength="500"
                                                       placeholder="Alasan penolakan"
                                                       class="flex-1 min-w-0 px-3 py-2 rounded-lg bg-[#F1F5F9] border border-transparent focus:border-[#FF5C2A] focus:bg-white focus:outline-none">
                                                <button type="submit"
                                                        class="px-3 py-2 rounded-lg bg-red-50 text-red-600 font-semibold hover:bg-red-100 transition-colors">
                                                    Tolak
                                                </button>
                                            </form>
                                        </div>
                                    @else
                                        <div class="text-slate-700">{{ $applicant->rejection_reason }}</div>
                                        <div class="text-slate-400 mt-1">{{ $applicant->rejected_at?->format('d M Y H:i') }}</div>
                                        <form method="POST" action="{{ route('admin.registrations.approve', $applicant) }}" class="mt-2">
                                            @csrf
                                            <button type="submit" class="font-semibold text-emerald-600 hover:text-emerald-700">
                                                Setujui sekarang
                                            </button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-10 text-center text-slate-400">
                                    {{ $status === 'rejected' ? 'Belum ada permohonan yang ditolak.' : 'Tidak ada permohonan yang menunggu.' }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">{{ $applicants->links() }}</div>
        </div>
    </div>
</x-layouts.app>