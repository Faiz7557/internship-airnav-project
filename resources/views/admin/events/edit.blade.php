@extends('layouts.admin')

@section('content')
<div class="max-w-4xl mx-auto animate-enter pt-10 pb-20">
    
    <!-- Header Section -->
    <div class="flex flex-col md:flex-row justify-between items-center mb-12 gap-6">
        <div class="relative">
            <h1 class="text-4xl font-black text-transparent bg-clip-text bg-gradient-to-r from-[#1F3C88] to-blue-500 font-outfit tracking-tight drop-shadow-sm">Edit Event</h1>
            <div class="h-1 w-20 bg-blue-500 rounded-full mt-2"></div>
            <p class="text-slate-500 font-medium mt-3 text-lg">Perbarui detail event dengan presisi.</p>
        </div>
        <a href="{{ route('admin.events.index') }}" class="group flex items-center gap-3 px-6 py-3 bg-white/40 backdrop-blur-md border border-white/60 rounded-full shadow-lg hover:shadow-blue-500/10 hover:bg-white/80 transition-all duration-300 transform hover:-translate-y-1">
            <div class="w-8 h-8 rounded-full bg-[#1F3C88] text-white flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
            </div>
            <span class="text-[#1F3C88] font-bold text-sm tracking-wide">KEMBALI KE LIST</span>
        </a>
    </div>

    <!-- Futuristic Form Card -->
    <div class="relative group">
        <!-- Glowing gradient border effect -->
        <div class="absolute -inset-1 bg-gradient-to-r from-blue-600 to-indigo-600 rounded-[2.5rem] blur opacity-20 group-hover:opacity-40 transition duration-1000 group-hover:duration-200"></div>
        
        <div class="relative glass-card rounded-[2.5rem] p-10 md:p-14 border border-white/80 shadow-2xl overflow-hidden bg-white/60 backdrop-blur-2xl">
            
            <!-- Background Elements -->
            <div class="absolute top-0 right-0 w-[500px] h-[500px] bg-gradient-to-br from-blue-400/10 to-transparent rounded-full blur-3xl -z-10 translate-x-1/3 -translate-y-1/3"></div>
            <div class="absolute bottom-0 left-0 w-[500px] h-[500px] bg-gradient-to-tr from-indigo-400/10 to-transparent rounded-full blur-3xl -z-10 -translate-x-1/3 translate-y-1/3"></div>

            <form action="{{ route('admin.events.update', $event->id) }}" method="POST" class="space-y-10">
                @csrf
                @method('PUT')
                
                <!-- Event Name Input -->
                <div class="space-y-4">
                    <label for="name" class="text-xs font-black text-slate-400 uppercase tracking-[0.2em] ml-2">Nama Event</label>
                    <div class="relative group/input">
                        <div class="absolute inset-y-0 left-0 pl-5 flex items-center pointer-events-none z-10">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-slate-400 group-focus-within/input:text-blue-600 transition-colors duration-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <input type="text" id="name" name="name" 
                            class="block w-full pl-14 pr-6 py-5 bg-white/50 border-2 border-slate-100 rounded-2xl text-lg font-bold text-slate-700 placeholder-slate-300 focus:outline-none focus:border-blue-500/50 focus:ring-4 focus:ring-blue-500/10 transition-all duration-300 backdrop-blur-sm" 
                            placeholder="Contoh: Angkutan Lebaran 2024" value="{{ old('name', $event->name) }}" required>
                    </div>
                    @error('name') <p class="text-rose-500 text-sm font-bold flex items-center gap-2 ml-2 animate-pulse"><svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" /></svg>{{ $message }}</p> @enderror
                </div>

                <!-- Date Range Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="space-y-4">
                        <label for="start_date" class="text-xs font-black text-slate-400 uppercase tracking-[0.2em] ml-2">Mulai</label>
                        <div class="relative group/input">
                            <div class="absolute inset-y-0 left-0 pl-5 flex items-center pointer-events-none z-10">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-slate-400 group-focus-within/input:text-blue-600 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <input type="date" id="start_date" name="start_date" 
                                class="block w-full pl-14 pr-6 py-5 bg-white/50 border-2 border-slate-100 rounded-2xl text-lg font-bold text-slate-700 focus:outline-none focus:border-blue-500/50 focus:ring-4 focus:ring-blue-500/10 transition-all duration-300" 
                                value="{{ old('start_date', $event->start_date->format('Y-m-d')) }}" required>
                        </div>
                        @error('start_date') <p class="text-rose-500 text-sm font-bold ml-2">{{ $message }}</p> @enderror
                    </div>
                    <div class="space-y-4">
                        <label for="end_date" class="text-xs font-black text-slate-400 uppercase tracking-[0.2em] ml-2">Selesai</label>
                        <div class="relative group/input">
                            <div class="absolute inset-y-0 left-0 pl-5 flex items-center pointer-events-none z-10">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-slate-400 group-focus-within/input:text-blue-600 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <input type="date" id="end_date" name="end_date" 
                                class="block w-full pl-14 pr-6 py-5 bg-white/50 border-2 border-slate-100 rounded-2xl text-lg font-bold text-slate-700 focus:outline-none focus:border-blue-500/50 focus:ring-4 focus:ring-blue-500/10 transition-all duration-300" 
                                value="{{ old('end_date', $event->end_date->format('Y-m-d')) }}" required>
                        </div>
                        @error('end_date') <p class="text-rose-500 text-sm font-bold ml-2">{{ $message }}</p> @enderror
                    </div>
                </div>

                <!-- Color Picker -->
                <div class="space-y-4">
                    <label for="color" class="text-xs font-black text-slate-400 uppercase tracking-[0.2em] ml-2">Identitas Warna</label>
                    <div class="p-2 bg-white/60 rounded-[1.5rem] border-2 border-slate-100 flex items-center gap-6 shadow-sm hover:shadow-md transition-all duration-300 group/color focus-within:border-blue-300 focus-within:ring-4 focus-within:ring-blue-500/10">
                        <div class="relative w-20 h-20 flex-shrink-0">
                            <input type="color" id="color_picker" 
                                class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-20" 
                                onchange="document.getElementById('color').value = this.value; document.getElementById('color_preview').style.backgroundColor = this.value" 
                                value="{{ old('color', $event->color) }}">
                            <div id="color_preview" class="w-full h-full rounded-2xl shadow-inner border border-black/5 transition-transform duration-300 group-hover/color:scale-105" style="background-color: {{ old('color', $event->color) }}"></div>
                            <div class="absolute inset-0 flex items-center justify-center pointer-events-none">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white drop-shadow-md opacity-0 group-hover/color:opacity-80 transition-opacity" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg>
                            </div>
                        </div>
                        <div class="flex-grow">
                            <div class="relative">
                                <span class="absolute left-0 top-1/2 -translate-y-1/2 text-slate-400 font-mono text-xl pl-2">#</span>
                                <input type="text" id="color" name="color" 
                                    class="w-full bg-transparent border-none focus:ring-0 p-0 pl-6 text-3xl font-black text-slate-700 uppercase tracking-widest" 
                                    value="{{ old('color', $event->color) }}" 
                                    oninput="document.getElementById('color_picker').value = this.value; document.getElementById('color_preview').style.backgroundColor = this.value"
                                    required pattern="^#+([a-fA-F0-9]{6}|[a-fA-F0-9]{3})$">
                            </div>
                            <p class="text-xs text-slate-400 font-semibold mt-1">Klik kotak warna untuk mengubah</p>
                        </div>
                    </div>
                    @error('color') <p class="text-rose-500 text-sm font-bold ml-2">{{ $message }}</p> @enderror
                </div>

                <!-- Description -->
                <div class="space-y-4">
                    <label for="description" class="text-xs font-black text-slate-400 uppercase tracking-[0.2em] ml-2">Catatan Tambahan</label>
                    <div class="relative">
                        <textarea id="description" name="description" rows="4" 
                            class="block w-full px-6 py-5 bg-white/50 border-2 border-slate-100 rounded-2xl text-lg font-medium text-slate-600 placeholder-slate-300 focus:outline-none focus:border-blue-500/50 focus:ring-4 focus:ring-blue-500/10 transition-all duration-300 resize-none layout-grid" 
                            placeholder="Tuliskan detail event di sini...">{{ old('description', $event->description) }}</textarea>
                        <div class="absolute bottom-4 right-4 pointer-events-none">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex items-center justify-end gap-6 pt-8 border-t border-slate-200/50">
                    <a href="{{ route('admin.events.index') }}" class="px-8 py-4 text-slate-500 font-bold hover:text-rose-500 hover:bg-rose-50 rounded-2xl transition-all duration-300">
                        BATALKAN
                    </a>
                    <button type="submit" class="relative overflow-hidden group px-10 py-4 bg-[#1F3C88] rounded-2xl shadow-xl shadow-blue-900/20 hover:shadow-blue-900/40 transform hover:-translate-y-1 transition-all duration-300">
                        <div class="absolute inset-0 w-full h-full bg-gradient-to-r from-transparent via-white/20 to-transparent -translate-x-full group-hover:translate-x-full transition-transform duration-700 ease-in-out"></div>
                        <span class="relative text-white font-black tracking-widest flex items-center gap-3">
                            SIMPAN PERUBAHAN
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .animate-enter { animation: fadeInUp 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards; opacity: 0; transform: translateY(40px); }
    @keyframes fadeInUp { to { opacity: 1; transform: translateY(0); } }
</style>
@endsection
