<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Data - AirNav Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style> body { font-family: 'Poppins', sans-serif; } </style>
</head>
<body class="bg-gradient-to-br from-[#1F3C88] to-[#0a1e50] min-h-screen flex flex-col relative overflow-x-hidden">

    <div class="fixed top-[-10%] left-[-10%] w-[500px] h-[500px] bg-blue-400/20 rounded-full blur-[100px] -z-10 pointer-events-none"></div>
    <div class="fixed bottom-[-10%] right-[-10%] w-[500px] h-[500px] bg-blue-600/20 rounded-full blur-[100px] -z-10 pointer-events-none"></div>

    <nav class="w-full px-6 md:px-12 py-6 flex justify-between items-center z-50">
        <div class="flex items-center gap-3">
             <img src="{{ asset('img/logo_airnav.png') }}" alt="AirNav Logo" class="h-10 md:h-12 object-contain">
             <span class="text-white font-bold text-xl hidden md:block drop-shadow-md tracking-wide">AirNav Indonesia</span>
        </div>
        <div class="flex items-center gap-2 font-medium text-white">
            <a href="{{ route('home') }}" class="flex items-center gap-2 px-5 py-2.5 rounded-full transition duration-300 hover:bg-white/10 hover:backdrop-blur-sm">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-5 h-5"><path d="M10 12.5a2.5 2.5 0 100-5 2.5 2.5 0 000 5z" /><path fill-rule="evenodd" d="M.664 10.59a1.651 1.651 0 010-1.186A10.004 10.004 0 0110 3c4.257 0 8.2 1.966 9.336 6.41.147.381.146.804 0 1.186A10.004 10.004 0 0110 17c-4.257 0-8.2-1.966-9.336-6.41zM10 15a5 5 0 100-10 5 5 0 000 10z" clip-rule="evenodd" /></svg>
                Home
            </a>
            <a href="#" class="flex items-center gap-2 bg-white/20 backdrop-blur-md text-white px-5 py-2.5 rounded-full transition shadow-sm border border-white/10">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-5 h-5"><path fill-rule="evenodd" d="M5.5 17a4.5 4.5 0 01-1.44-8.765 4.5 4.5 0 018.302-3.046 3.5 3.5 0 014.504 4.272A4 4 0 0115 17H5.5zm3.75-2.75a.75.75 0 001.5 0V9.66l1.95 2.1a.75.75 0 101.1-1.02l-3.25-3.5a.75.75 0 00-1.1 0l-3.25 3.5a.75.75 0 101.1 1.02l1.95-2.1v4.59z" clip-rule="evenodd" /></svg>
                Upload
            </a>
            <a href="{{ route('summary') }}" class="flex items-center gap-2 px-5 py-2.5 rounded-full transition duration-300 hover:bg-white/10 hover:backdrop-blur-sm">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-5 h-5"><path fill-rule="evenodd" d="M4.5 2A1.5 1.5 0 003 3.5v13A1.5 1.5 0 004.5 18h11a1.5 1.5 0 001.5-1.5V7.621a1.5 1.5 0 00-.44-1.06l-4.12-4.122A1.5 1.5 0 0011.378 2H4.5zm2.25 8.5a.75.75 0 000 1.5h6.5a.75.75 0 000-1.5h-6.5zm0 3a.75.75 0 000 1.5h6.5a.75.75 0 000-1.5h-6.5z" clip-rule="evenodd" /></svg>
                Summary
            </a>
            <a href="#" class="flex items-center gap-2 px-5 py-2.5 rounded-full transition duration-300 hover:bg-white/10 hover:backdrop-blur-sm">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-5 h-5"><path fill-rule="evenodd" d="M4.25 2A2.25 2.25 0 002 4.25v2.5A2.25 2.25 0 004.25 9h2.5A2.25 2.25 0 009 6.75v-2.5A2.25 2.25 0 006.75 2h-2.5zm0 9A2.25 2.25 0 002 13.25v2.5A2.25 2.25 0 004.25 18h2.5A2.25 2.25 0 009 15.75v-2.5A2.25 2.25 0 006.75 11h-2.5zm9-9A2.25 2.25 0 0011 4.25v2.5A2.25 2.25 0 0013.25 9h2.5A2.25 2.25 0 0018 6.75v-2.5A2.25 2.25 0 0015.75 2h-2.5zm0 9A2.25 2.25 0 0011 13.25v2.5A2.25 2.25 0 0013.25 18h2.5A2.25 2.25 0 0018 15.75v-2.5A2.25 2.25 0 0015.75 11h-2.5z" clip-rule="evenodd" /></svg>
                Dashboard
            </a>
        </div>
    </nav>

    <main class="flex-grow flex items-center justify-center p-6 z-10 relative">
        <div class="bg-white w-full max-w-xl rounded-[2.5rem] p-8 md:p-10 shadow-2xl relative z-10">
            
            @if(session('success'))
                <div class="mb-6 p-4 bg-green-50 text-green-700 rounded-xl text-center text-sm font-medium border border-green-100">✅ {{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="mb-6 p-4 bg-red-50 text-red-700 rounded-xl text-center text-sm font-medium border border-red-100">❌ {{ session('error') }}</div>
            @endif

            <div class="mx-auto w-16 h-16 mb-4 text-[#1F3C88]">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-full h-full">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 16.5V9.75m0 0l3 3m-3-3l-3 3M6.75 19.5a4.5 4.5 0 01-1.41-8.775 5.25 5.25 0 0110.233-2.33 3 3 0 013.758 3.848A3.752 3.752 0 0118 19.5H6.75z" />
                </svg>
            </div>

            <h1 class="text-2xl md:text-3xl font-bold text-[#1F3C88] text-center mb-2">Upload Your File</h1>
            <p class="text-slate-400 text-center mb-6 text-sm">Please upload your file according to Template</p>

            <div id="js-error-container" class="hidden mb-6 mx-auto w-full bg-red-50 border border-red-100 rounded-xl p-3 flex items-start gap-3 transition-all duration-300">
                <div class="p-1.5 bg-white rounded-full text-red-500 shadow-sm shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
                <div class="text-left w-full">
                    <h4 class="text-sm font-bold text-red-700">File Tidak Sesuai</h4>
                    <p id="js-error-message" class="text-xs text-red-500 mt-0.5 leading-snug"></p>
                </div>
                <button onclick="hideError()" class="ml-auto text-red-300 hover:text-red-500 transition">✕</button>
            </div>

            <form id="uploadForm" action="{{ route('upload.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf
                <input type="hidden" name="sheet_name" id="selectedSheetInput">
                <input type="hidden" name="manual_month" id="manualMonthInput">
                <input type="hidden" name="manual_year" id="manualYearInput">
                <input type="hidden" name="capacity_data" id="capacityDataInput">

                <div class="relative group">
                    <label for="dropzone-file" id="dropzone-box" class="flex flex-col items-center justify-center w-full h-16 border-2 border-[#1F3C88] border-dashed rounded-full cursor-pointer bg-slate-50 hover:bg-blue-50 transition overflow-hidden group-hover:border-blue-600">
                        <div class="flex items-center gap-3 px-6 w-full">
                            <span class="bg-[#1F3C88] text-white px-5 py-1.5 rounded-full text-sm font-semibold whitespace-nowrap group-hover:bg-blue-700 transition">Choose File</span>
                            <span class="text-slate-500 text-sm truncate w-full text-left" id="file-name">No File Chosen</span>
                        </div>
                        <input id="dropzone-file" name="file" type="file" class="hidden" required onchange="handleFileSelect(this)" />
                    </label>
                    <p class="text-xs text-slate-400 mt-2 text-center">Supported: .xls or .xlsx according to Airnav Template</p>
                </div>

                <button type="button" onclick="startProcess()" id="btn-submit" class="w-full bg-[#1F3C88] hover:bg-blue-800 text-white font-bold py-4 rounded-full shadow-lg hover:shadow-xl hover:-translate-y-1 transition-all duration-300 flex items-center justify-center gap-2 text-lg">
                    <span id="btn-text">Process Data</span>
                    <svg id="btn-loading" class="animate-spin h-5 w-5 text-white hidden" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </button>
            </form>

            <div class="mt-6 flex items-center justify-center gap-2 text-slate-300 text-xs">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-3 h-3 text-green-500"><path fill-rule="evenodd" d="M10 1a4.5 4.5 0 00-4.5 4.5V9H5a2 2 0 00-2 2v6a2 2 0 002 2h10a2 2 0 002-2v-6a2 2 0 00-2-2h-.5V5.5A4.5 4.5 0 0010 1zm3 8V5.5a3 3 0 10-6 0V9h6z" clip-rule="evenodd" /></svg>
                Secure Processing Environment
            </div>
        </div>
    </main>

    <div id="capacityModal" class="fixed inset-0 z-[100] hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity"></div>
        <div class="fixed inset-0 z-10 overflow-y-auto">
            <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                <div class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg border border-slate-100">
                    <div class="bg-white px-6 pb-6 pt-6">
                        <div class="text-center mb-6">
                            <h3 class="text-xl font-bold text-[#1F3C88]">Atur Kapasitas Runway</h3>
                            <p id="cap-month-name" class="text-sm font-semibold text-slate-500 mt-1 bg-slate-100 inline-block px-3 py-1 rounded-full">Periode: -</p>
                        </div>
                        
                        <div id="capacityRowsContainer" class="space-y-3"></div>

                        <button type="button" onclick="addCapacityRow()" class="mt-4 w-full border-2 border-dashed border-[#1F3C88] text-[#1F3C88] font-bold py-2 rounded-xl hover:bg-blue-50 transition flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                            Tambah Periode
                        </button>
                    </div>
                    <div class="bg-slate-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6 gap-2">
                        <button type="button" onclick="submitCapacity()" class="inline-flex w-full justify-center rounded-lg bg-[#1F3C88] px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-blue-800 sm:ml-3 sm:w-auto transition">Lanjut Process</button>
                        <button type="button" onclick="closeCapacityModal()" class="mt-3 inline-flex w-full justify-center rounded-lg bg-white px-5 py-2.5 text-sm font-semibold text-slate-900 shadow-sm ring-1 ring-inset ring-slate-300 hover:bg-slate-50 sm:mt-0 sm:w-auto transition">Cancel</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="sheetModal" class="fixed inset-0 z-[100] hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity"></div>
        <div class="fixed inset-0 z-10 overflow-y-auto">
            <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                <div class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-md border border-slate-100">
                    <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                        <div class="text-center">
                            <h3 class="text-lg font-bold leading-6 text-[#1F3C88] mb-2">Pilih Sheet Excel</h3>
                            <p class="text-sm text-slate-500 mb-4">File ini memiliki banyak sheet. Pilih salah satu:</p>
                            <div id="sheetListContainer" class="max-h-60 overflow-y-auto space-y-2 text-left"></div>
                        </div>
                    </div>
                    <div class="bg-slate-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                        <button type="button" onclick="closeSheetModal()" class="inline-flex w-full justify-center rounded-lg bg-white px-4 py-2 text-sm font-semibold text-slate-900 shadow-sm ring-1 ring-inset ring-slate-300 hover:bg-slate-50">Cancel</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="dateModal" class="fixed inset-0 z-[100] hidden" role="dialog" aria-modal="true">
        <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity"></div>
        <div class="fixed inset-0 z-10 overflow-y-auto">
            <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                <div class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-sm border border-slate-100">
                    <div class="bg-white px-6 pb-6 pt-6">
                        <div class="text-center mb-4">
                            <h3 class="text-lg font-bold text-[#1F3C88]">Tanggal Tidak Terdeteksi</h3>
                            <p class="text-sm text-slate-500 mt-1">Silakan masukkan Bulan & Tahun data ini secara manual.</p>
                        </div>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-xs font-bold text-slate-600 uppercase mb-1 text-left">Bulan</label>
                                <select id="inputMonth" class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <option value="1">Januari</option>
                                    <option value="2">Februari</option>
                                    <option value="3">Maret</option>
                                    <option value="4">April</option>
                                    <option value="5">Mei</option>
                                    <option value="6">Juni</option>
                                    <option value="7">Juli</option>
                                    <option value="8">Agustus</option>
                                    <option value="9">September</option>
                                    <option value="10">Oktober</option>
                                    <option value="11">November</option>
                                    <option value="12">Desember</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-600 uppercase mb-1 text-left">Tahun</label>
                                <input type="number" id="inputYear" value="{{ date('Y') }}" class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                        </div>
                    </div>
                    <div class="bg-slate-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                        <button type="button" onclick="submitManualDate()" class="inline-flex w-full justify-center rounded-lg bg-[#1F3C88] px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-800 sm:ml-3 sm:w-auto">Lanjut</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="warningModal" class="fixed inset-0 z-[100] hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity"></div>
        <div class="fixed inset-0 z-10 overflow-y-auto">
            <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                <div class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg border border-slate-100">
                    <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-yellow-100 sm:mx-0 sm:h-10 sm:w-10">
                                <svg class="h-6 w-6 text-yellow-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" /></svg>
                            </div>
                            <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left">
                                <h3 class="text-lg font-bold leading-6 text-slate-800" id="modal-title">Data Sudah Ada</h3>
                                <div class="mt-2"><p class="text-sm text-slate-500" id="modal-message">Data sudah tercatat. Timpa?</p></div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-slate-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6 gap-2">
                        <button type="button" onclick="confirmOverwrite()" class="inline-flex w-full justify-center rounded-lg bg-[#1F3C88] px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-blue-800 sm:ml-3 sm:w-auto transition">Ya, Timpa Data</button>
                        <button type="button" onclick="closeWarningModal()" class="mt-3 inline-flex w-full justify-center rounded-lg bg-white px-5 py-2.5 text-sm font-semibold text-slate-900 shadow-sm ring-1 ring-inset ring-slate-300 hover:bg-slate-50 sm:mt-0 sm:w-auto transition">Cancel</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        let globalExists = false;
        let globalMessage = "";
        let maxDaysInMonth = 31;

        function handleFileSelect(input) {
            document.getElementById('file-name').innerText = input.files[0].name;
            document.getElementById('selectedSheetInput').value = '';
            document.getElementById('manualMonthInput').value = '';
            document.getElementById('manualYearInput').value = '';
            document.getElementById('capacityDataInput').value = '';
            hideError();
        }

        function showError(message) {
            const errorContainer = document.getElementById('js-error-container');
            const errorText = document.getElementById('js-error-message');
            errorText.innerText = message;
            errorContainer.classList.remove('hidden');
        }

        function resetFileInput() {
            document.getElementById('dropzone-file').value = '';
            document.getElementById('file-name').innerText = 'No File Chosen';
            document.getElementById('selectedSheetInput').value = '';
            document.getElementById('manualMonthInput').value = '';
            document.getElementById('manualYearInput').value = '';
            document.getElementById('capacityDataInput').value = '';
        }

        function hideError() {
            document.getElementById('js-error-container').classList.add('hidden');
        }

        function startProcess() {
            const fileInput = document.getElementById('dropzone-file');
            if (fileInput.files.length === 0) {
                showError("Mohon pilih file Excel terlebih dahulu.");
                return;
            }

            hideError();
            setLoading(true);

            let formData = new FormData();
            formData.append('file', fileInput.files[0]);
            formData.append('sheet_name', document.getElementById('selectedSheetInput').value);
            formData.append('manual_month', document.getElementById('manualMonthInput').value);
            formData.append('manual_year', document.getElementById('manualYearInput').value);
            formData.append('_token', '{{ csrf_token() }}');

            fetch('{{ route("upload.check") }}', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                setLoading(false);

                if (data.status === 'multiple_sheets') {
                    showSheetModal(data.sheets);
                }
                else if (data.status === 'invalid_format') {
                    showError(data.message);
                    resetFileInput();
                }
                else if (data.status === 'missing_date') {
                    document.getElementById('dateModal').classList.remove('hidden');
                }
                else if (data.status === 'success') {
                    globalExists = data.exists;
                    globalMessage = data.message;
                    
                    openCapacityModal(data.bulan_nama, data.days_in_month);
                } else {
                    showError('Error: ' + (data.error || 'Unknown error'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showError("Terjadi kesalahan sistem.");
                setLoading(false);
            });
        }

        function setLoading(isLoading) {
            const btnText = document.getElementById('btn-text');
            const btnLoading = document.getElementById('btn-loading');
            const btn = document.getElementById('btn-submit');
            
            if (isLoading) {
                btnText.innerText = "Checking...";
                btnLoading.classList.remove('hidden');
                btn.disabled = true;
            } else {
                btnText.innerText = "Process Data";
                btnLoading.classList.add('hidden');
                btn.disabled = false;
            }
        }

        function openCapacityModal(bulanNama, maxDays) {
            maxDaysInMonth = maxDays;
            document.getElementById('cap-month-name').innerText = "Periode: " + bulanNama;
            
            const container = document.getElementById('capacityRowsContainer');
            container.innerHTML = '';
            
            addCapacityRow(1, maxDaysInMonth, 31);
            
            document.getElementById('capacityModal').classList.remove('hidden');
        }

        function addCapacityRow(startVal, endVal, capVal) {
            const container = document.getElementById('capacityRowsContainer');
            const rows = container.children;
            const isFirstRow = rows.length === 0;

            let start = startVal;
            let end = endVal;
            let cap = capVal || 31;

            if (!isFirstRow && start === undefined) {
                const lastEndInput = rows[rows.length - 1].querySelector('.cap-end');
                const lastEndVal = parseInt(lastEndInput.value) || 0;
                start = lastEndVal < maxDaysInMonth ? lastEndVal + 1 : maxDaysInMonth;
            } else if (start === undefined) {
                start = 1;
            }

            if (end === undefined) {
                end = maxDaysInMonth;
            }

            const rowId = 'cap-row-' + Date.now();
            
            const rowHtml = `
                <div id="${rowId}" class="flex items-center gap-2 bg-slate-50 p-3 rounded-xl border border-slate-200 relative group">
                    <div class="flex-1">
                        <label class="block text-[10px] font-bold text-slate-500 uppercase">Kapasitas</label>
                        <input type="number" class="cap-val w-full bg-white border border-slate-300 rounded-md px-2 py-1.5 text-sm font-bold text-[#1F3C88]" value="${cap}" min="1">
                    </div>
                    <div class="w-16">
                        <label class="block text-[10px] font-bold text-slate-500 uppercase">Tgl</label>
                        <input type="number" oninput="syncPreviousRow(this)" class="cap-start w-full bg-white border border-slate-300 rounded-md px-2 py-1.5 text-sm text-center" value="${start}" min="1" max="${maxDaysInMonth}">
                    </div>
                    <div class="text-slate-400 font-bold mt-4">-</div>
                    <div class="w-16">
                        <label class="block text-[10px] font-bold text-slate-500 uppercase">s/d</label>
                        <input type="number" oninput="syncNextRow(this)" class="cap-end w-full bg-white border border-slate-300 rounded-md px-2 py-1.5 text-sm text-center" value="${end}" min="1" max="${maxDaysInMonth}">
                    </div>
                    
                    ${!isFirstRow ? `
                    <button type="button" onclick="removeCapacityRow('${rowId}')" class="mt-4 w-8 h-8 shrink-0 flex items-center justify-center text-red-500 bg-red-50 hover:bg-red-100 hover:text-red-700 rounded-md transition" title="Hapus Baris">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                    </button>
                    ` : `
                    <div class="w-8 shrink-0"></div>
                    `}
                </div>
            `;
            container.insertAdjacentHTML('beforeend', rowHtml);
        }

        function syncNextRow(inputElement) {
            const currentRow = inputElement.closest('.group');
            const container = document.getElementById('capacityRowsContainer');
            const rows = Array.from(container.children);
            
            const currentIndex = rows.indexOf(currentRow);
            
            if (currentIndex !== -1 && currentIndex < rows.length - 1) {
                const nextRow = rows[currentIndex + 1];
                const currentEndValue = parseInt(inputElement.value) || 0;
                
                const nextStartInput = nextRow.querySelector('.cap-start');
                
                let nextStartValue = currentEndValue + 1;
                if (nextStartValue > maxDaysInMonth) nextStartValue = maxDaysInMonth;
                
                nextStartInput.value = nextStartValue;
            }
        }

        function syncPreviousRow(inputElement) {
            const currentRow = inputElement.closest('.group');
            const container = document.getElementById('capacityRowsContainer');
            const rows = Array.from(container.children);
            
            const currentIndex = rows.indexOf(currentRow);
            
            if (currentIndex > 0) {
                const prevRow = rows[currentIndex - 1];
                const currentStartValue = parseInt(inputElement.value) || 2;
                
                const prevEndInput = prevRow.querySelector('.cap-end');
                
                let prevEndValue = currentStartValue - 1;
                
                if (prevEndValue < 1) prevEndValue = 1;
                
                prevEndInput.value = prevEndValue;
            }
        }

        function removeCapacityRow(rowId) {
            const rowToRemove = document.getElementById(rowId);
            if (rowToRemove) {
                rowToRemove.remove();
            }

            const container = document.getElementById('capacityRowsContainer');
            const remainingRows = container.children;

            if (remainingRows.length > 0) {
                remainingRows[0].querySelector('.cap-start').value = 1;
                for (let i = 1; i < remainingRows.length; i++) {
                    const prevEnd = parseInt(remainingRows[i-1].querySelector('.cap-end').value) || 0;
                    const currentStartInput = remainingRows[i].querySelector('.cap-start');
                    
                    let newStart = prevEnd + 1;
                    if (newStart > maxDaysInMonth) newStart = maxDaysInMonth;
                    
                    currentStartInput.value = newStart;
                }

                const lastRow = remainingRows[remainingRows.length - 1];
                lastRow.querySelector('.cap-end').value = maxDaysInMonth;
            }
        }

        function closeCapacityModal() {
            document.getElementById('capacityModal').classList.add('hidden');
            resetFileInput();
        }

        function submitCapacity() {
            const container = document.getElementById('capacityRowsContainer');
            const rows = container.children;
            let capacityData = [];
            
            for (let i = 0; i < rows.length; i++) {
                const cap = rows[i].querySelector('.cap-val').value;
                const start = rows[i].querySelector('.cap-start').value;
                const end = rows[i].querySelector('.cap-end').value;
                
                if (cap && start && end) {
                    capacityData.push({ capacity: cap, start: start, end: end });
                }
            }
            
            if (capacityData.length === 0) {
                alert("Harap masukkan minimal 1 data kapasitas!");
                return;
            }

            document.getElementById('capacityDataInput').value = JSON.stringify(capacityData);
            document.getElementById('capacityModal').classList.add('hidden');

            if (globalExists) {
                document.getElementById('modal-message').innerText = globalMessage + " Apakah Anda ingin menimpa data lama?";
                document.getElementById('warningModal').classList.remove('hidden');
            } else {
                document.getElementById('uploadForm').submit();
            }
        }

        function showSheetModal(sheets) {
            const container = document.getElementById('sheetListContainer');
            container.innerHTML = '';
            sheets.forEach(sheetName => {
                let btn = document.createElement('button');
                btn.className = "w-full text-left px-4 py-3 rounded-lg border border-slate-200 hover:bg-blue-50 hover:border-blue-300 hover:text-blue-700 transition flex items-center gap-3";
                btn.innerHTML = `<span class="font-medium">${sheetName}</span>`;
                btn.onclick = function() { selectSheet(sheetName); };
                container.appendChild(btn);
            });
            document.getElementById('sheetModal').classList.remove('hidden');
        }

        function selectSheet(sheetName) {
            document.getElementById('selectedSheetInput').value = sheetName;
            closeSheetModal();
            startProcess();
        }

        function closeSheetModal() { document.getElementById('sheetModal').classList.add('hidden'); }

        function submitManualDate() {
            const m = document.getElementById('inputMonth').value;
            const y = document.getElementById('inputYear').value;
            
            document.getElementById('manualMonthInput').value = m;
            document.getElementById('manualYearInput').value = y;
            
            document.getElementById('dateModal').classList.add('hidden');
            startProcess();
        }

        function closeWarningModal() {
            document.getElementById('warningModal').classList.add('hidden');
            resetFileInput();
        }
        
        function confirmOverwrite() { document.getElementById('uploadForm').submit(); }
    </script>
</body>
</html>
