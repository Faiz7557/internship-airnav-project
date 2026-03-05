<!DOCTYPE html>
<html>
<head>
    <title>Laporan Bulanan AirNav</title>
    <style>
        @page {
            margin: 2.5cm 1.5cm 2cm 1.5cm;
        }

        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 10pt;
            color: #333;
        }

        header {
            position: fixed;
            top: -2cm;
            left: 0px;
            right: 0px;
            height: 2cm;
        }

        table.kop-surat {
            border: none;
            border-radius: 0;
            margin-bottom: 5px;
        }

        table.kop-surat td {
            border: none;
            padding: 0;
        }

        .kop-logo {
            text-align: left;
            width: 60%;
            vertical-align: middle;
            padding-bottom: 5px;
        }

        .kop-logo img {
            height: 65px;
            width: auto;
            vertical-align: baseline;
            margin-right: 12px;
        }

        .logo-text {
            font-size: 12pt;
            vertical-align: baseline;
            color: #000;
        }

        .kop-info {
            text-align: left;
            width: 15%;
            vertical-align: bottom;
            font-size: 7pt;
            line-height: 1.1;
            color: #000;
            padding-bottom: 2.2px;
            white-space: nowrap;
        }

        h2 {
            text-align: center;
            color: #1F3C88;
            margin-bottom: 2px;
            font-size: 14pt;
        }

        h3 {
            color: #1F3C88;
            font-size: 11pt;
            margin-bottom: 5px;
            margin-top: 10px;
        }

        p {
            text-align: center;
            margin-top: 0;
            color: #666;
            font-size: 9pt;
        }
        
        table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            margin-bottom: 15px;
            font-size: 8pt;
            border: 1px solid #cbd5e1;
            border-radius: 10px;
            overflow: hidden;
        }

        th, td {
            border-bottom: 1px solid #e2e8f0;
            padding: 6px;
            text-align: center;
        }

        th {
            background-color: #1F3C88;
            color: #ffffff;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 8pt;
            letter-spacing: 0.5px;
        }

        tr:nth-child(even) {
            background-color: #f8fafc;
        }

        .table-peak {
            width: 60%;
            margin-left: auto;
            margin-right: auto;
        }

        .highlight-total {
            background-color: #eff6ff;
            color: #1d4ed8;
            font-weight: bold;
        }

        .chart-container {
            text-align: center;
            margin-bottom: 12px;
        }

        .chart-img {
            max-width: 100%;
            height: 240px;
            width: auto;
            border: 1px solid #eee;
            padding: 5px;
            border-radius: 8px;
        }
        
        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body>

    @php
        $branchCode = $data->first()->branch_code ?? 'WARR';
        $branchNamesMap = \App\Models\Cabang::pluck('nama', 'kode_cabang')->toArray();
        
        $namaCabang = $branchNamesMap[$branchCode] ?? $branchCode;
        $periodeBulan = str_replace('_', ' ', $namaBulan);
    @endphp
    <header>
        <table class="kop-surat">
            <tr>
                <td style="width: 60%; vertical-align: bottom;">
                    <table style="border: none; width: auto; margin: 0; padding: 0;">
                        <tr>
                            <td style="border: none; padding: 0 3px 0 0; vertical-align: bottom;">
                                <img src="{{ public_path('img/logo_airnav.png') }}" style="width: 66.4px; height: auto; display: block;">
                            </td>
                            <td style="border: none; padding: 0 0 5px 0; vertical-align: bottom;">
                                <span style="font-size: 14pt; color: #000; line-height: 0.8; display: block; position: relative; top: 4px;">AirNav Indonesia</span>
                            </td>
                        </tr>
                    </table>
                </td>
                <td class="kop-info">
                    <span style="display: inline-block; text-align: left; font-size: 7pt; line-height: 1.1; color: #000; vertical-align: bottom;">
                        CABANG SURABYA<br>
                        Gedung AOB Bandara Juanda<br>
                        Jl. Juanda no : 1<br>
                        Sedati, Sidoarjo 61253<br>
                        Telp : (031) 2986515<br>
                        <span style="line-height: 0.8; display: block;">email : sekgmsub.airnav@gmail.com</span>
                    </span>
                </td>
            </tr>
        </table>
    </header>

    <main>
        <h2>Laporan Data Pergerakan Pesawat</h2>
        <p>Periode: {{ $periodeBulan }} (Cabang {{ $namaCabang }})</p>

        <h3>1. Data Daily Movement</h3>
        <table>
            <thead>
                <tr>
                    <th>Tgl</th>
                    <th>Dep</th>
                    <th>Arr</th>
                    <th>Total</th>
                    <th>Jam Peak</th>
                    <th>Jml Peak</th>
                    <th>Rwy Cap</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data as $row)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($row->date)->format('d') }}</td>
                    <td>{{ $row->total_dep }}</td>
                    <td>{{ $row->total_arr }}</td>
                    <td class="highlight-total">{{ $row->total_flights }}</td>
                    <td>{{ $row->peak_hour }}</td>
                    <td>{{ $row->peak_hour_count }}</td>
                    <td>{{ $row->runway_capacity }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="page-break"></div>

        <br>
        <h3>2. Data Frekuensi Peak Hour</h3>
        <br>
        <table class="table-peak">
            <thead>
                <tr>
                    <th>Jam (UTC)</th>
                    <th>Frekuensi Kejadian</th>
                </tr>
            </thead>
            <tbody>
                @foreach($peakHours as $jam => $freq)
                <tr>
                    <td><strong>{{ $jam }}</strong></td>
                    <td>{{ $freq }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="page-break"></div>

        <h2>Visualisasi Grafik</h2>

        <div class="chart-container">
            <img src="{{ $chartPeak }}" class="chart-img">
        </div>

        <div class="chart-container">
            <img src="{{ $chartTraffic }}" class="chart-img">
        </div>

        <div class="chart-container">
            <img src="{{ $chartTabulation }}" class="chart-img">
        </div>
    </main>

</body>
</html>
