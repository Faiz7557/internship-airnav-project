@extends('layouts.admin')

@section('content')
<div class="max-w-6xl mx-auto animate-enter">
    
    <!-- Header -->
    <div class="flex flex-col md:flex-row justify-between items-end mb-8 gap-4">
        <div>
            <h1 class="text-3xl font-extrabold text-[#1F3C88] font-outfit tracking-tight">Manajemen Event</h1>
            <p class="text-slate-500 font-medium mt-1">Kelola hari libur dan event penting untuk ditampilkan di dashboard.</p>
        </div>
        <a href="{{ route('admin.events.create') }}" class="group relative px-6 py-3 bg-[#1F3C88] text-white font-bold rounded-xl shadow-lg shadow-blue-900/20 hover:bg-blue-800 transition-all hover:-translate-y-1 overflow-hidden">
            <span class="relative z-10 flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" /></svg>
                Tambah Event
            </span>
            <div class="absolute inset-0 bg-white/20 translate-y-full group-hover:translate-y-0 transition-transform duration-300"></div>
        </a>
    </div>

    @if(session('success'))
        <div class="mb-6 bg-emerald-50 text-emerald-700 px-4 py-3 rounded-xl border border-emerald-100 flex items-center gap-2 shadow-sm animate-enter" style="animation-delay: 0.1s">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" /></svg>
            <span class="font-bold">{{ session('success') }}</span>
        </div>
    @endif

    <!-- Content Card -->
    <div class="glass-card rounded-3xl p-1 overflow-hidden animate-enter" style="animation-delay: 0.2s">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-indigo-50/50 text-indigo-900 border-b border-indigo-100/50">
                        <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider">Nama Event</th>
                        <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider">Tanggal Mulai</th>
                        <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider">Tanggal Selesai</th>
                        <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider">Warna</th>
                        <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($events as $event)
                        <tr class="group hover:bg-white/60 transition-colors duration-200">
                            <td class="px-6 py-5">
                                <div class="font-bold text-slate-700 text-sm group-hover:text-[#1F3C88] transition-colors font-outfit">{{ $event->name }}</div>
                                <div class="text-xs text-slate-400 mt-1 truncate max-w-[200px]">{{ $event->description ?? '-' }}</div>
                            </td>
                            <td class="px-6 py-5">
                                <span class="text-sm font-semibold text-slate-600 font-outfit">{{ \Carbon\Carbon::parse($event->start_date)->format('d M Y') }}</span>
                            </td>
                            <td class="px-6 py-5">
                                <span class="text-sm font-semibold text-slate-600 font-outfit">{{ \Carbon\Carbon::parse($event->end_date)->format('d M Y') }}</span>
                            </td>
                            <td class="px-6 py-5">
                                <div class="flex items-center gap-3">
                                    <div class="w-6 h-6 rounded-lg shadow-sm border border-white ring-1 ring-slate-100" style="background-color: {{ $event->color }}"></div>
                                    <span class="text-xs font-mono text-slate-500 bg-slate-50 px-2 py-1 rounded border border-slate-100">{{ $event->color }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-5 text-right">
                                <div class="flex justify-end gap-2 opacity-80 group-hover:opacity-100 transition-opacity">
                                    <a href="{{ route('admin.events.edit', $event->id) }}" class="p-2 bg-indigo-50 text-indigo-600 rounded-lg hover:bg-indigo-100 hover:text-indigo-800 transition shadow-sm" title="Edit">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" /></svg>
                                    </a>
                                    <form action="{{ route('admin.events.destroy', $event->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus event ini?');" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-2 bg-rose-50 text-rose-600 rounded-lg hover:bg-rose-100 hover:text-rose-800 transition shadow-sm" title="Hapus">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" /></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-slate-400">
                                <div class="flex flex-col items-center gap-3">
                                    <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                                    </div>
                                    <p class="font-medium">Belum ada event yang ditambahkan.</p>
                                    <a href="{{ route('admin.events.create') }}" class="text-sm text-indigo-600 hover:text-indigo-800 font-bold hover:underline">Tambah Event Baru</a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<style>
    .animate-enter { animation: enterUp 0.6s ease-out forwards; opacity: 0; transform: translateY(20px); }
    @keyframes enterUp { to { opacity: 1; transform: translateY(0); } }
</style>
@endsection
