<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Analytics Dashboard - AirNav Indonesia</title>
    <script src="{{ asset('js/libs/tailwindcss.js') }}"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Outfit:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style type="text/tailwindcss">
        body { font-family: 'Poppins', sans-serif; overflow-x: hidden; }
        /* Fail-Safe Marker: Red Line */
        body::before {
            content: "";
            position: fixed;
            top: 0; left: 0; width: 100%; height: 5px;
            background: red; z-index: 99999; pointer-events: none;
        }

        .font-outfit { font-family: 'Outfit', sans-serif; }
        
        /* Neo-Glass & Ambient Background */
        .glass-card {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.5);
            box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.1);
        }
        .glass-nav {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.5);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        }

        .ambient-light {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: -1;
            overflow: hidden;
        }
        .orb {
            position: absolute;
            border-radius: 50%;
            filter: blur(80px);
            opacity: 0.6;
            animation: float 10s infinite ease-in-out;
        }
        @keyframes float {
            0%, 100% { transform: translateY(0) scale(1); }
            50% { transform: translateY(-20px) scale(1.05); }
        }
        .animate-float-slow { animation: float 15s infinite ease-in-out; }
        .animate-float-delayed { animation: float 12s infinite ease-in-out 5s; }
        .animate-pulse-slow { animation: pulse 8s infinite ease-in-out; }

        .animate-enter {
            animation: enterUp 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards;
            opacity: 0;
            transform: translateY(30px);
        }
        @keyframes enterUp {
            to { opacity: 1; transform: translateY(0); }
        }
        .toggle-btn {
            @apply px-4 py-1.5 text-xs font-bold rounded-full transition-all duration-300;
        }
        .toggle-btn.active {
            @apply bg-gradient-to-r from-[#1F3C88] to-blue-700 text-white shadow-lg shadow-blue-900/20 transform scale-105;
        }
        .toggle-btn.inactive {
            @apply text-slate-500 hover:text-[#1F3C88] hover:bg-white hover:shadow-sm;
        }
    </style>
</head>

<body class="bg-slate-50 min-h-screen relative overflow-x-hidden text-slate-800">

    <!-- Ambient Background -->
    <div class="ambient-light">
        <div class="orb bg-blue-300/30 w-[600px] h-[600px] top-[-100px] left-[-100px] animate-float-slow"></div>
        <div class="orb bg-purple-300/30 w-[500px] h-[500px] bottom-0 right-0 animate-float-delayed"></div>
        <div class="orb bg-amber-200/20 w-[400px] h-[400px] top-[40%] left-[30%] animate-pulse-slow"></div>
    </div>

    <!-- Navigation (Fixed Top Glass) -->
    <nav class="glass-nav sticky top-0 z-50 w-full mb-8 transition-all duration-300">
        <div class="max-w-7xl mx-auto px-6 py-4 flex justify-between items-center">
            <div class="flex items-center gap-4">
                <div class="bg-gradient-to-br from-white to-slate-50 p-2 rounded-xl shadow-md border border-slate-100">
                    <img src="{{ asset('img/logo_airnav.png') }}" class="h-8">
                </div>
                <div class="hidden md:block">
                    <h1 class="font-bold text-lg text-[#1F3C88] leading-tight font-outfit tracking-tight">AirNav Analytics</h1>
                    <p class="text-[10px] text-slate-500 font-medium tracking-wider uppercase">Dashboard Operasional</p>
                </div>
            </div>
            
            <div class="flex gap-1 md:gap-2 bg-slate-100/50 p-1.5 rounded-2xl backdrop-blur-sm border border-white/50">
                <a href="{{ route('home') }}" class="px-4 py-2 rounded-xl text-sm font-bold text-slate-500 hover:bg-white hover:text-[#1F3C88] hover:shadow-sm transition-all duration-300">Home</a>
                <a href="{{ route('upload') }}" class="px-4 py-2 rounded-xl text-sm font-bold text-slate-500 hover:bg-white hover:text-[#1F3C88] hover:shadow-sm transition-all duration-300">Upload</a>
                <a href="{{ route('summary') }}" class="px-4 py-2 rounded-xl text-sm font-bold text-slate-500 hover:bg-white hover:text-[#1F3C88] hover:shadow-sm transition-all duration-300">Summary</a>
                <a href="{{ route('dashboard') }}" class="px-4 py-2 rounded-xl text-sm font-bold bg-white text-[#1F3C88] shadow-md shadow-blue-900/5 ring-1 ring-black/5">Dashboard</a>
            </div>
        </div>
    </nav>
    
    <div class="max-w-7xl mx-auto px-6 py-2 pb-12 relative z-10">
        
        <!-- Header & Filter -->
        <div class="flex flex-col md:flex-row justify-between items-end mb-10 gap-4">
            <div>
                <h1 class="text-4xl font-extrabold text-[#1F3C88] mb-1 font-outfit tracking-tight drop-shadow-sm">Dashboard Operasional</h1>
                <p class="text-slate-500 font-medium text-base">Monitoring pergerakan pesawat & statistik harian</p>
                @if(isset($isFiltered) && $isFiltered)
                    <span class="inline-flex items-center gap-1 mt-2 px-3 py-1 rounded-lg bg-blue-50 text-blue-700 text-xs font-bold border border-blue-100 shadow-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M3 3a1 1 0 011-1h12a1 1 0 011 1v3a1 1 0 01-.293.707L12 11.414V15a1 1 0 01-.293.707l-2 2A1 1 0 018 17v-5.586L3.293 6.707A1 1 0 013 6V3z" clip-rule="evenodd" /></svg>
                        Filter: {{ date('F', mktime(0, 0, 0, $month, 10)) }} {{ $year }}
                    </span>
                @endif
            </div>

            <!-- Filter Form -->
            <form action="{{ route('dashboard') }}" method="GET" class="glass-card p-1.5 rounded-2xl flex items-center gap-2 border border-white/50 shadow-sm relative z-20">
                <div class="relative group">
                    <select name="month" class="appearance-none bg-transparent text-sm font-bold text-slate-600 focus:outline-none cursor-pointer hover:text-[#1F3C88] py-2 pl-4 pr-8 rounded-xl transition-colors">
                        <option value="">Bulan</option>
                        @foreach(range(1, 12) as $m)
                            <option value="{{ $m }}" {{ request('month') == $m ? 'selected' : '' }}>
                                {{ date('F', mktime(0, 0, 0, $m, 10)) }}
                            </option>
                        @endforeach
                    </select>
                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-slate-400">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </div>
                </div>
                
                <div class="w-px h-6 bg-slate-200"></div>
                
                <div class="relative group">
                    <select name="year" class="appearance-none bg-transparent text-sm font-bold text-slate-600 focus:outline-none cursor-pointer hover:text-[#1F3C88] py-2 pl-4 pr-8 rounded-xl transition-colors">
                        <option value="">Tahun</option>
                        @foreach($availableDates as $y => $dates)
                            <option value="{{ $y }}" {{ request('year') == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endforeach
                    </select>
                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-slate-400">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </div>
                </div>

                <button type="submit" class="bg-gradient-to-r from-[#1F3C88] to-blue-700 text-white p-2.5 rounded-xl hover:shadow-lg hover:shadow-blue-900/20 transition-all transform active:scale-95 flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                </button>
            </form>
        </div>

        <!-- 1. KPI Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            
            <!-- Card 1: Total Flights -->
            <x-kpi-card 
                title="Total Penerbangan" 
                value="{{ number_format($totalFlights) }}" 
                iconBg="bg-blue-50" iconColor="text-[#1F3C88]"
                growth="{{ $growthPercentage }}"
                growthText="vs periode lalu"
            >
                <x-slot name="icon">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z" /><path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z" /></svg>
                </x-slot>
            </x-kpi-card>

            <!-- Card 2: Highest Peak -->
            <x-kpi-card 
                title="Puncak Tertinggi" 
                value="{{ $highestPeak['count'] }}" 
                iconBg="bg-amber-50" iconColor="text-[#FDBE33]"
            >
                <x-slot name="icon">
                     <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" /></svg>
                </x-slot>
                
                <div class="mt-4 pt-3 border-t border-slate-100/50 flex justify-between items-center text-xs">
                     <div class="flex flex-col">
                        <span class="text-slate-400 text-[10px] font-bold uppercase tracking-wider">Waktu</span>
                        <span class="font-bold text-[#1F3C88] font-outfit">{{ $highestPeak['time'] }}</span>
                     </div>
                     <div class="flex flex-col text-right">
                        <span class="text-slate-400 text-[10px] font-bold uppercase tracking-wider">Tanggal</span>
                        <span class="font-bold text-slate-500">{{ \Carbon\Carbon::parse($busiestDay['date'])->format('d M Y') }}</span>
                     </div>
                </div>
            </x-kpi-card>

             <!-- Card 3: Capacity Utilization -->
             <x-kpi-card 
                title="Kesehatan Kapasitas" 
                value="{{ $capacityUtilization }}%" 
                iconBg="{{ $capacityUtilization > 80 ? 'bg-rose-50' : ($capacityUtilization > 60 ? 'bg-amber-50' : 'bg-emerald-50') }}" 
                iconColor="{{ $capacityUtilization > 80 ? 'text-rose-500' : ($capacityUtilization > 60 ? 'text-amber-500' : 'text-emerald-500') }}"
            >
                <x-slot name="icon">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                </x-slot>

                <div class="mt-2">
                    <div class="flex justify-between items-end mb-1">
                        <span class="text-[10px] font-medium bg-slate-50 px-2 py-0.5 rounded text-slate-400 border border-slate-100">
                            Peak Utilization
                        </span>
                        <span class="text-[10px] text-slate-400 font-medium">Batas: < 80%</span>
                    </div>
                    <div class="w-full bg-slate-100/80 rounded-full h-2 overflow-hidden shadow-inner">
                        <div class="h-full rounded-full transition-all duration-1000 ease-out {{ $capacityUtilization > 80 ? 'bg-rose-500 shadow-[0_0_10px_rgba(244,63,94,0.4)]' : ($capacityUtilization > 60 ? 'bg-[#FDBE33] shadow-[0_0_10px_rgba(253,190,51,0.4)]' : 'bg-emerald-500 shadow-[0_0_10px_rgba(16,185,129,0.4)]') }}" 
                             style="width: {{ min($capacityUtilization, 100) }}%"></div>
                    </div>
                </div>
            </x-kpi-card>

             <!-- Card 4: Operations Profile -->
             <x-kpi-card 
                title="Rata-rata Harian" 
                value="{{ $avgDailyFlights }}" 
                iconBg="bg-indigo-50" iconColor="text-indigo-600"
             >
                <x-slot name="icon">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path d="M7 3a1 1 0 000 2h6a1 1 0 100-2H7zM4 7a1 1 0 011-1h10a1 1 0 110 2H5a1 1 0 01-1-1zM2 11a2 2 0 012-2h12a2 2 0 012 2v4a2 2 0 01-2 2H4a2 2 0 01-2-2v-4z" /></svg>
                </x-slot>

                <div class="mt-4 pt-3 border-t border-slate-100/50">
                    <div class="flex justify-between items-center group">
                         <div class="flex items-center gap-2">
                            <span class="text-xs text-slate-500 font-medium font-outfit uppercase tracking-wider">Traffic Latih</span>
                        </div>
                         <div class="flex items-center gap-2">
                            <span class="font-bold text-purple-600 text-sm">{{ $trainingImpact }}%</span>
                            <div class="w-12 bg-slate-100 rounded-full h-1.5 overflow-hidden">
                                <div class="bg-purple-500 h-1.5 rounded-full shadow-sm" style="width: {{ $trainingImpact }}%"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </x-kpi-card>
        </div>



        <!-- 1. SECTION: Operational Insights (Composition & Distribution) -->
        <div class="mb-8 animate-enter" style="animation-delay: 0.5s">
            <h2 class="text-xl font-bold text-[#1F3C88] mb-6 flex items-center gap-2 border-l-4 border-[#FDBE33] pl-3 font-outfit">
                1. Distribusi & Pola Operasional
            </h2>

            <!-- Top Row: Composition & Weekly Pattern -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
                
                <!-- Category Chart (Composition) -->
                <x-chart-card title="Komposisi" chartId="categoryChart" height="220px">
                    <x-slot name="icon">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z" /></svg>
                    </x-slot>
                    <x-slot name="iconBg">bg-amber-50</x-slot>
                    <x-slot name="iconColor">text-[#FDBE33]</x-slot>
                    
                    <x-slot name="action">
                         <span class="bg-amber-100/80 backdrop-blur text-amber-700 text-[10px] font-bold px-2 py-1 rounded-full shadow-sm border border-amber-200 whitespace-nowrap">
                            {{ $dominantInsight }}
                        </span>
                    </x-slot>

                    <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none animate-enter" style="animation-delay: 0.8s">
                        <span class="text-[10px] text-slate-400 uppercase font-bold tracking-widest">Total</span>
                        <span class="text-2xl font-extrabold text-[#1F3C88] font-outfit">{{ number_format($totalFlights) }}</span>
                    </div>

                    <!-- Legend -->
                    <div class="mt-auto space-y-3 border-t border-slate-100/50 pt-4 relative z-10">
                        <div class="flex justify-between text-sm items-center group cursor-default">
                            <span class="flex items-center gap-2 text-slate-600 font-medium text-xs uppercase tracking-wider"><span class="w-2 h-2 bg-[#1F3C88] rounded-full ring-2 ring-blue-50 group-hover:ring-blue-100 transition"></span> Domestik</span> 
                            <span class="font-bold text-slate-700 bg-slate-50 px-2 py-0.5 rounded font-outfit">{{ number_format($chartCategory['dom']) }}</span>
                        </div>
                        <div class="flex justify-between text-sm items-center group cursor-default">
                            <span class="flex items-center gap-2 text-slate-600 font-medium text-xs uppercase tracking-wider"><span class="w-2 h-2 bg-[#FDBE33] rounded-full ring-2 ring-amber-50 group-hover:ring-amber-100 transition"></span> Internasional</span> 
                            <span class="font-bold text-slate-700 bg-slate-50 px-2 py-0.5 rounded font-outfit">{{ number_format($chartCategory['int']) }}</span>
                        </div>
                        <div class="flex justify-between text-sm items-center group cursor-default">
                            <span class="flex items-center gap-2 text-slate-600 font-medium text-xs uppercase tracking-wider"><span class="w-2 h-2 bg-slate-300 rounded-full ring-2 ring-slate-50 group-hover:ring-slate-100 transition"></span> Training</span> 
                            <span class="font-bold text-slate-700 bg-slate-50 px-2 py-0.5 rounded font-outfit">{{ number_format($chartCategory['training']) }}</span>
                        </div>
                    </div>
                </x-chart-card>

                <!-- Peak Hour & Weekly Pattern -->
                <div class="lg:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-6 h-full">
                    
                    <!-- Peak Hour Frequency -->
                    <x-chart-card title="Jam Sibuk" chartId="peakChart" height="220px">
                        <x-slot name="icon">
                           <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        </x-slot>
                        <x-slot name="iconBg">bg-rose-50</x-slot>
                        <x-slot name="iconColor">text-rose-500</x-slot>

                        <x-slot name="action">
                            <span class="bg-rose-100/80 backdrop-blur text-rose-700 text-[10px] font-bold px-2 py-1 rounded-full shadow-sm border border-rose-200">
                                Peak: {{ $highestPeak['count'] }} mvmt
                            </span>
                        </x-slot>
                    </x-chart-card>

                    <!-- Day of Week Pattern -->
                    <x-chart-card title="Pola Mingguan" chartId="dayOfWeekChart" height="220px" subtitle="Rata-rata Penerbangan Harian">
                        <x-slot name="icon">
                             <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                        </x-slot>
                        <x-slot name="iconBg">bg-indigo-50</x-slot>
                        <x-slot name="iconColor">text-indigo-500</x-slot>
                        
                        <x-slot name="action">
                            <div class="flex flex-col items-end">
                                <span class="bg-indigo-100/80 backdrop-blur text-indigo-700 text-[10px] font-bold px-2 py-1 rounded-full shadow-sm border border-indigo-200">
                                    Weekend {{ $weekendDiff >= 0 ? '+' : '' }}{{ $weekendDiff }}%
                                </span>
                            </div>
                        </x-slot>
                    </x-chart-card>
                </div>
            </div>
        </div>

        <!-- 2. SECTION: Temporal Analysis (Time-Based Trends) -->
        <div class="mb-12 animate-enter" style="animation-delay: 0.6s">
            <h2 class="text-xl font-bold text-[#1F3C88] mb-6 flex items-center gap-2 border-l-4 border-[#FDBE33] pl-3 font-outfit">
                2. Analisis Tren Waktu
            </h2>
            
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                
                <!-- Trend Chart -->
                <x-chart-card title="Tren Pergerakan" chartId="trendChart" height="300px">
                    <x-slot name="icon">
                         <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z" /></svg>
                    </x-slot>
                    <x-slot name="iconBg">bg-blue-50</x-slot>
                    <x-slot name="iconColor">text-[#1F3C88]</x-slot>

                    <x-slot name="action">
                        <div class="flex items-center gap-2">
                             <button type="button" onclick="document.getElementById('addEventModal').classList.remove('hidden');" class="bg-indigo-600 hover:bg-indigo-700 text-white text-[10px] font-bold px-2.5 py-1.5 rounded-full shadow-sm transition flex items-center gap-1">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                                Event
                            </button>
                             <span class="hidden sm:inline-block bg-blue-100/80 backdrop-blur text-blue-800 text-[10px] font-bold px-2 py-1 rounded-full shadow-sm border border-blue-200">
                                Max: {{ $busiestDay['count'] }}
                            </span>
                            <!-- Filter Toggles -->
                            <div class="flex gap-1 bg-slate-100 p-1 rounded-full border border-slate-200/60">
                                <button id="btn-trend-total" class="toggle-btn active">Total</button>
                                <button id="btn-trend-dom" class="toggle-btn inactive">Dom</button>
                                <button id="btn-trend-int" class="toggle-btn inactive">Int'l</button>
                                <button id="btn-trend-train" class="toggle-btn inactive">Train</button>
                            </div>
                        </div>
                    </x-slot>
                </x-chart-card>

                <!-- Arrival vs Departure Split -->
                <x-chart-card title="Arr vs Dep" chartId="arrDepChart" height="300px">
                     <x-slot name="icon">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" /></svg>
                    </x-slot>
                    <x-slot name="iconBg">bg-teal-50</x-slot>
                    <x-slot name="iconColor">text-teal-600</x-slot>

                    <x-slot name="action">
                        <div class="flex items-center gap-2">
                            <span class="hidden sm:inline-block bg-teal-100/80 backdrop-blur text-teal-800 text-[10px] font-bold px-2 py-1 rounded-full shadow-sm border border-teal-200">
                                {{ $balanceInsight }}
                            </span>
                            <!-- Toggle Controls -->
                            <div class="flex gap-1 bg-slate-100 p-1 rounded-full border border-slate-200/60">
                                <button id="btn-all" class="toggle-btn active">All</button>
                                <button id="btn-arr" class="toggle-btn inactive">Arr</button>
                                <button id="btn-dep" class="toggle-btn inactive">Dep</button>
                            </div>
                        </div>
                    </x-slot>
                </x-chart-card>
            </div>
            
            <!-- Yearly Comparison (Conditional) -->
            @if(isset($yearlyComparison) && $yearlyComparison)
                <div class="mt-6">
                     <x-chart-card title="Komparasi Seasonality (Multi-Year)" chartId="yearlyChart" height="350px">
                        <x-slot name="icon">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2" /></svg>
                        </x-slot>
                        <x-slot name="iconBg">bg-purple-50</x-slot>
                        <x-slot name="iconColor">text-purple-600</x-slot>
                    </x-chart-card>
                </div>
            @endif
        </div>

    <!-- 3. SECTION: Pertumbuhan & Komparasi (YTD & MoM) -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-4 gap-4 animate-enter" style="animation-delay: 0.4s">
        <h3 class="text-lg font-bold text-[#1F3C88] flex items-center gap-2 font-outfit">
            <span class="p-1.5 bg-blue-50 rounded-lg text-blue-600">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" /></svg>
            </span>
            Pertumbuhan & Komparasi
        </h3>

        <!-- Global Year & Compare Filters for KPIs -->
        <div class="glass-card p-1 rounded-xl flex gap-1 border border-white/80 shadow-sm bg-white/50 backdrop-blur-md">
            <div class="relative group">
                <select id="globalYearSelect" class="appearance-none bg-transparent text-xs font-bold text-[#1F3C88] focus:outline-none cursor-pointer py-1.5 pl-3 pr-7 hover:text-blue-700 transition-colors">
                    @foreach($availableDates as $y => $d)
                        <option value="{{ $y }}" {{ ($year ?? 2026) == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endforeach
                </select>
                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-1.5 text-blue-400 group-hover:text-blue-600 transition-colors">
                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"></path></svg>
                </div>
            </div>
            <div class="w-px h-5 bg-slate-200/80 self-center"></div>
            <div class="relative group">
                <select id="globalCompareSelect" class="appearance-none bg-transparent text-xs font-bold text-slate-500 focus:outline-none cursor-pointer py-1.5 pl-3 pr-7 hover:text-indigo-600 transition-colors">
                    <option value="">vs Year</option>
                    @foreach($availableDates as $y => $d)
                        @if($y != ($year ?? 2026))
                            <option value="{{ $y }}">{{ $y }}</option>
                        @endif
                    @endforeach
                </select>
                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-1.5 text-slate-400 group-hover:text-indigo-400 transition-colors">
                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"></path></svg>
                </div>
            </div>
        </div>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-5 gap-6 mb-10 animate-enter" style="animation-delay: 0.5s">
        <!-- Card 1: Total Flights Growth (YTD) -->
        <x-kpi-card 
            title="Total Penerbangan (YTD)" 
            value="{{ number_format($totalCurrentYear ?? 0) }}" 
            iconBg="bg-blue-50" iconColor="text-blue-600"
            growth="{{ $growthTotal ?? 0 }}"
            growthText="vs {{ $prevYear ?? 'Prev' }}"
            class="border border-white/60"
            idVal="kpiTotalVal" idIcon="kpiTotalGrowthIcon" idText="kpiTotalGrowthText" idContainer="kpiTotalGrowthContainer" idVs="kpiTotalVs"
        >
            <x-slot name="icon">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19V5m0 0l-4 4m4-4l4 4" /></svg>
            </x-slot>
        </x-kpi-card>

        <!-- Card 2: Peak Day Traffic (YTD) -->
        <x-kpi-card 
            title="Puncak (YTD)" 
            value="{{ number_format($peakDayCurrent ?? 0) }}" 
            iconBg="bg-amber-50" iconColor="text-amber-600"
            growth="{{ $growthPeak ?? 0 }}"
            growthText="vs {{ $prevYear ?? 'Prev' }}"
             class="border border-white/60"
            idVal="kpiPeakVal" idIcon="kpiPeakGrowthIcon" idText="kpiPeakGrowthText" idContainer="kpiPeakGrowthContainer" idVs="kpiPeakVs"
        >
             <x-slot name="icon">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" /></svg>
            </x-slot>
        </x-kpi-card>

        <!-- Card 3: Avg Daily Traffic (YTD) -->
        <x-kpi-card 
            title="Avg Harian (YTD)" 
            value="{{ number_format($avgCurrent ?? 0) }}" 
            iconBg="bg-purple-50" iconColor="text-purple-600"
            growth="{{ $growthAvg ?? 0 }}"
            growthText="vs {{ $prevYear ?? 'Prev' }}"
             class="border border-white/60"
            idVal="kpiAvgVal" idIcon="kpiAvgGrowthIcon" idText="kpiAvgGrowthText" idContainer="kpiAvgGrowthContainer" idVs="kpiAvgVs"
        >
             <x-slot name="icon">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" /></svg>
            </x-slot>
        </x-kpi-card>

        <!-- Card 4: Peak Hour (MoM) -->
        <x-kpi-card 
            title="Peak Jam (MoM)" 
            value="{{ number_format($momStats['current_peak'] ?? 0) }}" 
            iconBg="bg-cyan-50" iconColor="text-cyan-600"
            growth="{{ $momStats['peak_growth'] ?? 0 }}"
            growthText="vs Last Month"
            class="border border-white/60"
            idVal="kpiMomPeakVal" idIcon="kpiMomPeakGrowthIcon" idText="kpiMomPeakGrowthText" idContainer="kpiMomPeakGrowthContainer" idVs="kpiMomPeakVs"
        >
            <x-slot name="icon">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" /></svg>
            </x-slot>
        </x-kpi-card>

        <!-- Card 5: Avg Daily (MoM) -->
        <x-kpi-card 
            title="Avg Harian (MoM)" 
            value="{{ number_format($momStats['current_avg'] ?? 0) }}" 
            iconBg="bg-teal-50" iconColor="text-teal-600"
            growth="{{ $momStats['avg_growth'] ?? 0 }}"
            growthText="vs Last Month"
            class="border border-white/60"
            idVal="kpiMomAvgVal" idIcon="kpiMomAvgGrowthIcon" idText="kpiMomAvgGrowthText" idContainer="kpiMomAvgGrowthContainer" idVs="kpiMomAvgVs"
        >
            <x-slot name="icon">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" /></svg>
            </x-slot>
        </x-kpi-card>
    </div>

    <!-- 4. SECTION: Heatmap & Seasonal Analysis -->
    <div class="mb-10 animate-enter" style="animation-delay: 0.7s">
        <!-- Heatmap (Full Width) -->
        <div class="w-full glass-card p-6 rounded-[2rem] relative hover:shadow-lg transition-all duration-300 border border-white/60">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
                <h3 class="font-bold text-[#1F3C88] flex items-center gap-2 text-sm uppercase tracking-wider font-outfit">
                    <div class="p-2 bg-indigo-50 rounded-lg text-indigo-600">
                         <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" /></svg>
                    </div>
                    Heatmap Kesibukan
                </h3>
                
                <!-- Single Year Filter for Heatmap -->
                <div class="glass-card p-1 rounded-xl border border-white/80 shadow-sm bg-white/50 backdrop-blur-md">
                     <div class="relative group">
                        <select id="heatmapSingleYearSelect" class="appearance-none bg-transparent text-xs font-bold text-[#1F3C88] focus:outline-none cursor-pointer py-1.5 pl-3 pr-7 hover:text-indigo-600 transition-colors">
                            @foreach($availableDates as $y => $d)
                                <option value="{{ $y }}" {{ ($year ?? 2026) == $y ? 'selected' : '' }}>{{ $y }}</option>
                            @endforeach
                        </select>
                         <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-1.5 text-blue-400 group-hover:text-indigo-400 transition-colors">
                            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"></path></svg>
                        </div>
                    </div>
                </div>
            </div>

            <div class="overflow-x-auto pb-2 custom-scrollbar">
                <div id="heatmapYearView">
                    <div id="calendarHeatmap" class="min-w-[800px]"></div>
                </div>
            </div>
            
            <!-- Custom Legend -->
            <div class="flex items-center gap-2 mt-4 text-[10px] font-bold text-slate-400 justify-end">
                <span>Sepi</span>
                <div class="flex gap-1">
                    <span class="w-3 h-3 rounded-sm bg-slate-100"></span>
                    <span class="w-3 h-3 rounded-sm bg-indigo-100"></span>
                    <span class="w-3 h-3 rounded-sm bg-indigo-300"></span>
                    <span class="w-3 h-3 rounded-sm bg-indigo-500"></span>
                    <span class="w-3 h-3 rounded-sm bg-[#1F3C88]"></span>
                </div>
                <span>Sibuk</span>
            </div>
        </div>
    </div>

    <!-- Footer Note -->
    <div class="text-center text-slate-400 text-xs pb-8 flex items-center justify-center gap-1">
        <span>&copy; {{ date('Y') }} AirNav Indonesia Cabang Surabaya.</span>
        <span class="w-1 h-1 bg-slate-300 rounded-full"></span>
        <span>Data Operational Dashboard.</span>
    </div>
    </div>

    <!-- Footer -->
    <footer class="mt-8 py-8 border-t border-slate-200/60 text-center relative z-10 glass-card">
        <p class="text-slate-500 font-medium text-sm">© {{ date('Y') }} AirNav Indonesia. All rights reserved.</p>
        <p class="text-[10px] text-slate-400 mt-2 uppercase tracking-widest opacity-70">CONFIDENTIAL DATA • FOR INTERNAL USE ONLY</p>
    </footer>

    <!-- Drill Down Modal (Hidden by Default) -->
    <div id="drillDownModal" class="fixed inset-0 z-[100] hidden" role="dialog" aria-modal="true">
        <!-- Backdrop -->
        <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity opacity-0" id="modalBackdrop"></div>
        
        <!-- Modal Panel -->
        <div class="absolute inset-0 z-10 flex items-center justify-center p-4">
            <div class="bg-white/90 backdrop-blur-xl rounded-[2rem] shadow-2xl w-full max-w-4xl transform scale-95 opacity-0 transition-all duration-300 border border-white/60" id="modalPanel">
                <!-- Decorative Glows -->
                <div class="absolute top-0 right-0 w-64 h-64 bg-indigo-100/50 rounded-full blur-3xl -z-10 -mr-16 -mt-16 pointer-events-none"></div>
                
                <div class="p-6 relative max-h-[90vh] overflow-y-auto custom-scrollbar">
                    <!-- Close Button -->
                    <button id="closeModalBtn" onclick="closeModal()" class="absolute top-6 right-6 p-2 bg-slate-100/80 rounded-full text-slate-500 hover:bg-rose-100 hover:text-rose-600 transition-colors z-20">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>
                    </button>
                    
                    <!-- Header -->
                    <div class="mb-6 flex items-start justify-between pr-10">
                        <div>
                            <h2 class="text-2xl font-bold text-[#1F3C88] font-outfit flex items-center gap-2">
                                <span class="p-2 bg-indigo-50 rounded-lg text-indigo-600">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                </span>
                                Profil Jam Sibuk: <span id="modalDayName" class="text-indigo-600">-</span>
                            </h2>
                            <p class="text-slate-500 text-sm ml-12" id="modalSubtitle">Detail distribusi pergerakan per jam.</p>
                        </div>
                    </div>

                    <!-- Layout Grid: Chart (Left) + Detailed Stats (Right) -->
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                        
                        <!-- Main Content: Charts (Span 2) -->
                        <div class="lg:col-span-2 space-y-4">
                            <!-- Metrics Row inside Chart Area for Mobile/Tablet -->
                            <div class="grid grid-cols-3 gap-3">
                                <div class="bg-indigo-50/50 p-3 rounded-2xl border border-indigo-100">
                                    <div class="text-[10px] text-indigo-500 font-bold uppercase tracking-wider">Total</div>
                                    <div class="text-xl font-extrabold text-[#1F3C88] font-outfit" id="modalTotalFlights">-</div>
                                </div>
                                <div class="bg-amber-50/50 p-3 rounded-2xl border border-amber-100">
                                    <div class="text-[10px] text-amber-600 font-bold uppercase tracking-wider">Puncak</div>
                                    <div class="text-xl font-extrabold text-amber-600 font-outfit" id="modalPeakHour">-</div>
                                </div>
                                <div class="bg-emerald-50/50 p-3 rounded-2xl border border-emerald-100">
                                    <div class="text-[10px] text-emerald-600 font-bold uppercase tracking-wider">Status</div>
                                    <div class="text-xl font-extrabold text-emerald-600 font-outfit" id="modalStatus">-</div>
                                </div>
                            </div>
                            
                            <!-- Charts Grid -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <!-- Line Chart (Hourly Profile) -->
                                <div class="h-[280px] w-full bg-slate-50/50 rounded-2xl p-2 border border-slate-100 shadow-inner relative">
                                    <h4 class="absolute top-3 left-4 text-[10px] font-bold text-slate-400 uppercase tracking-wider">Profil Per Jam</h4>
                                    <canvas id="drillDownChart"></canvas>
                                </div>

                                <!-- Donut Chart (Flight Composition) -->
                                <div class="h-[280px] w-full bg-slate-50/50 rounded-2xl p-2 border border-slate-100 shadow-inner relative flex flex-col items-center justify-center">
                                    <h4 class="absolute top-3 left-4 text-[10px] font-bold text-slate-400 uppercase tracking-wider">Proporsi Tipe</h4>
                                    <div class="h-[200px] w-full relative">
                                        <canvas id="modalCompositionChart"></canvas>
                                        <!-- Center Text -->
                                        <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none">
                                            <span class="text-xs text-slate-400 font-medium">Total</span>
                                            <span class="text-xl font-bold text-[#1F3C88] font-outfit" id="modalCompTotal">-</span>
                                        </div>
                                    </div>
                                    <!-- Legend -->
                                    <div class="flex gap-3 mt-2">
                                        <div class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-[#1F3C88]"></span><span class="text-[10px] text-slate-500 font-bold">Dom</span></div>
                                        <div class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-[#FDBE33]"></span><span class="text-[10px] text-slate-500 font-bold">Int</span></div>
                                        <div class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-slate-400"></span><span class="text-[10px] text-slate-500 font-bold">Train</span></div>
                                    </div>
                                </div>
                            </div>

                             <!-- Top 3 Hours List -->
                            <div class="bg-indigo-50/30 rounded-2xl p-4 border border-indigo-100/50">
                                <h4 class="text-xs font-bold text-indigo-400 uppercase tracking-wider mb-3">3 Jam Tersibuk</h4>
                                <div id="modalTop3List" class="grid grid-cols-3 gap-4">
                                    <!-- Populated by JS -->
                                </div>
                            </div>
                        </div>

                        <!-- Sidebar: Time of Day Analysis -->
                        <div class="bg-slate-50/80 rounded-3xl p-6 border border-slate-100 h-full flex flex-col justify-center relative overflow-hidden">
                             <!-- Decorative background -->
                             <div class="absolute top-0 right-0 w-32 h-32 bg-blue-50 rounded-full blur-2xl -mr-10 -mt-10 pointer-events-none"></div>

                            <h3 class="text-sm font-bold text-[#1F3C88] mb-6 flex items-center gap-2 relative z-10">
                                <div class="p-1.5 bg-blue-100 rounded text-blue-600">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd" /></svg>
                                </div>
                                Distribusi Waktu
                            </h3>
                            
                            <div class="space-y-5 relative z-10">
                                <!-- Morning (06-12) -->
                                <div>
                                    <div class="flex justify-between text-xs mb-1.5">
                                        <span class="text-xs font-bold text-slate-500">Pagi (06-12)</span>
                                        <span class="text-xs font-bold text-[#1F3C88] font-outfit" id="statPagiVal">-</span>
                                    </div>
                                    <div class="w-full bg-slate-200/60 rounded-full h-2 overflow-hidden">
                                        <div class="bg-gradient-to-r from-sky-400 to-sky-500 h-2 rounded-full shadow-sm" id="statPagiBar" style="width: 0%"></div>
                                    </div>
                                </div>

                                <!-- Afternoon (12-18) -->
                                <div>
                                    <div class="flex justify-between text-xs mb-1.5">
                                        <span class="text-xs font-bold text-slate-500">Siang (12-18)</span>
                                        <span class="text-xs font-bold text-[#1F3C88] font-outfit" id="statSiangVal">-</span>
                                    </div>
                                    <div class="w-full bg-slate-200/60 rounded-full h-2 overflow-hidden">
                                        <div class="bg-gradient-to-r from-amber-400 to-amber-500 h-2 rounded-full shadow-sm" id="statSiangBar" style="width: 0%"></div>
                                    </div>
                                </div>

                                <!-- Evening (18-24) -->
                                <div>
                                    <div class="flex justify-between text-xs mb-1.5">
                                        <span class="text-xs font-bold text-slate-500">Sore (18-24)</span>
                                        <span class="text-xs font-bold text-[#1F3C88] font-outfit" id="statSoreVal">-</span>
                                    </div>
                                    <div class="w-full bg-slate-200/60 rounded-full h-2 overflow-hidden">
                                        <div class="bg-gradient-to-r from-indigo-400 to-indigo-600 h-2 rounded-full shadow-sm" id="statSoreBar" style="width: 0%"></div>
                                    </div>
                                </div>

                                <!-- Night (00-06) -->
                                <div>
                                    <div class="flex justify-between text-xs mb-1.5">
                                        <span class="text-xs font-bold text-slate-500">Malam (00-06)</span>
                                        <span class="text-xs font-bold text-[#1F3C88] font-outfit" id="statMalamVal">-</span>
                                    </div>
                                    <div class="w-full bg-slate-200/60 rounded-full h-2 overflow-hidden">
                                        <div class="bg-gradient-to-r from-slate-400 to-slate-500 h-2 rounded-full shadow-sm" id="statMalamBar" style="width: 0%"></div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mt-8 pt-5 border-t border-slate-200/60 relative z-10">
                                <div class="bg-white/60 p-3 rounded-xl border border-white shadow-sm">
                                    <p class="text-xs text-slate-500 italic text-center" id="modalInsightText">
                                        "Trafik tertinggi terjadi pada jam <span class="font-bold text-[#1F3C88]">07:00</span>."
                                    </p>
                                </div>
                            </div>
                        </div>

                    </div>
                    
                    <div class="mt-6 flex justify-end">
                        <button onclick="closeModal()" class="px-6 py-2.5 bg-white border border-slate-200 text-slate-600 font-bold rounded-xl hover:bg-slate-50 hover:text-[#1F3C88] transition shadow-sm text-sm">Tutup Detail</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Event Detail Modal (New) -->
    <div id="eventDetailModal" class="fixed inset-0 z-[100] hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" id="eventModalBackdrop"></div>
        <div class="fixed inset-0 z-10 overflow-y-auto">
            <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                <div class="relative transform overflow-hidden rounded-[2rem] bg-white/95 backdrop-blur-xl text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg border border-white/60">
                    
                    <!-- Modal Header -->
                    <div class="bg-gradient-to-r from-slate-50 to-white px-6 py-5 border-b border-slate-100 flex justify-between items-center">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 rounded-2xl bg-blue-50 flex items-center justify-center text-blue-600 shadow-sm border border-blue-100">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd" /></svg>
                            </div>
                            <div>
                                <h3 class="text-xl font-bold text-[#1F3C88] font-outfit" id="eventModalTitle">Detail Event</h3>
                                <p class="text-xs text-slate-500 font-medium mt-0.5" id="eventModalDate">Date Range</p>
                            </div>
                        </div>
                        <button type="button" id="closeEventModalBtn" class="bg-slate-100 p-2 rounded-full text-slate-400 hover:bg-rose-100 hover:text-rose-500 transition-colors">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                        </button>
                    </div>

                    <!-- Modal Body -->
                    <div class="px-6 py-6">
                        <div class="grid grid-cols-2 gap-4 mb-6">
                            <div class="bg-blue-50/50 p-5 rounded-2xl border border-blue-100">
                                <p class="text-[10px] text-blue-600 font-bold uppercase tracking-wider mb-1">Total Pergerakan</p>
                                <p class="text-3xl font-extrabold text-[#1F3C88] font-outfit" id="eventModalTotal">-</p>
                            </div>
                            <div class="bg-indigo-50/50 p-5 rounded-2xl border border-indigo-100">
                                <p class="text-[10px] text-indigo-600 font-bold uppercase tracking-wider mb-1">Rata-rata Harian</p>
                                <p class="text-3xl font-extrabold text-indigo-700 font-outfit" id="eventModalAvg">-</p>
                            </div>
                        </div>

                        <div class="bg-white rounded-2xl p-5 border border-slate-100 shadow-lg shadow-slate-100/50">
                            <div class="flex justify-between items-center mb-4">
                                <span class="text-xs font-bold text-slate-500 uppercase tracking-wide">Puncak Trafik Event</span>
                                <span class="text-[10px] font-bold text-slate-600 bg-slate-100 px-2 py-1 rounded-lg border border-slate-200" id="eventModalPeakDate">-</span>
                            </div>
                            <div class="flex items-baseline gap-2 mb-3">
                                <span class="text-4xl font-extrabold text-slate-800 font-outfit" id="eventModalPeakVal">-</span>
                                <span class="text-sm font-medium text-slate-400">penerbangan</span>
                            </div>
                            <div class="w-full bg-slate-100 rounded-full h-2 overflow-hidden">
                                <div class="bg-gradient-to-r from-slate-600 to-slate-800 h-2 rounded-full" style="width: 100%"></div>
                            </div>
                             <p class="text-[10px] text-slate-400 mt-2 text-right italic">*Data tertinggi selama periode event</p>
                        </div>
                    </div>
                    
                    <!-- Footer -->
                     <div class="bg-slate-50/50 px-6 py-4 border-t border-slate-100 flex justify-between items-center">
                        <form id="deleteEventForm" method="POST" action="" class="hidden m-0">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="px-4 py-2 text-rose-500 font-bold text-sm rounded-xl hover:bg-rose-100 transition shadow-sm border border-rose-200" onclick="return confirm('Hapus event ini?');">
                                Hapus
                            </button>
                        </form>
                        <button type="button" id="closeEventModalBtnText" class="px-6 py-2.5 bg-white border border-slate-200 text-slate-600 font-bold text-sm rounded-xl hover:bg-slate-50 hover:text-[#1F3C88] shadow-sm transition">
                            Tutup
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>



    <!-- Add Event Modal -->
    <div id="addEventModal" class="fixed inset-0 z-[100] hidden" role="dialog" aria-modal="true">
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" onclick="document.getElementById('addEventModal').classList.add('hidden');"></div>
        <div class="fixed inset-0 z-10 flex items-center justify-center p-4">
            <div class="bg-white/95 backdrop-blur-xl rounded-[2rem] shadow-2xl w-full max-w-md transform transition-all border border-white/60">
                <div class="bg-gradient-to-r from-slate-50 to-white px-6 py-5 border-b border-slate-100 flex justify-between items-center rounded-t-[2rem]">
                    <h3 class="text-xl font-bold text-[#1F3C88] font-outfit">Tambah Event Baru</h3>
                    <button type="button" onclick="document.getElementById('addEventModal').classList.add('hidden');" class="bg-slate-100 p-2 rounded-full text-slate-400 hover:bg-rose-100 hover:text-rose-500 transition-colors">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
                <form action="{{ route('dashboard.events.store') }}" method="POST" class="px-6 py-6">
                    @csrf
                    <div class="space-y-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-500 mb-1">Nama Event</label>
                            <input type="text" name="name" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition" placeholder="Contoh: Nataru 2026">
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-slate-500 mb-1">Tanggal Mulai</label>
                                <input type="date" name="start_date" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-500 mb-1">Tanggal Selesai</label>
                                <input type="date" name="end_date" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition">
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-500 mb-1">Warna Highlight</label>
                            <div class="flex items-center gap-3">
                                <input type="color" name="color" value="#10b981" required class="h-10 w-14 rounded cursor-pointer border-0 bg-transparent p-0">
                                <span class="text-xs text-slate-400">Pilih warna untuk grafik</span>
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-500 mb-1">Deskripsi Tambahan</label>
                            <textarea name="description" rows="2" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition" placeholder="Opsional..."></textarea>
                        </div>
                    </div>
                    <div class="mt-8 flex justify-end gap-3">
                        <button type="button" onclick="document.getElementById('addEventModal').classList.add('hidden');" class="px-5 py-2.5 text-slate-600 font-bold text-sm hover:bg-slate-50 rounded-xl transition">Batal</button>
                        <button type="submit" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-sm rounded-xl shadow-md shadow-indigo-600/20 transition">Simpan Event</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Consolidated Chart Scripts -->
    <!-- External Libraries (Local) -->

    <!-- Local Libraries (Must Load BEFORE Dashboard Scripts) -->
    <script src="{{ asset('js/libs/hammer.min.js') }}"></script>
    <script src="{{ asset('js/libs/chart.umd.min.js') }}"></script>
    <script src="{{ asset('js/libs/chartjs-plugin-datalabels.min.js') }}"></script>
    <script src="{{ asset('js/libs/chartjs-plugin-annotation.min.js') }}"></script>
    <script src="{{ asset('js/libs/chartjs-plugin-zoom.min.js') }}"></script>

    <!-- Dashboard Data & Logic -->
    <script>
        window.DashboardData = {
            chartTrend: @json($chartTrend),
            events: @json($events ?? []),
            labelType: @json($labelType),
            month: @json($month),
            year: @json($year),
            chartArrDep: @json($chartArrDep),
            chartCategory: @json($chartCategory),
            peakHourfreq: @json($peakHourfreq),
            hourlyProfiles: @json($hourlyProfiles),
            dayOfWeekComposition: @json($dayOfWeekComposition ?? []),
            dayOfWeekData: @json($dayOfWeekData),
            avgDailyFlights: @json($avgDailyFlights),
            heatmapData: @json($heatmapData ?? []),
            yearlyComparison: @json($yearlyComparison ?? []),
        };
    </script>
    
    <script src="{{ asset('js/dashboard.js') }}?v={{ time() }}"></script>
    <!-- Global Heatmap Tooltip -->
    <div id="heatmapTooltip" class="fixed z-[100] hidden bg-slate-800 text-white text-xs rounded-lg py-2 px-3 shadow-xl pointer-events-none transform -translate-x-1/2 -translate-y-full mb-2 transition-opacity duration-200">
        <div class="font-bold border-b border-slate-600 pb-1 mb-1" id="htDate"></div>
        <div class="text-slate-300" id="htValue"></div>
        <!-- Little arrow -->
        <div class="absolute bottom-0 left-1/2 transform -translate-x-1/2 translate-y-1 w-2 h-2 bg-slate-800 rotate-45"></div>
    </div>
</body>
</html>