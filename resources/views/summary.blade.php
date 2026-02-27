<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Summary Data - AirNav Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.0.0"></script>

    <style> 
        body { font-family: 'Poppins', sans-serif; } 
        .animate-fade-in-up { animation: fadeInUp 0.5s ease-out; }
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body class="bg-slate-50 min-h-screen flex flex-col">

    <nav class="w-full px-6 md:px-12 py-6 flex justify-between items-center bg-[#1F3C88] shadow-lg sticky top-0 z-50">
        <div class="flex items-center gap-3">
             <img src="{{ asset('img/logo_airnav.png') }}" class="h-10 md:h-12 object-contain">
             <span class="text-white font-bold text-xl hidden md:block tracking-wide">AirNav Indonesia</span>
        </div>
        <div class="flex items-center gap-2 font-medium text-white">
            <a href="{{ route('home') }}" class="flex items-center gap-2 px-5 py-2.5 rounded-full transition duration-300 hover:bg-white/10 hover:backdrop-blur-sm">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-5 h-5">
                    <path d="M10 12.5a2.5 2.5 0 100-5 2.5 2.5 0 000 5z" />
                    <path fill-rule="evenodd" d="M.664 10.59a1.651 1.651 0 010-1.186A10.004 10.004 0 0110 3c4.257 0 8.2 1.966 9.336 6.41.147.381.146.804 0 1.186A10.004 10.004 0 0110 17c-4.257 0-8.2-1.966-9.336-6.41zM10 15a5 5 0 100-10 5 5 0 000 10z" clip-rule="evenodd" />
                </svg>
                Home
            </a>

            <a href="{{ route('upload') }}" class="flex items-center gap-2 px-5 py-2.5 rounded-full transition duration-300 hover:bg-white/10 hover:backdrop-blur-sm">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-5 h-5">
                    <path fill-rule="evenodd" d="M5.5 17a4.5 4.5 0 01-1.44-8.765 4.5 4.5 0 018.302-3.046 3.5 3.5 0 014.504 4.272A4 4 0 0115 17H5.5zm3.75-2.75a.75.75 0 001.5 0V9.66l1.95 2.1a.75.75 0 101.1-1.02l-3.25-3.5a.75.75 0 00-1.1 0l-3.25 3.5a.75.75 0 101.1 1.02l1.95-2.1v4.59z" clip-rule="evenodd" />
                </svg>
                Upload
            </a>
            
            <a href="#" class="flex items-center gap-2 bg-white/20 backdrop-blur-md text-white px-5 py-2.5 rounded-full transition shadow-sm border border-white/10">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-5 h-5">
                    <path fill-rule="evenodd" d="M4.5 2A1.5 1.5 0 003 3.5v13A1.5 1.5 0 004.5 18h11a1.5 1.5 0 001.5-1.5V7.621a1.5 1.5 0 00-.44-1.06l-4.12-4.122A1.5 1.5 0 0011.378 2H4.5zm2.25 8.5a.75.75 0 000 1.5h6.5a.75.75 0 000-1.5h-6.5zm0 3a.75.75 0 000 1.5h6.5a.75.75 0 000-1.5h-6.5z" clip-rule="evenodd" />
                </svg>
                Summary
            </a>
            
            <a href="#" class="flex items-center gap-2 px-5 py-2.5 rounded-full transition duration-300 hover:bg-white/10 hover:backdrop-blur-sm">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-5 h-5">
                    <path fill-rule="evenodd" d="M4.25 2A2.25 2.25 0 002 4.25v2.5A2.25 2.25 0 004.25 9h2.5A2.25 2.25 0 009 6.75v-2.5A2.25 2.25 0 006.75 2h-2.5zm0 9A2.25 2.25 0 002 13.25v2.5A2.25 2.25 0 004.25 18h2.5A2.25 2.25 0 009 15.75v-2.5A2.25 2.25 0 006.75 11h-2.5zm9-9A2.25 2.25 0 0011 4.25v2.5A2.25 2.25 0 0013.25 9h2.5A2.25 2.25 0 0018 6.75v-2.5A2.25 2.25 0 0015.75 2h-2.5zm0 9A2.25 2.25 0 0011 13.25v2.5A2.25 2.25 0 0013.25 18h2.5A2.25 2.25 0 0018 15.75v-2.5A2.25 2.25 0 0015.75 11h-2.5z" clip-rule="evenodd" />
                </svg>
                Dashboard
            </a>
        </div>
    </nav>

    <section class="bg-white border-b border-slate-200 px-6 md:px-12 py-6">
        <div class="max-w-7xl mx-auto flex flex-col md:flex-row items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-[#1F3C88]">Monthly Summary</h1>
                <p class="text-slate-500 text-sm">Pilih periode data untuk melihat laporan detail.</p>
            </div>
            
            <div class="flex flex-wrap items-center gap-3 bg-slate-100 p-2 rounded-xl">
                <select id="filterMonth" onchange="updateYearOptions()" class="bg-white border border-slate-300 text-slate-700 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block py-2.5 pl-4 pr-10 min-w-[120px]">
                    <option value="" disabled selected>Pilih Bulan</option>
                    @foreach(array_keys($availableDates) as $month)
                        <option value="{{ $month }}">
                            {{ DateTime::createFromFormat('!m', $month)->format('F') }}
                        </option>
                    @endforeach
                </select>

                <select id="filterYear" onchange="updateBranchOptions()" disabled class="bg-slate-200 border border-slate-300 text-slate-500 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block py-2.5 pl-4 pr-10 min-w-[100px] cursor-not-allowed">
                    <option value="" disabled selected>Pilih Tahun</option>
                </select>

                <select id="filterBranch" disabled class="bg-slate-200 border border-slate-300 text-slate-500 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block py-2.5 pl-4 pr-10 min-w-[120px] cursor-not-allowed">
                    <option value="" disabled selected>Pilih Cabang</option>
                </select>

                <button onclick="loadData()" id="btn-show" class="bg-[#1F3C88] hover:bg-blue-800 text-white font-medium rounded-lg text-sm px-6 py-2.5 transition flex items-center gap-2 opacity-50 cursor-not-allowed" disabled>
                    <span>Tampilkan</span>
                </button>

                <div class="relative ml-2 border-l border-slate-300 pl-4 flex items-center gap-2">
                    
                    <span class="text-xs font-bold text-slate-500 uppercase mr-1">
                        Export
                    </span>

                    <button id="btnExportPDF" disabled type="button" onclick="exportPDF()" class="opacity-50 cursor-not-allowed bg-red-600 hover:bg-red-700 text-white rounded-lg p-2 shadow-sm transition hover:scale-105 focus:ring-2 focus:ring-red-300 flex items-center justify-center" title="Download PDF">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                            <polyline points="14 2 14 8 20 8"></polyline>
                            <line x1="16" y1="13" x2="8" y2="13"></line>
                            <line x1="16" y1="17" x2="8" y2="17"></line>
                            <polyline points="10 9 9 9 8 9"></polyline>
                        </svg>
                    </button>

                    <button id="dropdownExportBtn" disabled type="button" class="opacity-50 cursor-not-allowed bg-green-600 hover:bg-green-700 text-white rounded-lg p-2 shadow-sm transition hover:scale-105 focus:ring-2 focus:ring-green-300 flex items-center justify-center" title="Download Excel">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                            <polyline points="14 2 14 8 20 8"></polyline>
                            <line x1="8" y1="13" x2="16" y2="17"></line>
                            <line x1="16" y1="13" x2="8" y2="17"></line>
                        </svg>
                    </button>

                    <div id="dropdownExport" class="z-50 hidden bg-white divide-y divide-gray-100 rounded-lg shadow-xl w-56 absolute right-0 top-full mt-2 border border-slate-100 origin-top-right">
                        <ul class="py-2 text-sm text-gray-700">
                            <li>
                                <a href="javascript:void(0)" onclick="exportExcel('all')" class="block px-4 py-2 hover:bg-green-50 text-green-700 font-bold flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                    Export Semua
                                </a>
                            </li>
                            <li><a href="javascript:void(0)" onclick="exportExcel('table')" class="block px-4 py-2 hover:bg-gray-100">Hanya Movement Twr-Afis</a></li>
                            <li><a href="javascript:void(0)" onclick="exportExcel('peak')" class="block px-4 py-2 hover:bg-gray-100">Hanya Grafik Movement</a></li>
                            <li><a href="javascript:void(0)" onclick="exportExcel('traffic')" class="block px-4 py-2 hover:bg-gray-100">Hanya Grafik Dep-Arr</a></li>
                            <li><a href="javascript:void(0)" onclick="exportExcel('tabulation')" class="block px-4 py-2 hover:bg-gray-100">Hanya Grafik Peak Hours</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <main class="flex-grow px-6 md:px-12 py-8 max-w-7xl mx-auto w-full">

        <div id="empty-state" class="flex flex-col items-center justify-center py-20 text-center animate-fade-in-up">
            <div class="w-48 h-48 bg-blue-50 rounded-full flex items-center justify-center mb-6 shadow-sm">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-24 w-24 text-blue-200" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7m0 10a2 2 0 002 2h2a2 2 0 00-2 2" />
                </svg>
            </div>
            <h2 class="text-2xl font-bold text-slate-700">Belum ada data ditampilkan</h2>
            <p class="text-slate-400 mt-2 max-w-md">Silakan lengkapi filter <strong>Bulan, Tahun, dan Cabang</strong> di atas, lalu klik <strong>Tampilkan</strong>.</p>
        </div>

        <div id="loading-state" class="hidden flex flex-col items-center justify-center py-32">
            <svg class="animate-spin h-12 w-12 text-[#1F3C88] mb-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <p class="text-slate-500 font-medium">Sedang memuat data...</p>
        </div>

        <div id="dashboard-content" class="hidden space-y-12 transition-opacity duration-500 opacity-0">
            
            <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
                <div class="lg:col-span-3 bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                    <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center bg-blue-50/50">
                        <h3 class="font-bold text-[#1F3C88] flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" /></svg>
                            Daily Movements
                        </h3>
                    </div>
                    <div class="overflow-x-auto max-h-[500px] overflow-y-auto">
                        <table class="w-full text-sm text-left text-slate-600">
                            <thead class="text-xs text-[#1F3C88] uppercase bg-slate-50 sticky top-0 z-10 shadow-sm">
                                <tr>
                                    <th class="px-4 py-3 font-bold">Tanggal</th>
                                    <th class="px-4 py-3 text-center">Dep</th>
                                    <th class="px-4 py-3 text-center">Arr</th>
                                    <th class="px-4 py-3 text-center font-bold text-blue-700">Total</th>
                                    <th class="px-4 py-3 text-center">Jam Peak</th>
                                    <th class="px-4 py-3 text-center">Jml Peak</th>
                                    <th class="px-4 py-3 text-center">RWY Cap</th>
                                    <th class="px-4 py-3 text-center">Cabang</th>
                                </tr>
                            </thead>
                            <tbody id="tableBody" class="divide-y divide-slate-100"></tbody>
                        </table>
                    </div>
                </div>

                <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden h-fit">
                    <div class="px-6 py-4 border-b border-slate-100 bg-blue-50/50">
                        <h3 class="font-bold text-[#1F3C88] flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                            Peak Hour Stats
                        </h3>
                    </div>
                    <div class="overflow-y-auto max-h-[500px]">
                        <table class="w-full text-sm text-left text-slate-600">
                            <thead class="text-xs text-[#1F3C88] uppercase bg-slate-50">
                                <tr>
                                    <th class="px-4 py-3">Jam (UTC)</th>
                                    <th class="px-4 py-3 text-right">Freq</th>
                                </tr>
                            </thead>
                            <tbody id="peakStatBody" class="divide-y divide-slate-100"></tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="space-y-8">
                <div class="bg-white p-6 rounded-3xl shadow-sm border border-slate-200">
                    <div class="relative h-[450px]"><canvas id="chartPeakMovement"></canvas></div>
                </div>
                
                <div class="bg-white p-6 rounded-3xl shadow-sm border border-slate-200">
                    <div class="relative h-[450px]"><canvas id="chartTraffic"></canvas></div>
                </div>
                
                <div class="bg-white p-6 rounded-3xl shadow-sm border border-slate-200">
                    <div class="relative h-[450px]"><canvas id="chartPeakTabulation"></canvas></div>
                </div>
            </div>
        </div>

    </main>

    <form id="exportForm" action="{{ route('summary.export') }}" method="POST" class="hidden">
        @csrf
        <input type="hidden" name="month" id="exportMonth">
        <input type="hidden" name="year" id="exportYear">
        <input type="hidden" name="branch_code" id="exportBranch"> <input type="hidden" name="type" id="exportType">
        <input type="hidden" name="chart_peak_img" id="exportImgPeak">
        <input type="hidden" name="chart_traffic_img" id="exportImgTraffic">
        <input type="hidden" name="chart_tabulation_img" id="exportImgTabulation">
    </form>

    <script>
        const dateMap = @json($availableDates);
        let chartPeak, chartTraffic, chartTabulation;
        const branchNamesMap = @json($cabangs);

        function updateYearOptions() {
            const monthSelect = document.getElementById('filterMonth');
            const yearSelect = document.getElementById('filterYear');
            const branchSelect = document.getElementById('filterBranch');
            const btnShow = document.getElementById('btn-show');
            const selectedMonth = monthSelect.value;

            yearSelect.innerHTML = '<option value="" disabled selected>Pilih Tahun</option>';
            yearSelect.disabled = true;
            yearSelect.classList.add('bg-slate-200', 'cursor-not-allowed');
            yearSelect.classList.remove('bg-white');

            branchSelect.innerHTML = '<option value="" disabled selected>Pilih Cabang</option>';
            branchSelect.disabled = true;
            branchSelect.classList.add('bg-slate-200', 'cursor-not-allowed');
            branchSelect.classList.remove('bg-white');
            
            btnShow.disabled = true;
            btnShow.classList.add('opacity-50', 'cursor-not-allowed');

            if (selectedMonth && dateMap[selectedMonth]) {
                const years = Object.keys(dateMap[selectedMonth]).sort().reverse();
                years.forEach(year => {
                    const option = document.createElement('option');
                    option.value = year;
                    option.textContent = year;
                    yearSelect.appendChild(option);
                });
                yearSelect.disabled = false;
                yearSelect.classList.remove('bg-slate-200', 'cursor-not-allowed');
                yearSelect.classList.add('bg-white');
            }
        }

        function updateBranchOptions() {
            const monthSelect = document.getElementById('filterMonth');
            const yearSelect = document.getElementById('filterYear');
            const branchSelect = document.getElementById('filterBranch');
            const btnShow = document.getElementById('btn-show');
            
            const selectedMonth = monthSelect.value;
            const selectedYear = yearSelect.value;

            branchSelect.innerHTML = '<option value="" disabled selected>Pilih Cabang</option>';
            branchSelect.disabled = true;
            branchSelect.classList.add('bg-slate-200', 'cursor-not-allowed');
            branchSelect.classList.remove('bg-white');
            
            btnShow.disabled = true;
            btnShow.classList.add('opacity-50', 'cursor-not-allowed');

            if (selectedMonth && selectedYear && dateMap[selectedMonth][selectedYear]) {
                const branches = dateMap[selectedMonth][selectedYear].sort();
                branches.forEach(branch => {
                    const option = document.createElement('option');
                    option.value = branch;
                    const cityName = branchNamesMap[branch] ? branchNamesMap[branch] : '';
                    option.textContent = cityName ? `${branch} - ${cityName}` : branch;
                    
                    branchSelect.appendChild(option);
                });
                branchSelect.disabled = false;
                branchSelect.classList.remove('bg-slate-200', 'cursor-not-allowed');
                branchSelect.classList.add('bg-white');
            }
        }

        document.getElementById('filterBranch').addEventListener('change', function() {
            const btnShow = document.getElementById('btn-show');
            if (this.value) {
                btnShow.disabled = false;
                btnShow.classList.remove('opacity-50', 'cursor-not-allowed');
            }
        });

        function loadData() {
            const monthSelect = document.getElementById('filterMonth');
            const yearSelect = document.getElementById('filterYear');
            const branchSelect = document.getElementById('filterBranch');
            
            const monthVal = monthSelect.value;
            const yearVal = yearSelect.value;
            const branchVal = branchSelect.value;

            if (!monthVal || !yearVal || !branchVal) {
                alert("Mohon lengkapi Bulan, Tahun, dan Cabang.");
                return;
            }

            const monthName = monthSelect.options[monthSelect.selectedIndex].text.trim();
            const displayCityName = branchNamesMap[branchVal] ? branchNamesMap[branchVal] : branchVal;
            const titlePeak = `Grafik Peak Movement Bulan ${monthName} Tahun ${yearVal} Cabang ${displayCityName}`;
            const titleTraffic = `Grafik Pergerakan Departure dan Arrival Bulan ${monthName} Tahun ${yearVal} Cabang ${displayCityName}`;
            const titleTabulation = `Grafik Tabulasi Peak Hour Pergerakan Bulan ${monthName} Tahun ${yearVal} Cabang ${displayCityName}`;

            document.getElementById('empty-state').classList.add('hidden');
            document.getElementById('dashboard-content').classList.add('hidden');
            document.getElementById('dashboard-content').classList.remove('opacity-100');
            document.getElementById('loading-state').classList.remove('hidden');
            
            const btnShow = document.getElementById('btn-show');
            const btnExport = document.getElementById('dropdownExportBtn');
            const btnPDF = document.getElementById('btnExportPDF');

            btnShow.disabled = true;
            btnShow.classList.add('opacity-70');
            btnExport.disabled = true;
            btnExport.classList.add('opacity-50', 'cursor-not-allowed');
            btnPDF.disabled = true;
            btnPDF.classList.add('opacity-50', 'cursor-not-allowed');

            fetch(`{{ route('summary.data') }}?month=${monthVal}&year=${yearVal}&branch_code=${branchVal}`)
                .then(response => response.json())
                .then(data => {
                    document.getElementById('loading-state').classList.add('hidden');
                    btnShow.disabled = false;
                    btnShow.classList.remove('opacity-70');

                    if (data.status === 'success') {
                        updateTable(data.table_data);
                        updatePeakStats(data.charts.peak_hour_keys, data.charts.peak_hour_values);
                        renderCharts(data.charts, titlePeak, titleTraffic, titleTabulation);

                        const dashboard = document.getElementById('dashboard-content');
                        dashboard.classList.remove('hidden');
                        setTimeout(() => { dashboard.classList.add('opacity-100'); }, 50);

                        btnExport.disabled = false;
                        btnExport.classList.remove('opacity-50', 'cursor-not-allowed');
                        btnPDF.disabled = false;
                        btnPDF.classList.remove('opacity-50', 'cursor-not-allowed');

                    } else {
                        alert("Data tidak ditemukan.");
                        document.getElementById('empty-state').classList.remove('hidden');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert("Terjadi kesalahan sistem.");
                    document.getElementById('loading-state').classList.add('hidden');
                    document.getElementById('empty-state').classList.remove('hidden');
                    btnShow.disabled = false;
                });
        }

        function updateTable(data) {
            const tbody = document.getElementById('tableBody');
            tbody.innerHTML = '';
            data.forEach(row => {
                const tr = document.createElement('tr');
                tr.className = "hover:bg-slate-50 transition";
                tr.innerHTML = `
                    <td class="px-4 py-3 font-medium text-slate-900">${row.date}</td>
                    <td class="px-4 py-3 text-center">${row.total_dep}</td>
                    <td class="px-4 py-3 text-center">${row.total_arr}</td>
                    <td class="px-4 py-3 text-center font-bold text-blue-700 bg-blue-50">${row.total_flights}</td>
                    <td class="px-4 py-3 text-center font-mono text-xs">${row.peak_hour}</td>
                    <td class="px-4 py-3 text-center font-bold">${row.peak_hour_count}</td>
                    <td class="px-4 py-3 text-center text-slate-400">${row.runway_capacity}</td>
                    <td class="px-4 py-3 text-center font-semibold text-slate-500">${row.branch_code}</td>
                `;
                tbody.appendChild(tr);
            });
        }

        function updatePeakStats(keys, values) {
            const tbody = document.getElementById('peakStatBody');
            tbody.innerHTML = '';
            keys.forEach((key, index) => {
                const tr = document.createElement('tr');
                const bgClass = values[index] > 0 ? 'bg-blue-50/50 font-semibold text-blue-800' : '';
                tr.className = bgClass;
                tr.innerHTML = `
                    <td class="px-4 py-2 font-mono text-xs">${key}</td>
                    <td class="px-4 py-2 text-right">${values[index]}</td>
                `;
                tbody.appendChild(tr);
            });
        }

        function renderCharts(data, titlePeak, titleTraffic, titleTabulation) {
            Chart.register(ChartDataLabels);

            if (chartPeak) chartPeak.destroy();
            if (chartTraffic) chartTraffic.destroy();
            if (chartTabulation) chartTabulation.destroy();

            const maxPeak = data.peak_movement.length > 0 ? Math.max(...data.peak_movement, ...data.rwy_cap) : 0;
            const maxTraffic = data.traffic_total.length > 0 ? Math.max(...data.traffic_total) : 0;
            
            const commonOptions = {
                responsive: true,
                maintainAspectRatio: false,
                layout: { padding: { top: 10 } },
                scales: {
                    x: {
                        ticks: { maxRotation: 45, minRotation: 45, autoSkip: true, font: { size: 11 } },
                        grid:{ display:false },
                        title: {
                            display: true, text: 'Tanggal', color: '#64748b',
                            font: { size: 12, weight: 'bold' }, padding: { top: 10 }
                        }
                    },
                    y: {
                        beginAtZero: true,
                        grid: { display: true },
                        title: {
                            display: true, text: 'Jumlah Penerbangan', color: '#64748b',
                            font: { size: 12, weight: 'bold' }
                        }
                    }
                },
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { usePointStyle: true, padding: 20 }
                    },
                    tooltip: { mode: 'index', intersect: false, usePointStyle: true }
                },
                interaction: { mode: 'nearest', axis: 'x', intersect: false }
            };

            const ctx1 = document.getElementById('chartPeakMovement').getContext('2d');
            chartPeak = new Chart(ctx1, {
                type: 'bar',
                data: {
                    labels: data.labels,
                    datasets: [
                        {
                            label: 'Runway Capacity',
                            data: data.rwy_cap,
                            type: 'line',
                            borderColor: '#b91c1c', backgroundColor: '#ffffff',
                            borderWidth: 4, tension: 0.4,
                            pointBackgroundColor: '#ffffff', pointBorderColor: '#b91c1c', pointBorderWidth: 3, pointRadius: 5, pointHoverRadius: 7,
                            borderDash: [], order: 1,
                            pointStyle: 'circle',
                            datalabels: {
                                align: 'top', anchor: 'center', color: '#b91c1c', offset: 8,
                                backgroundColor: 'transparent', font: { weight: 'bold' }
                            }
                        },
                        {
                            label: 'Peak Movement',
                            data: data.peak_movement,
                            backgroundColor: '#1F3C88', borderRadius: 4, order: 2,
                            pointStyle: 'rect',
                            datalabels: {
                                color: function(context) { return context.dataset.data[context.dataIndex] == 0 ? '#64748b' : '#ffffff'; },
                                align: function(context) { return context.dataset.data[context.dataIndex] == 0 ? 'top' : 'start'; },
                                anchor: 'end', offset: 4, display: 'auto',
                                font: { weight: 'bold', size: 10 }
                            }
                        }
                    ]
                },
                options: {
                    ...commonOptions,
                    scales: {
                        ...commonOptions.scales,
                        y: { ...commonOptions.scales.y, max: maxPeak + 5 }
                    },
                    plugins: {
                        ...commonOptions.plugins,
                        title: {
                            display: true,
                            text: titlePeak,
                            color: '#1F3C88',
                            font: { size: 18, weight: 'bold', family: "'Poppins', sans-serif" },
                            padding: { bottom: 20 }
                        }
                    }
                }
            });

            const ctx2 = document.getElementById('chartTraffic').getContext('2d');
            chartTraffic = new Chart(ctx2, {
                data: {
                    labels: data.labels,
                    datasets: [
                        {
                            type: 'line',
                            label: 'Total',
                            data: data.traffic_total,
                            borderColor: '#b91c1c', backgroundColor: '#ffffff', borderWidth: 4, tension: 0.4,
                            pointBackgroundColor: '#ffffff', pointBorderColor: '#b91c1c', pointBorderWidth: 3,
                            pointRadius: 5, pointHoverRadius: 7, fill: false,
                            pointStyle: 'circle',
                            datalabels: {
                                align: 'top', anchor: 'center', color: '#333', offset: 8,
                                backgroundColor: 'transparent', font: { weight: 'bold' }
                            }
                        },
                        {
                            type: 'bar', label: 'Dep', data: data.traffic_dep, backgroundColor: '#3b82f6',
                            pointStyle: 'rect',
                            datalabels: {
                                rotation: function(context) { return context.dataset.data[context.dataIndex] == 0 ? 0 : -90; },
                                anchor: function(context) { return context.dataset.data[context.dataIndex] == 0 ? 'end' : 'center'; },
                                align: function(context) { return context.dataset.data[context.dataIndex] == 0 ? 'top' : 'center'; },
                                color: function(context) { return context.dataset.data[context.dataIndex] == 0 ? '#64748b' : '#ffffff'; },
                                font: { weight: 'bold', size: 11 }
                            }
                        },
                        {
                            type: 'bar', label: 'Arr', data: data.traffic_arr, backgroundColor: '#ef4444',
                            pointStyle: 'rect',
                            datalabels: {
                                rotation: function(context) { return context.dataset.data[context.dataIndex] == 0 ? 0 : -90; },
                                anchor: function(context) { return context.dataset.data[context.dataIndex] == 0 ? 'end' : 'center'; },
                                align: function(context) { return context.dataset.data[context.dataIndex] == 0 ? 'top' : 'center'; },
                                color: function(context) { return context.dataset.data[context.dataIndex] == 0 ? '#64748b' : '#ffffff'; },
                                font: { weight: 'bold', size: 11 }
                            }
                        }
                    ]
                },
                options: {
                    ...commonOptions,
                    scales: {
                        ...commonOptions.scales,
                        y: { ...commonOptions.scales.y, max: maxTraffic + 50 }
                    },
                    plugins: {
                        ...commonOptions.plugins,
                        title: {
                            display: true,
                            text: titleTraffic,
                            color: '#1F3C88',
                            font: { size: 18, weight: 'bold', family: "'Poppins', sans-serif" },
                            padding: { bottom: 20 }
                        }
                    }
                }
            });

            const maxFreq = data.peak_hour_values.length > 0 ? Math.max(...data.peak_hour_values) : 0;
            const ctx3 = document.getElementById('chartPeakTabulation').getContext('2d');
            chartTabulation = new Chart(ctx3, {
                type: 'bar',
                data: {
                    labels: data.peak_hour_keys,
                    datasets: [{
                        label: 'Frekuensi',
                        data: data.peak_hour_values,
                        backgroundColor: '#1F3C88', borderRadius: 4,
                        datalabels: {
                            color: function(context) { return context.dataset.data[context.dataIndex] == 0 ? '#64748b' : '#ffffff'; },
                            align: function(context) { return context.dataset.data[context.dataIndex] == 0 ? 'top' : 'start'; },
                            anchor: 'end', offset: 4,
                            font: { weight: 'bold', size: 10 }
                        }
                    }]
                },
                options: {
                    ...commonOptions,
                    scales: {
                        x: {
                            ticks: { maxRotation: 90, minRotation: 90, font: { size: 10 } },
                            grid: { display: false },
                            title: {
                                display: true, text: 'Jam (UTC)', color: '#64748b',
                                font: { size: 12, weight: 'bold' }, padding: { top: 10 }
                            }
                        },
                        y: {
                            beginAtZero: true, ticks: { stepSize: 1 }, max: maxFreq + 2,
                            grid: { display: true },
                            title: {
                                display: true, text: 'Frekuensi', color: '#64748b',
                                font: { size: 12, weight: 'bold' }
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                usePointStyle: false,
                                boxWidth: 40
                            }
                        },
                        tooltip: { mode: 'index', intersect: false },
                        title: {
                            display: true,
                            text: titleTabulation,
                            color: '#1F3C88',
                            font: { size: 18, weight: 'bold', family: "'Poppins', sans-serif" },
                            padding: { bottom: 20 }
                        }
                    }
                }
            });
        }

    const dropdownBtn = document.getElementById('dropdownExportBtn');
    const dropdownMenu = document.getElementById('dropdownExport');

    dropdownBtn.addEventListener('click', function(e) {
        if (this.disabled) return;
        e.stopPropagation();
        dropdownMenu.classList.toggle('hidden');
    });

    document.addEventListener('click', function(e) {
        if (!dropdownBtn.contains(e.target) && !dropdownMenu.contains(e.target)) {
            dropdownMenu.classList.add('hidden');
        }
    });

    function exportExcel(type) {
        const monthVal = document.getElementById('filterMonth').value;
        const yearVal = document.getElementById('filterYear').value;
        const branchVal = document.getElementById('filterBranch').value;

        if (!monthVal || !yearVal || !branchVal) {
            alert("Mohon lengkapi filter lalu klik Tampilkan terlebih dahulu.");
            return;
        }

        if (!chartPeak || !chartTraffic || !chartTabulation) {
            alert("Grafik belum dimuat sepenuhnya. Silakan klik tombol Tampilkan.");
            return;
        }

        const setWhiteBg = (chart) => {
            const ctx = chart.canvas.getContext('2d');
            ctx.save(); ctx.globalCompositeOperation = 'destination-over';
            ctx.fillStyle = 'white'; ctx.fillRect(0, 0, chart.canvas.width, chart.canvas.height);
            const img = chart.canvas.toDataURL('image/jpeg', 0.8);
            ctx.restore();
            return img;
        };

        const imgPeak = setWhiteBg(chartPeak);
        const imgTraffic = setWhiteBg(chartTraffic);
        const imgTabulation = setWhiteBg(chartTabulation);

        document.getElementById('exportMonth').value = monthVal;
        document.getElementById('exportYear').value = yearVal;
        document.getElementById('exportBranch').value = branchVal;
        document.getElementById('exportType').value = type;
        document.getElementById('exportImgPeak').value = imgPeak;
        document.getElementById('exportImgTraffic').value = imgTraffic;
        document.getElementById('exportImgTabulation').value = imgTabulation;

        document.getElementById('exportForm').submit();
        dropdownMenu.classList.add('hidden');
    }

    function exportPDF() {
        const monthVal = document.getElementById('filterMonth').value;
        const yearVal = document.getElementById('filterYear').value;
        const branchVal = document.getElementById('filterBranch').value;

        if (!monthVal || !yearVal || !branchVal) {
            alert("Mohon lengkapi filter lalu klik Tampilkan terlebih dahulu.");
            return;
        }

        if (!chartPeak || !chartTraffic || !chartTabulation) {
            alert("Grafik belum dimuat sepenuhnya. Silakan klik tombol Tampilkan.");
            return;
        }

        const imgPeak = chartPeak.toBase64Image();
        const imgTraffic = chartTraffic.toBase64Image();
        const imgTabulation = chartTabulation.toBase64Image();

        document.getElementById('exportMonth').value = monthVal;
        document.getElementById('exportYear').value = yearVal;
        document.getElementById('exportBranch').value = branchVal;
        document.getElementById('exportImgPeak').value = imgPeak;
        document.getElementById('exportImgTraffic').value = imgTraffic;
        document.getElementById('exportImgTabulation').value = imgTabulation;

        const form = document.getElementById('exportForm');
        const originalAction = form.action;
        
        form.action = "{{ route('summary.export_pdf') }}";
        form.submit();

        setTimeout(() => {
            form.action = originalAction;
        }, 100);
    }

    document.addEventListener("DOMContentLoaded", function() {
        const urlParams = new URLSearchParams(window.location.search);
        const paramMonth = urlParams.get('month');
        const paramYear = urlParams.get('year');
        const paramBranch = urlParams.get('branch_code');

        if (paramMonth && paramYear) {
            const monthSelect = document.getElementById('filterMonth');
            const yearSelect = document.getElementById('filterYear');
            const branchSelect = document.getElementById('filterBranch');
            const btnShow = document.getElementById('btn-show');

            monthSelect.value = paramMonth;
            updateYearOptions();

            let yearExists = Array.from(yearSelect.options).some(option => option.value == paramYear);
            if (yearExists) {
                yearSelect.value = paramYear;
                updateBranchOptions();
                
                if (paramBranch) {
                    let branchExists = Array.from(branchSelect.options).some(option => option.value == paramBranch);
                    if (branchExists) {
                        branchSelect.value = paramBranch;
                        btnShow.disabled = false;
                        btnShow.classList.remove('opacity-50', 'cursor-not-allowed');

                        loadData();
                    }
                }
            }
        }
    });
    </script>
</body>
</html>
    