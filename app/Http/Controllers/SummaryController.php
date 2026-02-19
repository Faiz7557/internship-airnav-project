<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DailyFlightStat;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class SummaryController extends Controller
{
    public function index()
    {
        $rawDates = DailyFlightStat::select(DB::raw('YEAR(date) as year, MONTH(date) as month'))
            ->distinct()
            ->orderBy('year', 'desc')
            ->get();

        $availableDates = $rawDates->groupBy('month')->map(function ($items) {
            return $items->pluck('year')->values();
        });

        return view('summary', compact('availableDates'));
    }

    public function getData(Request $request)
    {
        $month = $request->month;
        $year = $request->year;

        $data = DailyFlightStat::whereYear('date', $year)
                    ->whereMonth('date', $month)
                    ->orderBy('date', 'asc')
                    ->get();

        if ($data->isEmpty()) {
            return response()->json(['status' => 'empty']);
        }

        $labels = [];
        $peakMovementData = [];
        $rwyCapacityData = [];
        $trafficTotal = [];
        $trafficDep = [];
        $trafficArr = [];
        
        $peakHourFrequency = [];
        for ($i = 0; $i < 24; $i++) {
            $hourKey = str_pad($i, 2, '0', STR_PAD_LEFT) . ':00';
            $peakHourFrequency[$hourKey] = 0;
        }

        foreach ($data as $row) {
            $dateLabel = Carbon::parse($row->date)->format('d'); 
            $labels[] = $dateLabel;

            $peakMovementData[] = $row->peak_hour_count;
            $rwyCapacityData[] = $row->runway_capacity; 

            $trafficTotal[] = $row->total_flights;
            $trafficDep[] = $row->total_dep;
            $trafficArr[] = $row->total_arr;

            $hour = substr($row->peak_hour, 0, 5); 
            if (isset($peakHourFrequency[$hour])) {
                $peakHourFrequency[$hour]++;
            }
        }

        return response()->json([
            'status' => 'success',
            'table_data' => $data, 
            'charts' => [
                'labels' => $labels,
                'peak_movement' => $peakMovementData,
                'rwy_cap' => $rwyCapacityData,
                'traffic_total' => $trafficTotal,
                'traffic_dep' => $trafficDep,
                'traffic_arr' => $trafficArr,
                'peak_hour_keys' => array_keys($peakHourFrequency),
                'peak_hour_values' => array_values($peakHourFrequency),
            ]
        ]);
    }
    
    public function exportExcel(Request $request)
    {
        $month = $request->input('month');
        $year = $request->input('year');
        $type = $request->input('type', 'all'); 

        $data = DailyFlightStat::whereYear('date', $year)
                ->whereMonth('date', $month)
                ->orderBy('date', 'asc')
                ->get();
        
        if ($data->isEmpty()) {
            return redirect()->back()->with('error', 'Tidak ada data untuk diexport.');
        }

        $peakHours = [];
        for ($i = 0; $i < 24; $i++) {
            $k = str_pad($i, 2, '0', STR_PAD_LEFT) . ':00';
            $peakHours[$k] = 0;
        }
        foreach ($data as $row) {
            $hour = substr($row->peak_hour, 0, 5); 
            if (isset($peakHours[$hour])) $peakHours[$hour]++;
        }

        $spreadsheet = new Spreadsheet();
        $spreadsheet->removeSheetByIndex(0);

        if ($type == 'all' || $type == 'table') {
            $this->createTablesSheet($spreadsheet, $data, $peakHours);
        }

        if ($type == 'all' || $type == 'peak') {
            $this->createChartSheet($spreadsheet, 'Grafik Movement', $request->input('chart_peak_img'), 'Chart Peak Movement');
        }

        if ($type == 'all' || $type == 'traffic') {
            $this->createChartSheet($spreadsheet, 'Grafik Dep-Arr', $request->input('chart_traffic_img'), 'Chart Traffic');
        }

        if ($type == 'all' || $type == 'tabulation') {
            $this->createChartSheet($spreadsheet, 'Grafik Peak Hours', $request->input('chart_tabulation_img'), 'Chart Tabulation');
        }

        $spreadsheet->setActiveSheetIndex(0);

        $namaBulan = Carbon::create($year, $month)->format('F_Y');
        if ($type == 'table') {
            $fileName = 'Tabel_Movement_' . $namaBulan . '.xlsx';
        } 
        elseif ($type == 'peak') {
            $fileName = 'Grafik_Movement_' . $namaBulan . '.xlsx';
        } 
        elseif ($type == 'traffic') {
            $fileName = 'Grafik_Dep_Arr_' . $namaBulan . '.xlsx';
        } 
        elseif ($type == 'tabulation') {
            $fileName = 'Grafik_Peak_Hour_' . $namaBulan . '.xlsx';
        } 
        else {
            $fileName = 'Laporan_Lengkap_' . $namaBulan . '.xlsx';
        }

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $fileName . '"');
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    private function createTablesSheet($spreadsheet, $data, $peakData)
    {
        $sheet = $spreadsheet->createSheet();
        $sheet->setTitle('Movement TWR-AFIS');

        $headers1 = ['Tanggal', 'Dep', 'Arr', 'Total', 'Jam Peak', 'Jml Peak', 'Rwy Cap', 'Cabang'];
        $sheet->fromArray($headers1, null, 'A1', true); 
        
        $rows1 = [];
        foreach ($data as $item) {
            $rows1[] = [
                Carbon::parse($item->date)->format('d/m/Y'),
                $item->total_dep, $item->total_arr, $item->total_flights,
                $item->peak_hour, $item->peak_hour_count,
                $item->runway_capacity, $item->branch_code
            ];
        }
        $sheet->fromArray($rows1, null, 'A2', true); 
        $lastRow1 = count($rows1) + 1;
        $this->applyTableStyle($sheet, 'A1:H'.$lastRow1);

        $headers2 = ['Jam (UTC)', 'Frekuensi Kejadian'];
        $sheet->fromArray($headers2, null, 'J1', true); 
        
        $rows2 = [];
        foreach ($peakData as $hour => $count) {
            $rows2[] = [$hour, $count];
        }
        $sheet->fromArray($rows2, null, 'J2', true); 
        $lastRow2 = count($rows2) + 1;
        $this->applyTableStyle($sheet, 'J1:K'.$lastRow2);
    }

    private function createChartSheet($spreadsheet, $sheetTitle, $imageBase64, $imageName)
    {
        $sheet = $spreadsheet->createSheet();
        $sheet->setTitle($sheetTitle);
        
        $sheet->setShowGridlines(false); 

        $this->insertChartImage($sheet, $imageBase64, 'B2', $imageName);
    }

    private function insertChartImage($sheet, $base64String, $coordinates, $name)
    {
        if ($base64String && strpos($base64String, 'base64,') !== false) {
            $image_parts = explode(";base64,", $base64String);
            $image_base64 = base64_decode($image_parts[1]);

            $tempImage = tempnam(sys_get_temp_dir(), 'chart_') . '.png';
            file_put_contents($tempImage, $image_base64);

            $drawing = new Drawing();
            $drawing->setName($name);
            $drawing->setDescription($name);
            $drawing->setPath($tempImage);
            $drawing->setCoordinates($coordinates);
            $drawing->setHeight(450); 
            $drawing->setWorksheet($sheet);
        }
    }

    public function exportPDF(Request $request)
    {
        $month = $request->input('month');
        $year = $request->input('year');
        
        $data = DailyFlightStat::whereYear('date', $year)
                ->whereMonth('date', $month)
                ->orderBy('date', 'asc')
                ->get();

        if ($data->isEmpty()) {
            return redirect()->back()->with('error', 'Tidak ada data untuk diexport.');
        }

        $peakHours = [];
        for ($i = 0; $i < 24; $i++) {
            $k = str_pad($i, 2, '0', STR_PAD_LEFT) . ':00';
            $peakHours[$k] = 0;
        }
        foreach ($data as $row) {
            $hour = substr($row->peak_hour, 0, 5);
            if (isset($peakHours[$hour])) $peakHours[$hour]++;
        }

        $chartPeak = $request->input('chart_peak_img');
        $chartTraffic = $request->input('chart_traffic_img');
        $chartTabulation = $request->input('chart_tabulation_img');

        $namaBulan = Carbon::create($year, $month)->format('F_Y');
        $fileName = 'Laporan_PDF_' . $namaBulan . '.pdf';

        $pdf = app('dompdf.wrapper');
        $pdf->loadView('pdf.summary_report', compact(
            'data', 'peakHours', 'month', 'year', 'namaBulan',
            'chartPeak', 'chartTraffic', 'chartTabulation'
        ));

        $pdf->setPaper('A4', 'portrait');

        return $pdf->download($fileName);
    }

    private function applyTableStyle($sheet, $range)
    {
        $sheet->getStyle($range)->applyFromArray([
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER
            ]
        ]);

        $parts = explode(':', $range); 
        $startCol = preg_replace('/[0-9]/', '', $parts[0]);
        $endCol = preg_replace('/[0-9]/', '', $parts[1]);
        
        $firstRow = $parts[0] . ':' . $endCol . '1';

        $sheet->getStyle($firstRow)->getFont()->setBold(true);
        $sheet->getStyle($firstRow)->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FFEEEEEE');

        $currCol = $startCol;
        while ($currCol !== $endCol) {
            $sheet->getColumnDimension($currCol)->setAutoSize(true);
            $currCol++;
        }
        $sheet->getColumnDimension($endCol)->setAutoSize(true);
    }
}