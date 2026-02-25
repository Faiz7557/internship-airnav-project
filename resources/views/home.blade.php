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
            
            <a href="{{ route('dashboard') }}" class="flex items-center gap-2 px-5 py-2.5 rounded-full transition duration-300 hover:bg-white/15 hover:backdrop-blur-sm">
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
            <div class="flex items-center justify-between mb-10">
                <div>
                    <h2 class="text-3xl font-bold text-[#1F3C88]">Penerbangan {{ $nama_bulan }}</h2>
                    <p class="text-slate-400 mt-1">Ringkasan statistik berdasarkan data bulan terakhir.</p>
                </div>
                <button class="text-sm font-semibold text-[#1F3C88] hover:underline transition">
                    Lihat Analisis Lengkap →
                </button>
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