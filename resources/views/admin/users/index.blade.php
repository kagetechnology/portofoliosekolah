@extends('layouts.admin')
@section('title', 'Manajemen Pengguna')

@section('content')
    <header class="mb-6 flex items-end justify-between">
        <div>
            <p class="text-xs font-semibold uppercase tracking-widest text-blue-600">Admin</p>
            <h1 class="font-display text-3xl font-bold tracking-tight text-zinc-900">Pengguna</h1>
            <p class="mt-1 text-sm text-zinc-500">Kelola akun siswa, ubah role, dan aktifkan/nonaktifkan akun.</p>
        </div>
    </header>

    <form method="GET" class="mb-5 rounded-2xl border border-zinc-200 bg-white p-4">
        <div class="grid gap-3 sm:grid-cols-4">
            <div class="relative sm:col-span-2">
                <x-icon name="search" class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-zinc-400" />
                <input name="q" value="{{ $q }}" placeholder="Cari nama/email..."
                       class="w-full rounded-lg border border-zinc-200 bg-zinc-50 py-2.5 pl-9 pr-3 text-sm focus:border-zinc-900 focus:bg-white focus:outline-none focus:ring-2 focus:ring-zinc-900/10">
            </div>
            <select name="role" class="rounded-lg border border-zinc-200 bg-white px-3 py-2.5 text-sm focus:border-zinc-900 focus:outline-none focus:ring-2 focus:ring-zinc-900/10">
                <option value="">Semua role</option>
                <option value="admin" @selected($role === 'admin')>Admin</option>
                <option value="siswa" @selected($role === 'siswa')>Siswa</option>
            </select>
            <select name="status" class="rounded-lg border border-zinc-200 bg-white px-3 py-2.5 text-sm focus:border-zinc-900 focus:outline-none focus:ring-2 focus:ring-zinc-900/10">
                <option value="">Semua status</option>
                <option value="pending" @selected($status === 'pending')>Pending</option>
                <option value="active" @selected($status === 'active')>Active</option>
                <option value="rejected" @selected($status === 'rejected')>Rejected</option>
            </select>
        </div>
        <div class="mt-3 flex gap-2">
            <button class="rounded-lg bg-zinc-900 px-4 py-2 text-sm font-medium text-white transition hover:bg-zinc-800 active:scale-95">Terapkan</button>
            <a href="{{ route('admin.users.index') }}" class="rounded-lg border border-zinc-300 px-4 py-2 text-sm font-medium text-zinc-700 hover:bg-zinc-50">Reset</a>
        </div>
    </form>

    <div class="overflow-hidden rounded-2xl border border-zinc-200 bg-white">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-zinc-50 text-left text-xs font-semibold uppercase tracking-wider text-zinc-500">
                    <tr>
                        <th scope="col" class="px-5 py-3">Pengguna</th>
                        <th scope="col" class="px-5 py-3">Role</th>
                        <th scope="col" class="px-5 py-3">Status</th>
                        <th scope="col" class="px-5 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-100">
                    @forelse ($users as $u)
                        <tr class="hover:bg-zinc-50">
                            <td class="px-5 py-3">
                                <div class="flex items-center gap-3">
                                    <div class="flex h-9 w-9 items-center justify-center rounded-full bg-zinc-900 text-sm font-semibold text-white">
                                        {{ mb_substr($u->name, 0, 1) }}
                                    </div>
                                    <div>
                                        <p class="font-medium text-zinc-900">{{ $u->name }}</p>
                                        <p class="text-xs text-zinc-500">{{ $u->email }}@if($u->school_class) · {{ $u->school_class }}@endif</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-3 capitalize text-zinc-700">{{ $u->role }}</td>
                            <td class="px-5 py-3">
                                <x-badge :variant="match($u->status){'active'=>'success','pending'=>'warning','rejected'=>'danger', default=>'neutral'}">
                                    {{ $u->status }}
                                </x-badge>
                            </td>
                            <td class="px-5 py-3 text-right">
                                <details class="inline-block">
                                    <summary class="inline-flex cursor-pointer items-center gap-1.5 rounded-lg border border-zinc-300 px-3 py-1.5 text-xs font-medium text-zinc-700 hover:bg-zinc-50">
                                        Kelola <x-icon name="arrow-right" class="h-3 w-3" />
                                    </summary>
                                    <div class="absolute right-5 z-10 mt-2 w-80 rounded-2xl border border-zinc-200 bg-white p-4 text-left shadow-lg">
                                        <form method="POST" action="{{ route('admin.users.update', $u) }}" class="space-y-3">
                                            @csrf @method('PATCH')
                                            <div>
                                                <label class="text-xs">Nama Lengkap</label>
                                                <input name="name" value="{{ $u->name }}" required
                                                       class="mt-1 w-full rounded-md border border-zinc-200 px-2 py-1.5 text-xs">
                                            </div>
                                            <div class="grid grid-cols-2 gap-2">
                                                <div>
                                                    <label class="text-xs">Kelas</label>
                                                    <input name="school_class" value="{{ $u->school_class }}" placeholder="XII RPL 1"
                                                           class="mt-1 w-full rounded-md border border-zinc-200 px-2 py-1.5 text-xs">
                                                </div>
                                                <div>
                                                    <label class="text-xs">Tahun Masuk</label>
                                                    <input name="tahun_masuk" type="number" min="2000" max="2099" value="{{ $u->tahun_masuk }}" placeholder="2024"
                                                           class="mt-1 w-full rounded-md border border-zinc-200 px-2 py-1.5 text-xs">
                                                </div>
                                            </div>
                                            <div>
                                                <label class="text-xs">No. Telepon</label>
                                                <input name="phone" value="{{ $u->phone }}" placeholder="08..."
                                                       class="mt-1 w-full rounded-md border border-zinc-200 px-2 py-1.5 text-xs">
                                            </div>
                                            <div class="grid grid-cols-2 gap-2">
                                                <div>
                                                    <label class="text-xs">Role</label>
                                                    <select name="role" class="mt-1 w-full rounded-md border border-zinc-200 px-2 py-1.5 text-xs">
                                                        <option value="admin" @selected($u->role === 'admin')>admin</option>
                                                        <option value="siswa" @selected($u->role === 'siswa')>siswa</option>
                                                    </select>
                                                </div>
                                                <div>
                                                    <label class="text-xs">Status</label>
                                                    <select name="status" class="mt-1 w-full rounded-md border border-zinc-200 px-2 py-1.5 text-xs">
                                                        <option value="pending" @selected($u->status === 'pending')>pending</option>
                                                        <option value="active" @selected($u->status === 'active')>active</option>
                                                        <option value="rejected" @selected($u->status === 'rejected')>rejected</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <button class="w-full rounded-md bg-zinc-900 py-1.5 text-xs font-medium text-white">Simpan</button>
                                        </form>
                                        @if ($u->id !== auth()->id())
                                            <form method="POST" action="{{ route('admin.users.destroy', $u) }}" class="mt-2 border-t border-zinc-100 pt-3" onsubmit="return confirm('Hapus user ini?')">
                                                @csrf @method('DELETE')
                                                <button class="text-xs font-medium text-red-600 hover:underline">Hapus permanen</button>
                                            </form>
                                        @endif
                                    </div>
                                </details>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="px-5 py-12 text-center text-sm text-zinc-400">Tidak ada pengguna.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-5">{{ $users->links() }}</div>
@endsection
