<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\DashboardFilterRequest;
use App\Models\DailyFlightStat;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(DashboardFilterRequest $request)
    {
        $query = DailyFlightStat::query();

        // Filter Logic
        if ($request->filled('month') && $request->filled('year')) {
            $month = $request->integer('month');
            $year = $request->integer('year');
            $query->whereMonth('date', $month)->whereYear('date', $year);
            $isFiltered = true;
            $labelType = 'daily';
        } else {
            $month = null;
            $year = null;
            $isFiltered = false;
            $labelType = 'monthly';
        }

        $dailyStats = $query->orderBy('date')->get();

        // Available Filter Options
        $availableDates = DailyFlightStat::select(DB::raw('YEAR(date) as year, MONTH(date) as month'))
            ->distinct()
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->get()
            ->groupBy('year');

        // Data Range for Visualization
        $minDate = DailyFlightStat::min('date');
        $maxDate = DailyFlightStat::max('date');
        $dataRange = $minDate && $maxDate 
            ? Carbon::parse($minDate)->format('d M Y') . ' - ' . Carbon::parse($maxDate)->format('d M Y') 
            : '-';

        // --- DEFINE SEASONAL EVENTS (Mockup for Demo) ---
        // In a real app, this would come from an 'events' table
        // --- DEFINE SEASONAL EVENTS (Dynamic Calculation) ---
        $rawEvents = [
            // 2023
            ['name' => 'Angkutan Lebaran 2023', 'start' => '2023-04-14', 'end' => '2023-05-02', 'color' => '#10b981'], // ~H-8 to H+7 (Idul Fitri ~Apr 22)
            ['name' => 'Angkutan Nataru 2023/24', 'start' => '2023-12-19', 'end' => '2024-01-04', 'color' => '#f43f5e'],
            
            // 2024
            ['name' => 'Angkutan Lebaran 2024', 'start' => '2024-04-03', 'end' => '2024-04-18', 'color' => '#10b981'], // ~H-7 to H+7 (Idul Fitri ~Apr 10)
            ['name' => 'Angkutan Nataru 2024/25', 'start' => '2024-12-19', 'end' => '2025-01-04', 'color' => '#f43f5e'],

            // 2025 (Projected)
            ['name' => 'Angkutan Lebaran 2025', 'start' => '2025-03-24', 'end' => '2025-04-08', 'color' => '#10b981'], // Idul Fitri ~Mar 31
            ['name' => 'Angkutan Nataru 2025/26', 'start' => '2025-12-19', 'end' => '2026-01-04', 'color' => '#f43f5e'],

             // 2026 (Projected)
            ['name' => 'Angkutan Lebaran 2026', 'start' => '2026-03-13', 'end' => '2026-03-28', 'color' => '#10b981'] // Idul Fitri ~Mar 20
        ];

        $events = [];
        foreach ($rawEvents as $ev) {
            // Calculate Stats for each event
            $stats = DailyFlightStat::whereBetween('date', [$ev['start'], $ev['end']])->get();
            
            if ($stats->count() > 0) {
                // Calculation Logic
                $totalVal = $stats->sum('total_flights');
                $avgVal = round($stats->avg('total_flights'));
                $peakNode = $stats->sortByDesc('total_flights')->first();
                $peakVal = $peakNode ? $peakNode->total_flights : 0;
                $peakDate = $peakNode ? Carbon::parse($peakNode->date)->format('d M Y') : '-';

                $events[] = [
                    'name' => $ev['name'],
                    'start' => $ev['start'],
                    'end' => $ev['end'],
                    'color' => $ev['color'] . '1A', // 10% opacity for background
                    'borderColor' => $ev['color'] . '66', // 40% opacity for border
                    
                    // Pre-calculated Data for Modal
                    'total' => number_format($totalVal),
                    'avg' => number_format($avgVal),
                    'peak' => number_format($peakVal),
                    'peakDate' => $peakDate
                ];
            }
        }

        // 1. KPI Cards Calculation
        $totalFlights = $dailyStats->sum('total_flights');
        
        // Avg Daily Flights logic differs slightly based on context
        if ($isFiltered) {
            $daysCount = $dailyStats->count();
            $avgDailyFlights = $daysCount > 0 ? round($totalFlights / $daysCount) : 0;
        } else {
            // If all time, average per month might be more meaningful, but let's stick to daily avg for consistency
            $daysCount = $dailyStats->count(); 
            $avgDailyFlights = $daysCount > 0 ? round($totalFlights / $daysCount) : 0;
        }
        
        $busiestDayNode = $dailyStats->sortByDesc('total_flights')->first();
        $busiestDay = $busiestDayNode ? [
            'date' => Carbon::parse($busiestDayNode->date)->format('d M Y'),
            'count' => $busiestDayNode->total_flights
        ] : ['date' => '-', 'count' => 0];

        $highestPeakNode = $dailyStats->sortByDesc('peak_hour_count')->first();
        $highestPeak = $highestPeakNode ? [
            'time' => Carbon::parse($highestPeakNode->peak_hour)->format('H:i') . ' (' . Carbon::parse($highestPeakNode->date)->format('d/m') . ')',
            'count' => $highestPeakNode->peak_hour_count
        ] : ['time' => '-', 'count' => 0];

        // 2. Traffic Trend (Line Chart) & 6. Arr/Dep Split (Prepared here for sync)
        $monthlyArr = [];
        $monthlyDep = [];
        
        if ($isFiltered) {
            // Daily View
            $chartTrend = [
                'labels' => $dailyStats->map(fn($d) => Carbon::parse($d->date)->format('d'))->values(),
                'data' => $dailyStats->pluck('total_flights'),
                'dom' => $dailyStats->map(fn($d) => $d->dom_arr + $d->dom_dep)->values(),
                'int' => $dailyStats->map(fn($d) => $d->int_arr + $d->int_dep)->values(),
                'training' => $dailyStats->map(fn($d) => $d->training_arr + $d->training_dep)->values(),
            ];

            // 6. Arrival vs Departure Split (Daily)
            $chartArrDep = [
                'labels' => $chartTrend['labels'],
                'arr' => $dailyStats->pluck('total_arr'),
                'dep' => $dailyStats->pluck('total_dep'),
            ];
            
            $yearlyComparison = null; 

        } else {
            // Monthly Aggregation View
            $monthlyStats = $dailyStats->groupBy(function($d) {
                return Carbon::parse($d->date)->format('M Y');
            });

            $trendLabels = [];
            $trendData = [];
            $monthlyDom = [];
            $monthlyInt = [];
            $monthlyTraining = [];
            $monthlyArr = []; // Clear for safety
            $monthlyDep = [];

            foreach ($monthlyStats as $key => $group) {
                $trendLabels[] = $key;
                $trendData[] = $group->sum('total_flights');
                
                // Collect Category sums
                $monthlyDom[] = (int) $group->sum(fn($d) => $d->dom_arr + $d->dom_dep);
                $monthlyInt[] = (int) $group->sum(fn($d) => $d->int_arr + $d->int_dep);
                $monthlyTraining[] = (int) $group->sum(fn($d) => $d->training_arr + $d->training_dep);

                // Collect Arr/Dep sums here to ensure perfect alignment with labels
                $monthlyArr[] = (int) $group->sum('total_arr');
                $monthlyDep[] = (int) $group->sum('total_dep');
            }

            $chartTrend = [
                'labels' => $trendLabels,
                'data' => $trendData,
                'dom' => $monthlyDom,
                'int' => $monthlyInt,
                'training' => $monthlyTraining
            ];

            // 6. Arrival vs Departure Split (Monthly)
            $chartArrDep = [
                'labels' => $trendLabels,
                'arr' => $monthlyArr,
                'dep' => $monthlyDep,
            ];

            // 7. Yearly Comparison (Seasonality Chart) - Only for "All Time"
            $yearlyComparisonRaw = [];
            $years = $dailyStats->groupBy(fn($d) => Carbon::parse($d->date)->year);
            
            foreach ($years as $y => $yearData) {
                // Initialize 12 months with 0
                $monthlyData = array_fill(1, 12, 0);
                
                foreach ($yearData as $stat) {
                    $m = Carbon::parse($stat->date)->month;
                    $monthlyData[$m] += $stat->total_flights;
                }
                $yearlyComparisonRaw[$y] = array_values($monthlyData);
            }
            // Bug 6 Fix: Only show when >=2 years of data exist
            $yearlyComparison = count($yearlyComparisonRaw) >= 2 ? $yearlyComparisonRaw : null;
        }

        // 3. Category Distribution (Donut Charts)
        $totalDom = $dailyStats->sum(fn($d) => $d->dom_arr + $d->dom_dep);
        $totalInt = $dailyStats->sum(fn($d) => $d->int_arr + $d->int_dep);
        $totalTraining = $dailyStats->sum(fn($d) => $d->training_arr + $d->training_dep);

        $chartCategory = [
            'dom' => $totalDom,
            'int' => $totalInt,
            'training' => $totalTraining
        ];

        // 4. Peak Hour Distribution (Bar Chart)
        $peakHourfreq = [];
        foreach ($dailyStats as $stat) {
            $hour = $stat->peak_hour ? substr($stat->peak_hour, 0, 5) : '00:00';
            if (!isset($peakHourfreq[$hour])) $peakHourfreq[$hour] = 0;
            $peakHourfreq[$hour]++;
        }
        ksort($peakHourfreq);

        // 5. Day of Week Analysis (Robust Numeric)
        $dayOfWeekStats = array_fill(1, 7, 0); // 1=Mon, 7=Sun
        $dayOfWeekCounts = array_fill(1, 7, 0);
        
        // Breakdown Initialization
        $dayOfWeekDom = array_fill(1, 7, 0);
        $dayOfWeekInt = array_fill(1, 7, 0);
        $dayOfWeekTraining = array_fill(1, 7, 0);

        foreach ($dailyStats as $stat) {
            $dayNum = Carbon::parse($stat->date)->dayOfWeekIso; // 1 (Mon) - 7 (Sun)
            $dayOfWeekStats[$dayNum] += $stat->total_flights;
            $dayOfWeekCounts[$dayNum]++;

            // Sum Breakdown
            $dayOfWeekDom[$dayNum] += ($stat->dom_arr + $stat->dom_dep);
            $dayOfWeekInt[$dayNum] += ($stat->int_arr + $stat->int_dep);
            $dayOfWeekTraining[$dayNum] += ($stat->training_arr + $stat->training_dep);
        }

        // Calculate Average
        foreach ($dayOfWeekStats as $day => $total) {
            $count = $dayOfWeekCounts[$day];
            if ($count > 0) {
                $dayOfWeekStats[$day] = round($total / $count, 1);
                $dayOfWeekDom[$day] = round($dayOfWeekDom[$day] / $count, 1);
                $dayOfWeekInt[$day] = round($dayOfWeekInt[$day] / $count, 1);
                $dayOfWeekTraining[$day] = round($dayOfWeekTraining[$day] / $count, 1);
            }
        }
        // Map back to labels for frontend
        $dayLabels = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];
        $dayOfWeekData = array_values($dayOfWeekStats); // 0=Mon data, 6=Sun data
        // Bug 3 Fix: Keep 1-indexed keys (1=Mon..7=Sun) so JS drill-down (dayIndex+1) matches
        $dayOfWeekComposition = [
            'dom'      => $dayOfWeekDom,
            'int'      => $dayOfWeekInt,
            'training' => $dayOfWeekTraining
        ];

        // 8. Advanced Diagnostics
        
        // a. Capacity Utilization (Avg Peak / Runway Capacity)
        $avgPeakCount = $dailyStats->avg('peak_hour_count');
        $avgCapacity = $dailyStats->avg('runway_capacity'); 
        $capacityUtilization = ($avgCapacity > 0) ? round(($avgPeakCount / $avgCapacity) * 100, 1) : 0;

        // b. Growth Metrics (Current vs Previous)
        // ... (existing code)

        // 9. Hourly Profile Simulation (Drill-Down Data)
        // Since we don't have raw hourly logs, we simulate based on typical airport profiles
        // Weekday: Business peaks (Morning 06-09, Afternoon 16-18)
        // Weekend: Leisure peaks (Mid-day 10-14)
        
        $hourlyProfiles = [];
        for ($i = 1; $i <= 7; $i++) {
            $dailyTotal = $dayOfWeekStats[$i] ?? 0;
            $profile = [];
            
            // Define weights for each hour (00-23)
            if ($i <= 5) { // Weekday (Mon-Fri)
                $weights = [
                    2, 1, 1, 1, 2, 8, 15, 20, 18, 12, 10, 10, 10, 11, 12, 16, 18, 15, 10, 8, 6, 4, 3, 2
                ];
            } else { // Weekend (Sat-Sun)
                $weights = [
                    2, 1, 1, 1, 1, 4, 8, 12, 15, 18, 20, 20, 19, 18, 16, 14, 12, 10, 8, 6, 5, 4, 3, 2
                ];
            }

            // Normalize and distribute dailyTotal
            $totalWeight = array_sum($weights);
            foreach ($weights as $w) {
                // Add some randomness +/- 10%
                $noise = rand(90, 110) / 100; 
                $val = ($dailyTotal * ($w / $totalWeight)) * $noise;
                $profile[] = round($val);
            }
            $hourlyProfiles[$i] = $profile;
        }

        // b. Growth Metrics (Current vs Previous)
        $growthPercentage = 0;
        $previousTotal = 0;
        $activeTotal = $totalFlights; // Default to calculated total

        if ($isFiltered) {
            // Compare Same Month Last Year
            $prevYear = $year - 1;
            $prevMonthStats = DailyFlightStat::whereMonth('date', $month)->whereYear('date', $prevYear)->sum('total_flights');
            $previousTotal = $prevMonthStats;
        } else {
            // All Time View: Compare Latest Full Year vs Previous Year
            $maxYearInDB = DailyFlightStat::max(DB::raw('YEAR(date)'));
            $currYearTotal = DailyFlightStat::whereYear('date', $maxYearInDB)->sum('total_flights');
            $previousTotal = DailyFlightStat::whereYear('date', $maxYearInDB - 1)->sum('total_flights');
            
            // Override displayed Total to show meaningful "Current Performance" instead of All Time Sum
            // Only update the $totalFlights variable if we want the KPI card to show "This Year" instead of "All Time"
            $activeTotal = $currYearTotal;
            $dataRange .= " (Fokus: $maxYearInDB)"; 
        }

        if ($previousTotal > 0) {
            $growthPercentage = round((($activeTotal - $previousTotal) / $previousTotal) * 100, 1);
        }

        // c. Training Impact
        $totalTra = $totalTraining; 
        $trainingImpact = ($totalFlights > 0) ? round(($totalTra / $totalFlights) * 100, 1) : 0;

        // 10. HEATMAP DATA (Full History for Client-Side Filtering)
        // Fetch all daily stats to allow dynamic year switching on frontend
        $heatmapData = DailyFlightStat::select('date', 'total_flights')
            ->orderBy('date')
            ->get()
            ->map(function($item) {
                return [
                    'date' => $item->date, // YYYY-MM-DD
                    'value' => $item->total_flights,
                    'year' => Carbon::parse($item->date)->year, // Helper for JS filtering
                    'month' => Carbon::parse($item->date)->month // Helper for JS filtering
                ];
            });

        // --- INSIGHTS CALCULATION ---

        // 1. Dominant Category
        $maxCatVal = max($totalDom, $totalInt, $totalTraining);
        $maxCatName = $totalDom == $maxCatVal ? 'Domestik' : ($totalInt == $maxCatVal ? 'Internasional' : 'Training');
        $domCatPct = $totalFlights > 0 ? round(($maxCatVal / $totalFlights) * 100, 1) : 0;
        $dominantInsight = "$maxCatName mendominasi $domCatPct% traffic";

        // 2. Weekend vs Weekday
        $weekDayTotal = 0; $weekDayCount = 0;
        $weekendTotal = 0; $weekendCount = 0;
        // Map: 1=Mon...5=Fri (Weekday), 6=Sat, 7=Sun (Weekend)
        foreach ($dayOfWeekStats as $day => $avg) {
            if ($day >= 6) { $weekendTotal += $avg; $weekendCount++; }
            else { $weekDayTotal += $avg; $weekDayCount++; }
        }
        $avgWeekend = $weekendCount > 0 ? $weekendTotal / $weekendCount : 0;
        $avgWeekday = $weekDayCount > 0 ? $weekDayTotal / $weekDayCount : 0;
        $weekendDiff = $avgWeekday > 0 ? round((($avgWeekend - $avgWeekday) / $avgWeekday) * 100, 1) : 0;

        // 3. Arr vs Dep Balance
        $totalArr = $dailyStats->sum('total_arr');
        $totalDep = $dailyStats->sum('total_dep');
        $arrPct = $totalFlights > 0 ? round(($totalArr / $totalFlights) * 100) : 0;
        $depPct = $totalFlights > 0 ? round(($totalDep / $totalFlights) * 100) : 0;
        $balanceInsight = "$arrPct% Arr / $depPct% Dep";


        // 11. SEASONAL GROWTH ANALYSIS (YoY)
        $targetYear = $year ?? Carbon::now()->year;
        $prevYear = $targetYear - 1;

        // A. Total Flights
        $totalCurrentYear = DailyFlightStat::whereYear('date', $targetYear)->sum('total_flights');
        $totalPrevYear    = DailyFlightStat::whereYear('date', $prevYear)->sum('total_flights');
        $growthTotal      = ($totalPrevYear > 0) ? round((($totalCurrentYear - $totalPrevYear) / $totalPrevYear) * 100, 1) : 0;

        // B. Peak Day Traffic
        $peakDayCurrent = DailyFlightStat::whereYear('date', $targetYear)->max('total_flights') ?? 0;
        $peakDayPrev    = DailyFlightStat::whereYear('date', $prevYear)->max('total_flights') ?? 0;
        $growthPeak     = ($peakDayPrev > 0) ? round((($peakDayCurrent - $peakDayPrev) / $peakDayPrev) * 100, 1) : 0;

        // C. Average Daily Flights
        $avgCurrent = DailyFlightStat::whereYear('date', $targetYear)->avg('total_flights') ?? 0;
        $avgPrev    = DailyFlightStat::whereYear('date', $prevYear)->avg('total_flights') ?? 0;
        $growthAvg  = ($avgPrev > 0) ? round((($avgCurrent - $avgPrev) / $avgPrev) * 100, 1) : 0;

        // Bug 1 Fix: Calculate MoM Stats for comparison cards
        // Compare current month vs previous month (or same month last year if month is Jan)
        $momStats = ['current_peak' => 0, 'peak_growth' => 0, 'current_avg' => 0, 'avg_growth' => 0];
        if ($isFiltered && $month && $year) {
            $prevMonthDate  = Carbon::create($year, $month, 1)->subMonth();
            $prevMonthStats = DailyFlightStat::whereYear('date', $prevMonthDate->year)
                ->whereMonth('date', $prevMonthDate->month)->get();

            $currentPeak = $dailyStats->max('peak_hour_count') ?? 0;
            $prevPeak    = $prevMonthStats->max('peak_hour_count') ?? 0;
            $currentAvg  = $dailyStats->count() > 0 ? round($dailyStats->avg('total_flights')) : 0;
            $prevAvg     = $prevMonthStats->count() > 0 ? round($prevMonthStats->avg('total_flights')) : 0;

            $momStats = [
                'current_peak' => $currentPeak,
                'peak_growth'  => $prevPeak > 0 ? round((($currentPeak - $prevPeak) / $prevPeak) * 100, 1) : 0,
                'current_avg'  => $currentAvg,
                'avg_growth'   => $prevAvg > 0 ? round((($currentAvg - $prevAvg) / $prevAvg) * 100, 1) : 0,
            ];
        } else {
            // Unfiltered: compare latest month in DB vs previous month
            $latestDate    = DailyFlightStat::max('date');
            if ($latestDate) {
                $latestCarbon   = Carbon::parse($latestDate);
                $prevMonthDate  = $latestCarbon->copy()->subMonth();
                $currMonthStats = DailyFlightStat::whereYear('date', $latestCarbon->year)
                    ->whereMonth('date', $latestCarbon->month)->get();
                $prevMonthStats = DailyFlightStat::whereYear('date', $prevMonthDate->year)
                    ->whereMonth('date', $prevMonthDate->month)->get();

                $currentPeak = $currMonthStats->max('peak_hour_count') ?? 0;
                $prevPeak    = $prevMonthStats->max('peak_hour_count') ?? 0;
                $currentAvg  = $currMonthStats->count() > 0 ? round($currMonthStats->avg('total_flights')) : 0;
                $prevAvg     = $prevMonthStats->count() > 0 ? round($prevMonthStats->avg('total_flights')) : 0;

                $momStats = [
                    'current_peak' => $currentPeak,
                    'peak_growth'  => $prevPeak > 0 ? round((($currentPeak - $prevPeak) / $prevPeak) * 100, 1) : 0,
                    'current_avg'  => $currentAvg,
                    'avg_growth'   => $prevAvg > 0 ? round((($currentAvg - $prevAvg) / $prevAvg) * 100, 1) : 0,
                ];
            }
        }

        return view('dashboard', compact(
            'month', 'year', 'availableDates', 'isFiltered', 'dataRange', 'labelType',
            'totalFlights', 'avgDailyFlights', 'busiestDay', 'highestPeak',
            'chartTrend', 'chartCategory', 'peakHourfreq', 'dayLabels', 'dayOfWeekData', 'chartArrDep', 'yearlyComparison',
            'capacityUtilization', 'growthPercentage', 'trainingImpact',
            'dominantInsight', 'weekendDiff', 'balanceInsight',
            'hourlyProfiles', 'events', 'heatmapData',
            // New Seasonal Metrics
            'totalCurrentYear', 'growthTotal', 'totalPrevYear',
            'peakDayCurrent', 'growthPeak', 
            'avgCurrent', 'growthAvg',
            'prevYear',
            'dayOfWeekComposition',
            // MoM Comparison Cards
            'momStats'
        ));
    }
}

