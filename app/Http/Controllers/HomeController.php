<?php

namespace App\Http\Controllers;

use App\Models\DailyFlightStat;
use App\Models\Cabang;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    // 1. Tambahkan Request $request di sini
    public function index(Request $request)
    {
        $today = Carbon::now();
        $selectedCabang = $request->input('kode_cabang', 'WARR');
        $cabangs = Cabang::all();
        $latestData = DailyFlightStat::where('branch_code', $selectedCabang)->max('date');
        
        if (!$latestData) {
            return view('home', [
                'total' => 0,
                'growth' => 0,
                'dom_pct' => 0,
                'int_pct' => 0,
                'jam_sibuk' => '-',
                'runway_status' => 'No Data',
                'runway_color' => 'text-gray-400',
                'stress_level' => 0,
                'nama_bulan' => $today->translatedFormat('F Y'),
                'rata_harian' => 0,
                'rekor_penerbangan' => 0,
                'tanggal_rekor' => '-',
                'cabangs' => $cabangs,
                'selectedCabang' => $selectedCabang
            ]);
        }

        $referenceDate = Carbon::parse($latestData);
        $currentMonth = $referenceDate->month;
        $currentYear = $referenceDate->year;

        $namaBulanIni = $referenceDate->translatedFormat('F Y');

        $prevDate = $referenceDate->copy()->subMonth();
        $prevMonth = $prevDate->month;
        $prevYear = $prevDate->year;

        $currentTotal = DailyFlightStat::where('branch_code', $selectedCabang)
                                       ->whereMonth('date', $currentMonth)
                                       ->whereYear('date', $currentYear)
                                       ->sum('total_flights');

        $prevTotal = DailyFlightStat::where('branch_code', $selectedCabang)
                                    ->whereMonth('date', $prevMonth)
                                    ->whereYear('date', $prevYear)
                                    ->sum('total_flights');

        $growth = 0;
        if ($prevTotal > 0) {
            $growth = (($currentTotal - $prevTotal) / $prevTotal) * 100;
        } else {
            $growth = 100;
        }

        $domTotal = DailyFlightStat::where('branch_code', $selectedCabang)
                                   ->whereMonth('date', $currentMonth)
                                   ->whereYear('date', $currentYear)
                                   ->sum(DB::raw('dom_arr + dom_dep'));
                                   
        $intTotal = DailyFlightStat::where('branch_code', $selectedCabang)
                                   ->whereMonth('date', $currentMonth)
                                   ->whereYear('date', $currentYear)
                                   ->sum(DB::raw('int_arr + int_dep'));

        $grandTotal = $domTotal + $intTotal;
        
        $domPct = ($grandTotal > 0) ? round(($domTotal / $grandTotal) * 100) : 0;
        $intPct = ($grandTotal > 0) ? round(($intTotal / $grandTotal) * 100) : 0;

        $jamSibukData = DailyFlightStat::where('branch_code', $selectedCabang)
                                       ->whereMonth('date', $currentMonth)
                                       ->whereYear('date', $currentYear)
                                       ->select('peak_hour', DB::raw('count(*) as total'))
                                       ->groupBy('peak_hour')
                                       ->orderByDesc('total')
                                       ->first();

        $jamSibuk = $jamSibukData ? Carbon::parse($jamSibukData->peak_hour)->format('H:i') : '-';

        $daysCount = DailyFlightStat::where('branch_code', $selectedCabang)
                                    ->whereMonth('date', $currentMonth)
                                    ->whereYear('date', $currentYear)
                                    ->count();

        $rataHarian = ($daysCount > 0) ? round($currentTotal / $daysCount) : 0;
        $busiestDay = DailyFlightStat::where('branch_code', $selectedCabang)
                                     ->whereMonth('date', $currentMonth)
                                     ->whereYear('date', $currentYear)
                                     ->orderBy('total_flights', 'desc')
                                     ->first();
        
        $rekorPenerbangan = $busiestDay ? $busiestDay->total_flights : 0;
        $tanggalRekor = $busiestDay ? Carbon::parse($busiestDay->date)->format('d M') : '-';
        
        if ($daysCount > 0 && $currentTotal > 0) {
            $avgFlightPerHour = $currentTotal / $daysCount / 24;
            
            $rwyCap = DailyFlightStat::where('branch_code', $selectedCabang)
                                     ->whereMonth('date', $currentMonth)
                                     ->whereYear('date', $currentYear)
                                     ->value('runway_capacity');
            
            $stressLevel = ($rwyCap > 0) ? ($avgFlightPerHour / $rwyCap) * 100 : 0;
        } else {
            $stressLevel = 0;
        }

        $runwayStatus = 'Normal';
        $runwayColor = 'text-green-400';

        if ($stressLevel > 80) {
            $runwayStatus = 'Critical';
            $runwayColor = 'text-red-500';
        } elseif ($stressLevel > 60) {
            $runwayStatus = 'Heavy';
            $runwayColor = 'text-yellow-400';
        }

        return view('home', [
            'total' => number_format($currentTotal),
            'growth' => round($growth, 1),
            'dom_pct' => $domPct,
            'int_pct' => $intPct,
            'jam_sibuk' => $jamSibuk,
            'runway_status' => $runwayStatus,
            'runway_color' => $runwayColor,
            'stress_level' => round($stressLevel, 1),
            'nama_bulan' => $namaBulanIni,
            'rata_harian' => $rataHarian,
            'rekor_penerbangan' => $rekorPenerbangan,
            'tanggal_rekor' => $tanggalRekor,
            'cabangs' => $cabangs,
            'selectedCabang' => $selectedCabang
        ]);
    }
}
