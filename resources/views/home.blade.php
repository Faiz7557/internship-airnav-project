<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AirNav Dashboard Monitor</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
        }

        .banner-slide {
            transition: opacity 1.5s ease-in-out;
        }

        @keyframes scroll-cards {
            0% { transform: translateX(0); }
            100% { transform: translateX(-50%); }
        }

        .animate-scroll {
            animation: scroll-cards 30s linear infinite;
            width: max-content;
        }

        .animate-scroll:hover {
            animation-play-state: paused;
        }
    </style>
</head>
<body class="bg-white m-0 p-0 overflow-x-hidden relative overflow-y-scroll">
    <nav class="absolute top-0 inset-x-0 px-6 md:px-12 py-6 flex justify-between items-center z-50">
        <div class="flex items-center gap-3">
            <img src="{{ asset('img/logo_airnav.png') }}"
                 alt="AirNav Logo"
                 class="h-10 md:h-12 object-contain">
            
            <span class="text-white font-bold text-xl hidden md:block drop-shadow-md tracking-wide">
                AirNav Indonesia
            </span>
        </div>

        <div class="flex items-center gap-2 font-medium text-white mr-[-15px]">
            <a href="#" class="flex items-center gap-2 bg-white/20 backdrop-blur-md text-white px-5 py-2.5 rounded-full transition shadow-sm border border-white/10">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-5 h-5">
                    <path d="M10 12.5a2.5 2.5 0 100-5 2.5 2.5 0 000 5z" />
                    <path fill-rule="evenodd" d="M.664 10.59a1.651 1.651 0 010-1.186A10.004 10.004 0 0110 3c4.257 0 8.2 1.966 9.336 6.41.147.381.146.804 0 1.186A10.004 10.004 0 0110 17c-4.257 0-8.2-1.966-9.336-6.41zM10 15a5 5 0 100-10 5 5 0 000 10z" clip-rule="evenodd" />
                </svg>
                Home
            </a>

            <a href="{{ route('upload') }}" class="flex items-center gap-2 px-5 py-2.5 rounded-full transition duration-300 hover:bg-white/15 hover:backdrop-blur-sm">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-5 h-5">
                    <path fill-rule="evenodd" d="M5.5 17a4.5 4.5 0 01-1.44-8.765 4.5 4.5 0 018.302-3.046 3.5 3.5 0 014.504 4.272A4 4 0 0115 17H5.5zm3.75-2.75a.75.75 0 001.5 0V9.66l1.95 2.1a.75.75 0 101.1-1.02l-3.25-3.5a.75.75 0 00-1.1 0l-3.25 3.5a.75.75 0 101.1 1.02l1.95-2.1v4.59z" clip-rule="evenodd" />
                </svg>
                Upload
            </a>
                        
            <a href="{{ route('summary') }}" class="flex items-center gap-2 px-5 py-2.5 rounded-full transition duration-300 hover:bg-white/15 hover:backdrop-blur-sm">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-5 h-5">
                    <path fill-rule="evenodd" d="M4.5 2A1.5 1.5 0 003 3.5v13A1.5 1.5 0 004.5 18h11a1.5 1.5 0 001.5-1.5V7.621a1.5 1.5 0 00-.44-1.06l-4.12-4.122A1.5 1.5 0 0011.378 2H4.5zm2.25 8.5a.75.75 0 000 1.5h6.5a.75.75 0 000-1.5h-6.5zm0 3a.75.75 0 000 1.5h6.5a.75.75 0 000-1.5h-6.5z" clip-rule="evenodd" />
                </svg>
                Summary
            </a>
            
            <a href="#" class="flex items-center gap-2 px-5 py-2.5 rounded-full transition duration-300 hover:bg-white/15 hover:backdrop-blur-sm">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-5 h-5">
                    <path fill-rule="evenodd" d="M4.25 2A2.25 2.25 0 002 4.25v2.5A2.25 2.25 0 004.25 9h2.5A2.25 2.25 0 009 6.75v-2.5A2.25 2.25 0 006.75 2h-2.5zm0 9A2.25 2.25 0 002 13.25v2.5A2.25 2.25 0 004.25 18h2.5A2.25 2.25 0 009 15.75v-2.5A2.25 2.25 0 006.75 11h-2.5zm9-9A2.25 2.25 0 0011 4.25v2.5A2.25 2.25 0 0013.25 9h2.5A2.25 2.25 0 0018 6.75v-2.5A2.25 2.25 0 0015.75 2h-2.5zm0 9A2.25 2.25 0 0011 13.25v2.5A2.25 2.25 0 0013.25 18h2.5A2.25 2.25 0 0018 15.75v-2.5A2.25 2.25 0 0015.75 11h-2.5z" clip-rule="evenodd" />
                </svg>
                Dashboard
            </a>
        </div>
    </nav>

    <header class="relative w-full h-[550px] md:h-[760px] group overflow-hidden bg-gray-900 z-0">
        <img src="{{ asset('img/banner1.png') }}" class="banner-slide absolute inset-0 w-full h-full object-cover object-center opacity-100" id="slide-1">
        <img src="{{ asset('img/banner2.png') }}" class="banner-slide absolute inset-0 w-full h-full object-cover object-center opacity-0" id="slide-2">
        
        <div class="absolute inset-0 bg-gradient-to-t from-[#1F3C88]/90 via-[#1F3C88]/30 to-black/30"></div>

        <div class="absolute inset-0 flex flex-col items-center justify-center z-10 px-4 text-center pb-20 pt-20">
            <h1 class="text-4xl md:text-6xl font-bold text-white drop-shadow-xl leading-tight max-w-4xl">
                Ringkasan Operasional<br>Lalu Lintas Penerbangan
            </h1>
            <p class="text-blue-100 mt-4 text-lg max-w-2xl">
                Data pergerakan pesawat, analisis kapasitas runway, dan tren penumpang bulanan.
            </p>
        </div>
    </header>

    <section class="w-full bg-white rounded-t-[3rem] -mt-24 relative z-10 px-6 md:px-12 py-14">
        <div class="max-w-7xl mx-auto">
            <div class="flex flex-col md:flex-row md:items-center justify-between mb-10 gap-4">
                <div>
                    <h2 class="text-3xl font-bold text-[#1F3C88]">Penerbangan {{ $nama_bulan }} - {{ $selectedCabang }}</h2>
                    <p class="text-slate-400 mt-1">Ringkasan statistik berdasarkan data bulan terakhir.</p>
                </div>
                
                <div class="flex items-center gap-4">
                    
                    @php
                        $activeCabang = $cabangs->where('kode_cabang', $selectedCabang)->first();
                        $activeNama = $activeCabang ? $activeCabang->nama : 'Cabang';
                    @endphp
                    <div class="relative" id="customDropdownContainer">
                        <button type="button" onclick="toggleCustomDropdown()" class="flex items-center justify-between gap-3 w-full sm:w-auto bg-white border-2 border-blue-100 text-[#1F3C88] text-sm font-bold rounded-xl focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 py-2.5 pl-4 pr-3 shadow-sm hover:border-blue-300 hover:shadow-md transition-all outline-none">
                            <div class="flex items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-blue-600" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h8a2 2 0 012 2v12a1 1 0 110 2h-3a1 1 0 01-1-1v-2a1 1 0 00-1-1H9a1 1 0 00-1 1v2a1 1 0 01-1 1H4a1 1 0 110-2V4zm3 1h2v2H7V5zm2 4H7v2h2V9zm2-4h2v2h-2V5zm2 4h-2v2h2V9z" clip-rule="evenodd" />
                                </svg>
                                <span>{{ $selectedCabang }} - {{ $activeNama }}</span>
                            </div>
                            <svg class="h-4 w-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"></path></svg>
                        </button>

                        <div id="customDropdownMenu" class="hidden absolute right-0 mt-2 w-60 origin-top-right rounded-xl bg-white shadow-xl ring-1 ring-black ring-opacity-5 focus:outline-none z-50 overflow-hidden transform opacity-0 scale-95 transition-all duration-200">
                            <div class="py-1">
                                @foreach($cabangs as $cabang)
                                    <button type="button" onclick="window.location.href='?kode_cabang={{ $cabang->kode_cabang }}'" class="w-full text-left flex items-center px-4 py-2.5 text-sm font-semibold transition-colors {{ $selectedCabang == $cabang->kode_cabang ? 'bg-blue-50 text-[#1F3C88] border-l-4 border-[#1F3C88]' : 'text-slate-600 hover:bg-slate-50 hover:text-[#1F3C88] border-l-4 border-transparent' }}">
                                        {{ $cabang->kode_cabang }} - {{ $cabang->nama }}
                                    </button>
                                @endforeach
                                
                                <div class="border-t border-slate-100 my-1"></div>
                                
                                <button type="button" onclick="openManageCabang()" class="w-full text-left flex items-center gap-2 px-4 py-3 text-sm font-extrabold text-blue-600 hover:bg-blue-50 transition-colors">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M11.49 3.17c-.38-1.56-2.6-1.56-2.98 0a1.532 1.532 0 01-2.286.948c-1.372-.836-2.942.734-2.106 2.106.54.886.061 2.042-.947 2.287-1.561.379-1.561 2.6 0 2.978a1.532 1.532 0 01.947 2.287c-.836 1.372.734 2.942 2.106 2.106a1.532 1.532 0 012.287.947c.379 1.561 2.6 1.561 2.978 0a1.533 1.533 0 012.287-.947c1.372.836 2.942-.734 2.106-2.106a1.533 1.533 0 01.947-2.287c1.561-.379 1.561-2.6 0-2.978a1.532 1.532 0 01-.947-2.287c.836-1.372-.734-2.942-2.106-2.106a1.532 1.532 0 01-2.287-.947zM10 13a3 3 0 100-6 3 3 0 000 6z" clip-rule="evenodd" /></svg>
                                    Edit Data Cabang...
                                </button>
                            </div>
                        </div>
                    </div>

                    <a href="{{ route('summary') }}?branch_code={{ $selectedCabang }}" class="text-sm font-semibold text-[#1F3C88] hover:underline transition whitespace-nowrap">
                        Lihat Analisis Lengkap →
                    </a>
                </div>
            </div>

            <div class="overflow-hidden w-full py-4">
                <div class="flex gap-6 animate-scroll">
                    
                    @for ($i = 0; $i < 2; $i++)
                        <div class="w-[320px] shrink-0 bg-[#1F3C88] text-white rounded-3xl p-8 shadow-xl hover:-translate-y-2 transition duration-300 relative overflow-hidden group cursor-pointer">
                            <div class="absolute -right-6 -top-6 bg-white/10 w-32 h-32 rounded-full blur-3xl group-hover:bg-white/20 transition"></div>
                            <div class="flex items-center gap-3 mb-4">
                                <div class="p-2 bg-white/10 rounded-lg">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-200" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" /></svg>
                                </div>
                                <h3 class="text-blue-200 font-medium text-xs uppercase tracking-wider">Total Pergerakan</h3>
                            </div>
                            <div class="flex items-end gap-3">
                                <span class="text-5xl font-bold">{{ $total }}</span>
                                <div class="mb-1 flex items-center text-sm font-medium {{ $growth >= 0 ? 'text-green-400' : 'text-red-400' }}">
                                    @if($growth >= 0) <span>▲ {{ $growth }}%</span> @else <span>▼ {{ abs($growth) }}%</span> @endif
                                </div>
                            </div>
                            <p class="text-xs text-blue-200 opacity-70 mt-1">vs bulan lalu</p>
                        </div>

                        <div class="w-[320px] shrink-0 bg-[#1F3C88] text-white rounded-3xl p-8 shadow-xl hover:-translate-y-2 transition duration-300 relative overflow-hidden group cursor-pointer">
                            <h3 class="text-blue-200 font-medium text-xs uppercase tracking-wider mb-6 flex items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z" /></svg>
                                Komposisi Traffic
                            </h3>
                            <div class="flex justify-between items-end mb-3">
                                <div><div class="text-3xl font-bold">{{ $dom_pct }}%</div><div class="text-xs text-blue-200 mt-1">Domestik</div></div>
                                <div class="text-right"><div class="text-3xl font-bold text-orange-400">{{ $int_pct }}%</div><div class="text-xs text-orange-200 mt-1">Internasional</div></div>
                            </div>
                            <div class="w-full h-2 bg-blue-900/50 rounded-full overflow-hidden flex">
                                <div class="h-full bg-white shadow-[0_0_10px_rgba(255,255,255,0.5)]" style="width: {{ $dom_pct }}%"></div>
                                <div class="h-full bg-orange-400" style="width: {{ $int_pct }}%"></div>
                            </div>
                        </div>

                        <div class="w-[320px] shrink-0 bg-[#1F3C88] text-white rounded-3xl p-8 shadow-xl hover:-translate-y-2 transition duration-300 relative overflow-hidden group cursor-pointer">
                            <div class="absolute -left-6 -bottom-6 bg-red-500/20 w-32 h-32 rounded-full blur-3xl group-hover:bg-red-500/30 transition"></div>
                            <div class="flex items-center gap-3 mb-4">
                                <div class="p-2 bg-red-500/20 rounded-lg">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-red-200" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                </div>
                                <h3 class="text-blue-200 font-medium text-xs uppercase tracking-wider">Jam Sibuk (Peak)</h3>
                            </div>
                            <div class="flex items-baseline gap-2">
                                <span class="text-5xl font-bold text-red-400">{{ $jam_sibuk }}</span>
                                <span class="text-base font-light opacity-80">UTC</span>
                            </div>
                        </div>

                        <div class="w-[320px] shrink-0 bg-[#1F3C88] text-white rounded-3xl p-8 shadow-xl hover:-translate-y-2 transition duration-300 relative overflow-hidden group cursor-pointer">
                            <div class="absolute top-0 right-0 bg-green-500/10 w-full h-full blur-md"></div>
                            <div class="flex items-center gap-3 mb-4 relative z-10">
                                <div class="p-2 bg-green-500/20 rounded-lg">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-200" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                </div>
                                <h3 class="text-blue-200 font-medium text-xs uppercase tracking-wider">Status Runway</h3>
                            </div>
                            <div class="relative z-10">
                                <span class="text-4xl font-bold {{ $runway_color }}">{{ $runway_status }}</span>
                                <p class="text-xs text-blue-200 mt-2 opacity-80">Load: {{ $stress_level }}% dari Kapasitas.</p>
                            </div>
                        </div>

                        <div class="w-[320px] shrink-0 bg-[#1F3C88] text-white rounded-3xl p-8 shadow-xl hover:-translate-y-2 transition duration-300 relative overflow-hidden group cursor-pointer">
                            <div class="absolute -right-6 -bottom-6 bg-teal-500/20 w-32 h-32 rounded-full blur-3xl group-hover:bg-teal-500/30 transition"></div>
                            <div class="flex items-center gap-3 mb-4">
                                <div class="p-2 bg-teal-500/20 rounded-lg relative z-10">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-teal-200" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" /></svg>
                                </div>
                                <h3 class="text-blue-200 font-medium text-xs uppercase tracking-wider relative z-10">Rata-rata Harian</h3>
                            </div>
                            <div class="relative z-10 flex items-baseline gap-2">
                                <span class="text-5xl font-bold text-teal-400">{{ $rata_harian ?? 0 }}</span>
                                <span class="text-sm font-light opacity-80">Flight / Hari</span>
                            </div>
                        </div>

                        <div class="w-[320px] shrink-0 bg-[#1F3C88] text-white rounded-3xl p-8 shadow-xl hover:-translate-y-2 transition duration-300 relative overflow-hidden group cursor-pointer">
                            <div class="absolute -left-6 -top-6 bg-amber-500/20 w-32 h-32 rounded-full blur-3xl group-hover:bg-amber-500/30 transition"></div>
                            <div class="flex items-center gap-3 mb-4">
                                <div class="p-2 bg-amber-500/20 rounded-lg relative z-10">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-amber-200" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>
                                </div>
                                <h3 class="text-blue-200 font-medium text-xs uppercase tracking-wider relative z-10">Hari Tersibuk</h3>
                            </div>
                            <div class="relative z-10 flex justify-between items-end">
                                <div>
                                    <span class="text-5xl font-bold text-amber-400">{{ $rekor_penerbangan ?? 0 }}</span>
                                    <p class="text-xs text-blue-200 mt-2 opacity-80">Penerbangan</p>
                                </div>
                                <div class="text-right pb-1">
                                    <span class="bg-amber-500 text-slate-900 font-bold px-3 py-1 rounded-full text-xs shadow-md">Tgl {{ $tanggal_rekor ?? '-' }}</span>
                                </div>
                            </div>
                        </div>

                    @endfor
                </div>
            </div>
        </div>
    </section>

    <div id="cabangModal" class="hidden fixed inset-0 z-[100] bg-gray-900/60 backdrop-blur-sm overflow-y-auto flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl w-full max-w-2xl shadow-2xl overflow-hidden flex flex-col max-h-[90vh]">
            
            <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center bg-slate-50">
                <h3 class="text-lg font-bold text-[#1F3C88] flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                    Kelola Database Cabang
                </h3>
                <button onclick="closeCabangModal()" class="text-slate-400 hover:text-red-500 transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <div class="p-6 overflow-y-auto flex-1">
                
                @if(session('success'))
                    <div class="mb-4 p-3 bg-green-50 text-green-700 rounded-lg text-sm font-medium border border-green-100">✅ {{ session('success') }}</div>
                @endif
                @if($errors->any())
                    <div class="mb-4 p-3 bg-red-50 text-red-700 rounded-lg text-sm font-medium border border-red-100">❌ {{ $errors->first() }}</div>
                @endif

                <div class="mb-6 bg-blue-50/50 p-4 rounded-xl border border-blue-100">
                    <h4 class="text-xs font-bold text-blue-800 uppercase mb-3">➕ Tambah Cabang Baru</h4>
                    <form action="{{ route('cabang.store') }}" method="POST" class="flex flex-col sm:flex-row gap-3">
                        @csrf
                        <input type="text" name="kode_cabang" placeholder="Kode ICAO (Cth: WADD)" required class="w-full sm:w-1/3 border border-slate-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none uppercase">
                        <input type="text" name="nama" placeholder="Nama Kota (Cth: Bali)" required class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                        <button type="submit" class="bg-[#1F3C88] hover:bg-blue-800 text-white font-semibold rounded-lg px-5 py-2 text-sm transition shrink-0 shadow-sm">Simpan</button>
                    </form>
                </div>

                <h4 class="text-xs font-bold text-slate-500 uppercase mb-3">✏️ Edit Cabang Tersedia</h4>
                <div class="space-y-3">
                    @foreach($cabangs as $cabang)
                        <form action="{{ route('cabang.update', $cabang->id) }}" method="POST" class="flex flex-col sm:flex-row items-center gap-3 bg-white p-3 rounded-xl border border-slate-200 hover:border-blue-300 transition-colors shadow-sm group">
                            @csrf
                            @method('PUT')
                            <div class="w-full sm:w-1/3 relative">
                                <label class="text-[10px] text-slate-400 font-bold absolute -top-2 left-2 bg-white px-1">ICAO</label>
                                <input type="text" name="kode_cabang" value="{{ $cabang->kode_cabang }}" required class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm font-bold text-[#1F3C88] uppercase focus:ring-2 focus:ring-blue-500 outline-none">
                            </div>
                            <div class="w-full relative">
                                <label class="text-[10px] text-slate-400 font-bold absolute -top-2 left-2 bg-white px-1">Nama Kota</label>
                                <input type="text" name="nama" value="{{ $cabang->nama }}" required class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                            </div>
                            <button type="submit" class="w-full sm:w-auto bg-green-50 text-green-700 hover:bg-green-600 hover:text-white border border-green-200 font-semibold rounded-lg px-4 py-2 text-sm transition shrink-0 opacity-100 sm:opacity-0 sm:group-hover:opacity-100">Update</button>
                        </form>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <script>
        function toggleCustomDropdown() {
            const menu = document.getElementById('customDropdownMenu');
            if (menu.classList.contains('hidden')) {
                menu.classList.remove('hidden');
                setTimeout(() => {
                    menu.classList.remove('opacity-0', 'scale-95');
                    menu.classList.add('opacity-100', 'scale-100');
                }, 10);
            } else {
                menu.classList.remove('opacity-100', 'scale-100');
                menu.classList.add('opacity-0', 'scale-95');
                setTimeout(() => menu.classList.add('hidden'), 200);
            }
        }

        function openManageCabang() {
            const menu = document.getElementById('customDropdownMenu');
            menu.classList.remove('opacity-100', 'scale-100');
            menu.classList.add('opacity-0', 'scale-95');
            setTimeout(() => menu.classList.add('hidden'), 200);
            
            document.getElementById('cabangModal').classList.remove('hidden');
        }

        function closeCabangModal() {
            document.getElementById('cabangModal').classList.add('hidden');
        }

        document.addEventListener('click', function(event) {
            const dropdown = document.getElementById('customDropdownContainer');
            const menu = document.getElementById('customDropdownMenu');
            if (dropdown && !dropdown.contains(event.target)) {
                if (!menu.classList.contains('hidden')) {
                    menu.classList.remove('opacity-100', 'scale-100');
                    menu.classList.add('opacity-0', 'scale-95');
                    setTimeout(() => menu.classList.add('hidden'), 200);
                }
            }
        });

        @if(session('success') || $errors->any())
            document.addEventListener('DOMContentLoaded', function() {
                document.getElementById('cabangModal').classList.remove('hidden');
            });
        @endif

        function closeCabangModal() {
            document.getElementById('cabangModal').classList.add('hidden');
        }

        @if(session('success') || $errors->any())
            document.addEventListener('DOMContentLoaded', function() {
                document.getElementById('cabangModal').classList.remove('hidden');
            });
        @endif
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const slide1 = document.getElementById('slide-1');
            const slide2 = document.getElementById('slide-2');
            let active = 1;
            setInterval(() => {
                if (active === 1) {
                    slide1.classList.replace('opacity-100', 'opacity-0');
                    slide2.classList.replace('opacity-0', 'opacity-100');
                    active = 2;
                } else {
                    slide2.classList.replace('opacity-100', 'opacity-0');
                    slide1.classList.replace('opacity-0', 'opacity-100');
                    active = 1;
                }
            }, 5000);
        });
    </script>
</body>
</html>
