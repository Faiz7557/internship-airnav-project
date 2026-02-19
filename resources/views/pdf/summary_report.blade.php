<!DOCTYPE html>
<html>
<head>
    <title>Laporan Bulanan AirNav</title>
    <style>
        @page {
            margin: 2cm;
        }

        body {
            font-family: sans-serif;
            font-size: 10pt;
            color: #333;
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
            margin-left: 0;
        }

        .highlight-total {
            background-color: #eff6ff;
            color: #1d4ed8;
            font-weight: bold;
        }

        .table-peak {
            width: 60%;
            margin-left: 0;
        }

        .chart-container {
            text-align: center;
            margin-bottom: 30px;
        }

        .chart-img {
            width: 100%;
            height: auto;
            border: 1px solid #eee;
            padding: 5px;
        }
        
        .page-break {
            page-break-after: always;
        }
        
        .footer {
            position: fixed;
            bottom: -10px;
            left: 0;
            right: 0;
            font-size: 8pt;
            text-align: right;
            color: #aaa;
        }
    </style>
</head>
<body>

    <h2>Laporan Data Pergerakan Pesawat</h2>
    <p>Periode: {{ $namaBulan }} (Cabang Surabaya)</p>

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

    <h3>2. Data Frekuensi Peak Hour</h3>
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
    <br>

    <div class="chart-container">
        <img src="{{ $chartPeak }}" class="chart-img">
    </div>

    <div class="chart-container">
        <img src="{{ $chartTraffic }}" class="chart-img">
    </div>

    <div class="chart-container">
        <img src="{{ $chartTabulation }}" class="chart-img">
    </div>

</body>
</html>