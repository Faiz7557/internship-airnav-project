<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DailyFlightStat;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Exception;
use PhpOffice\PhpSpreadsheet\IOFactory; 

class UploadController extends Controller
{
    public function index()
    {
        return view('upload');
    }

    public function check(Request $request)
    {
        $request->validate(['file' => 'required|mimes:xls,xlsx']);

        try {
            $file = $request->file('file');
            $filePath = $file->getPathname();
            $selectedSheet = $request->input('sheet_name');
            
            $manualMonth = $request->input('manual_month');
            $manualYear = $request->input('manual_year');

            $reader = IOFactory::createReaderForFile($filePath);
            $reader->setReadDataOnly(true); 

            $allSheets = $reader->listWorksheetNames($filePath);

            if (count($allSheets) > 1 && !$selectedSheet) {
                return response()->json([
                    'status' => 'multiple_sheets',
                    'sheets' => $allSheets,
                    'message' => 'Pilih sheet yang akan diproses.'
                ]);
            }

            $targetSheet = $selectedSheet ?? $allSheets[0];

            $reader->setLoadSheetsOnly($targetSheet);
            $spreadsheet = $reader->load($filePath);
            $rawArray = $spreadsheet->getActiveSheet()->toArray();

            $headerFound = false;
            foreach ($rawArray as $row) {
                if (isset($row[0]) && strcasecmp(trim($row[0]), 'Time') === 0) {
                    $headerFound = true;
                    break;
                }
            }

            if (!$headerFound) {
                return response()->json([
                    'status' => 'invalid_format', 
                    'sheet_used' => $targetSheet,
                    'message' => "Format file pada sheet '$targetSheet' tidak sesuai template. Pastikan ada kolom 'Time'."
                ]);
            }

            $meta = $this->parseMetadata($rawArray);
            
            $tahun = $meta['tahun'];
            $bulan = $meta['bulan'];

            if ($tahun === null || $bulan === null) {
                if ($manualMonth && $manualYear) {
                    $tahun = $manualYear;
                    $bulan = $manualMonth;
                } else {
                    return response()->json([
                        'status' => 'missing_date',
                        'sheet_used' => $targetSheet,
                        'message' => 'Sistem tidak dapat membaca Bulan/Tahun dari file. Silakan masukkan secara manual.'
                    ]);
                }
            }
            
            $exists = DailyFlightStat::whereYear('date', $tahun)
                        ->whereMonth('date', $bulan)
                        ->exists();

            return response()->json([
                'status' => 'success',
                'exists' => $exists,
                'sheet_used' => $targetSheet,
                'message' => $exists 
                    ? "Data periode " . Carbon::create($tahun, $bulan)->format('F Y') . " sudah ada."
                    : "Data aman."
            ]);

        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xls,xlsx',
            'rwy_capacity' => 'required|numeric',
        ]);

        try {
            DB::beginTransaction(); 

            $file = $request->file('file');
            $filePath = $file->getPathname();
            $sheetName = $request->input('sheet_name');
            
            $manualMonth = $request->input('manual_month');
            $manualYear = $request->input('manual_year');

            $reader = IOFactory::createReaderForFile($filePath);
            $reader->setReadDataOnly(true);

            if ($sheetName) {
                $reader->setLoadSheetsOnly($sheetName);
            } else {
                $sheetNames = $reader->listWorksheetNames($filePath);
                $reader->setLoadSheetsOnly($sheetNames[0]);
            }

            $spreadsheet = $reader->load($filePath);
            $rawArray = $spreadsheet->getActiveSheet()->toArray();

            $meta = $this->parseMetadata($rawArray);
            $tahun = $meta['tahun'];
            $bulan = $meta['bulan'];
            $headerRowIndex = $meta['headerIndex'];

            if ($tahun === null || $bulan === null) {
                if ($manualMonth && $manualYear) {
                    $tahun = $manualYear;
                    $bulan = $manualMonth;
                } else {
                    $now = Carbon::now();
                    $tahun = $now->year;
                    $bulan = $now->month;
                }
            }

            $idx_start_hour = $headerRowIndex + 1; $idx_end_hour = $headerRowIndex + 24; 
            $idx_all_arr = $headerRowIndex + 26; $idx_all_dep = $headerRowIndex + 27; 
            $idx_dom_arr = $headerRowIndex + 28; $idx_dom_dep = $headerRowIndex + 29; 
            $idx_int_arr = $headerRowIndex + 30; $idx_int_dep = $headerRowIndex + 31; 
            $idx_trg_arr = $headerRowIndex + 32; $idx_trg_dep = $headerRowIndex + 33; 

            for ($day = 1; $day <= 31; $day++) {
                $colIndex = $day; 
                if (!checkdate($bulan, $day, $tahun)) continue;
                if (!isset($rawArray[$headerRowIndex][$colIndex])) continue; 

                $currentDate = Carbon::createFromDate($tahun, $bulan, $day)->format('Y-m-d');
                $maxMovement = 0; $peakHourLabel = '00:00';

                for ($r = $idx_start_hour; $r <= $idx_end_hour; $r++) {
                    $val = (int) ($rawArray[$r][$colIndex] ?? 0);
                    if ($val > $maxMovement) {
                        $maxMovement = $val;
                        $rawLabel = (string)$rawArray[$r][0]; 
                        $peakHourLabel = str_replace('.', ':', substr($rawLabel, 0, 5));
                    }
                }
                
                $domArr = (int) ($rawArray[$idx_dom_arr][$colIndex] ?? 0);
                $domDep = (int) ($rawArray[$idx_dom_dep][$colIndex] ?? 0);
                $intArr = (int) ($rawArray[$idx_int_arr][$colIndex] ?? 0);
                $intDep = (int) ($rawArray[$idx_int_dep][$colIndex] ?? 0);
                $trainingArr = (int) ($rawArray[$idx_trg_arr][$colIndex] ?? 0);
                $trainingDep = (int) ($rawArray[$idx_trg_dep][$colIndex] ?? 0);
                $totalArr = $domArr + $intArr + $trainingArr;
                $totalDep = $domDep + $intDep + $trainingDep;
                $totalFlights = $totalArr + $totalDep;

                DailyFlightStat::updateOrCreate(
                    ['date' => $currentDate, 'branch_code' => 'WARR'],
                    [
                        'total_dep' => $totalDep, 'total_arr' => $totalArr, 'total_flights' => $totalFlights,
                        'dom_dep' => $domDep, 'dom_arr' => $domArr,
                        'int_dep' => $intDep, 'int_arr' => $intArr,
                        'training_dep' => $trainingDep, 'training_arr' => $trainingArr,
                        'peak_hour' => $peakHourLabel, 'peak_hour_count' => $maxMovement,
                        'runway_capacity' => $request->rwy_capacity
                    ]
                );
            }

            DB::commit(); 
            return redirect()->route('summary', [
                'month' => $bulan, 
                'year' => $tahun
            ])->with('success', 'Data berhasil diproses.');

        } catch (Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    private function parseMetadata($rawArray)
    {
        $tahun = null; $bulan = null; $headerRowIndex = null;
        
        foreach ($rawArray as $index => $row) {
            $rowString = implode(' ', array_map('strval', $row));
            
            if (!$bulan && preg_match('/(Januari|January|Februari|February|Maret|March|April|Mei|May|Juni|June|Juli|July|Agustus|August|September|Oktober|October|November|December|Desember)\s+(\d{4})/i', $rowString, $matches)) {
                $bulanNama = $matches[1];
                $tahun = $matches[2];
                $bulan = $this->convertMonthToNumber($bulanNama);
            }
            
            if (isset($row[0]) && strcasecmp(trim($row[0]), 'Time') === 0) {
                $headerRowIndex = $index;
            }
            
            if ($headerRowIndex !== null && $bulan !== null) break;
        }

        return ['tahun' => $tahun, 'bulan' => $bulan, 'headerIndex' => $headerRowIndex];
    }

    private function convertMonthToNumber($monthName)
    {
        $months = [
            'januari' => 1, 'january' => 1, 
            'februari' => 2, 'february' => 2, 
            'maret' => 3, 'march' => 3, 
            'april' => 4, 
            'mei' => 5, 'may' => 5, 
            'juni' => 6, 'june' => 6, 
            'juli' => 7, 'july' => 7, 
            'agustus' => 8, 'august' => 8, 
            'september' => 9, 
            'oktober' => 10, 'october' => 10, 
            'november' => 11, 
            'desember' => 12, 'december' => 12
        ];
        return $months[strtolower($monthName)] ?? null;
    }
}