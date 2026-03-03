<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\DashboardFilterRequest;
use App\Models\DailyFlightStat;
use App\Models\DailyNote; // Added this import
use App\Models\Cabang; // Added this import
use App\Models\Event; // Added this import
use App\Services\DashboardService; // Added this import
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(DashboardFilterRequest $request, \App\Services\DashboardService $dashboardService)
    {
        $query = DailyFlightStat::query();

        // Filter Logic
        $isFiltered = false;
        
        $reqYear = $request->filled('year') ? $request->integer('year') : null;
        $reqMonth = $request->filled('month') ? $request->integer('month') : null;
        $reqBranch = $request->filled('branch') ? $request->input('branch') : null;

        $cabangs = \App\Models\Cabang::all();

        if ($reqBranch) {
            $query->where('branch_code', $reqBranch);
            $isFiltered = true;
        }

        // Fallback: If only Month is selected without Year, safely default to the latest existing year.
        if ($reqMonth && !$reqYear) {
            $latestStat = DailyFlightStat::orderBy('date', 'desc')->first();
            if ($latestStat) {
                $reqYear = Carbon::parse($latestStat->date)->year;
            } else {
                $reqYear = Carbon::now()->year;
            }
        }

        if ($reqYear) {
            $year = $reqYear;
            $query->whereYear('date', $year);
            $isFiltered = true;
            
            if ($reqMonth) {
                $month = $reqMonth;
                $query->whereMonth('date', $month);
                $labelType = 'daily';
            } else {
                $month = null;
                $labelType = 'monthly'; // Force monthly mode for this specific year
            }
        } else {
            $month = null;
            $year = null;
            $labelType = 'monthly';
        }

        $dailyStats = $query->orderBy('date')->get();

        // Available Filter Options
        $datesQuery = DailyFlightStat::select(DB::raw('YEAR(date) as year, MONTH(date) as month'))
            ->distinct()
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc');
            
        if ($reqBranch) {
            $datesQuery->where('branch_code', $reqBranch);
        }
        $availableDates = $datesQuery->get()->groupBy('year');

        // Data Range for Visualization based on filtered data
        $minDate = $dailyStats->min('date');
        $maxDate = $dailyStats->max('date');
        $dataRange = $minDate && $maxDate 
            ? Carbon::parse($minDate)->locale('id')->translatedFormat('d F Y') . ' hingga ' . Carbon::parse($maxDate)->locale('id')->translatedFormat('d F Y') 
            : '-';

        // --- PULL EVENTS FROM DATABASE ---
        $rawEvents = \App\Models\Event::all();

        $events = [];
        foreach ($rawEvents as $ev) {
            // Calculate Stats for each event
            $statsQuery = DailyFlightStat::whereBetween('date', [$ev->start_date, $ev->end_date]);
            if ($reqBranch) {
                $statsQuery->where('branch_code', $reqBranch);
            }
            $stats = $statsQuery->get();
            
            if ($stats->count() > 0) {
                // Calculation Logic
                $totalVal = $stats->sum('total_flights');
                $avgVal = round($stats->avg('total_flights'));
                $peakNode = $stats->sortByDesc('total_flights')->first();
                $peakVal = $peakNode ? $peakNode->total_flights : 0;
                $peakDate = $peakNode ? Carbon::parse($peakNode->date)->format('d M Y') : '-';

                $events[] = [
                    'id' => $ev->id,
                    'name' => $ev->name,
                    'start' => Carbon::parse($ev->start_date)->format('Y-m-d'),
                    'end' => Carbon::parse($ev->end_date)->format('Y-m-d'),
                    'color' => $ev->color . '1A', // 10% opacity for background
                    'borderColor' => $ev->color . '66', // 40% opacity for border
                    
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
        if ($highestPeakNode && $highestPeakNode->peak_hour) {
            $carbonPeak = Carbon::parse($highestPeakNode->date . ' ' . $highestPeakNode->peak_hour)->addHours(7);
            $highestPeak = [
                'time' => $carbonPeak->format('H:i') . ' (' . $carbonPeak->format('d/m') . ')',
                'date' => $carbonPeak->format('d M Y'), // Synchronized specific date
                'count' => $highestPeakNode->peak_hour_count
            ];
        } else {
            $highestPeak = ['time' => '-', 'date' => '-', 'count' => 0];
        }

        // 2. Traffic Trend (Line Chart) & 6. Arr/Dep Split (Prepared here for sync)
        $monthlyArr = [];
        $monthlyDep = [];
        
        if ($isFiltered && $labelType === 'daily') {
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
                // Initialize array of objects {x, y}. x = 1..366 (day of year)
                $dayData = [];
                foreach ($yearData as $stat) {
                    $dateObj = Carbon::parse($stat->date);
                    $dayOfYear = $dateObj->dayOfYear;
                    $dayData[] = [
                        'x' => $dayOfYear,
                        'y' => $stat->total_flights
                    ];
                }
                $yearlyComparisonRaw[$y] = $dayData;
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
            $hourInt = $stat->peak_hour ? (int)substr($stat->peak_hour, 0, 2) : 0;
            $hourUTC7 = ($hourInt + 7) % 24;
            $hour = sprintf('%02d:00', $hourUTC7);
            
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
        
        // a. Capacity Utilization (Avg Daily Flights / (Runway Capacity * 19 hours))
        // Jam operasional Surabaya: 22.00 - 17.00 UTC = 19 Jam
        $avgDailyTotal = $dailyStats->count() > 0 ? $totalFlights / $dailyStats->count() : 0;
        $avgHourlyCapacity = $dailyStats->avg('runway_capacity') ?? 0;
        $dailyCapacity = $avgHourlyCapacity * 19; 
        $capacityUtilization = ($dailyCapacity > 0) ? round(($avgDailyTotal / $dailyCapacity) * 100, 1) : 0;

        // b. Growth Metrics (Current vs Previous)
        // ... (existing code)

        // 9. Hourly Profile Simulation (Drill-Down Data)
        // Uses smart estimation logic from DashboardService to accurately shift profiles 
        // to match the specific UTC+7 Peak Hour for each day of the week.
        $hourlyProfiles = $dashboardService->generateHourlyProfiles($dailyStats);

        // b. Growth Metrics (Current vs Previous)
        $growthPercentage = 0;
        $previousTotal = 0;
        $activeTotal = $totalFlights; // Default to calculated total

        if ($isFiltered) {
            $prevYear = $year - 1;
            if ($month) {
                // Compare Same Month Last Year
                $prevQuery = DailyFlightStat::whereMonth('date', $month)->whereYear('date', $prevYear);
                if ($reqBranch) $prevQuery->where('branch_code', $reqBranch);
                $previousTotal = $prevQuery->sum('total_flights');
            } else {
                // Compare Same Year Last Year
                $prevQuery = DailyFlightStat::whereYear('date', $prevYear);
                if ($reqBranch) $prevQuery->where('branch_code', $reqBranch);
                $previousTotal = $prevQuery->sum('total_flights');
            }
        } else {
            // All Time View: Compare Latest Full Year vs Previous Year
            $maxYearQuery = DailyFlightStat::query();
            if ($reqBranch) $maxYearQuery->where('branch_code', $reqBranch);
            $maxYearInDB = $maxYearQuery->max(DB::raw('YEAR(date)')) ?? Carbon::now()->year;

            $currQuery = DailyFlightStat::whereYear('date', $maxYearInDB);
            if ($reqBranch) $currQuery->where('branch_code', $reqBranch);
            $currYearTotal = $currQuery->sum('total_flights');

            $prevQuery = DailyFlightStat::whereYear('date', $maxYearInDB - 1);
            if ($reqBranch) $prevQuery->where('branch_code', $reqBranch);
            $previousTotal = $prevQuery->sum('total_flights');
            
            // Override displayed Total to show meaningful "Current Performance" instead of All Time Sum
            // Only update the $totalFlights variable if we want the KPI card to show "This Year" instead of "All Time"
            $activeTotal = $currYearTotal;
            $dataRange .= " (Fokus: $maxYearInDB)"; 
        }

        if ($previousTotal > 0) {
            $growthPercentage = round((($activeTotal - $previousTotal) / $previousTotal) * 100, 1);
        } else if ($activeTotal > 0) {
            $growthPercentage = 100;
        }

        // c. Training Impact
        $totalTra = $totalTraining; 
        $trainingImpact = ($totalFlights > 0) ? round(($totalTra / $totalFlights) * 100, 1) : 0;

        // 10. HEATMAP DATA (Full History for Client-Side Filtering)
        // Fetch all daily stats to allow dynamic year switching on frontend
        
        // Eager load RawFlightData to avoid N+1 queries by prefetching all dates
        $rawQuery = \App\Models\RawFlightData::query();
        if ($reqBranch) $rawQuery->where('kode_cabang', $reqBranch);
        $allRawData = $rawQuery->get()->keyBy('date');
        
        $heatmapQuery = DailyFlightStat::select('date', 'total_flights', 'peak_hour_count', 'peak_hour', 'dom_arr', 'dom_dep', 'int_arr', 'int_dep', 'training_arr', 'training_dep');
        if ($reqBranch) $heatmapQuery->where('branch_code', $reqBranch);
        
        // Fetch Notes
        $notesQuery = DailyNote::query();
        if ($reqBranch) $notesQuery->where('branch_code', $reqBranch);
        $allNotes = $notesQuery->get()->keyBy('date');

        $heatmapData = $heatmapQuery->orderBy('date')
            ->get()
            ->map(function($item) use ($allRawData, $allNotes, $reqBranch) {
                // Fetch the actual hourly array for this specific date
                $rawRecord = $allRawData->get($item->date);
                $hourlyArray = array_fill(0, 24, 0);
                if ($rawRecord) {
                    for ($h = 0; $h < 24; $h++) {
                        $hourCol = 'h' . str_pad($h, 2, '0', STR_PAD_LEFT);
                        $hourlyArray[$h] = (int) $rawRecord->{$hourCol};
                    }
                }

                return [
                    'date' => $item->date, // YYYY-MM-DD
                    'value' => $item->total_flights,
                    'peak_count' => $item->peak_hour_count ?? 0,
                    'peak_hour' => $item->peak_hour ? (int)substr($item->peak_hour, 0, 2) : 0,
                    'dom' => $item->dom_arr + $item->dom_dep,
                    'int' => $item->int_arr + $item->int_dep,
                    'training' => $item->training_arr + $item->training_dep,
                    'year' => Carbon::parse($item->date)->year, // Helper for JS filtering
                    'month' => Carbon::parse($item->date)->month, // Helper for JS filtering
                    'hourly_data' => $hourlyArray, // Actual data for drill-down
                    'note' => $allNotes->has($item->date) ? $allNotes->get($item->date)->note : '',
                    'branch_code' => $reqBranch ?? 'WARR' // Default to Surabaya if global
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
        $currentYearQuery = DailyFlightStat::whereYear('date', $targetYear);
        if ($reqBranch) $currentYearQuery->where('branch_code', $reqBranch);
        $totalCurrentYear = $currentYearQuery->sum('total_flights');

        $prevYearQuery = DailyFlightStat::whereYear('date', $prevYear);
        if ($reqBranch) $prevYearQuery->where('branch_code', $reqBranch);
        $totalPrevYear = $prevYearQuery->sum('total_flights');
        
        $growthTotal      = ($totalPrevYear > 0) ? round((($totalCurrentYear - $totalPrevYear) / $totalPrevYear) * 100, 1) : ($totalCurrentYear > 0 ? 100 : 0);

        // B. Peak Day Traffic
        $peakDayCurrentQuery = DailyFlightStat::whereYear('date', $targetYear);
        if ($reqBranch) $peakDayCurrentQuery->where('branch_code', $reqBranch);
        $peakDayCurrent = $peakDayCurrentQuery->max('total_flights') ?? 0;

        $peakDayPrevQuery = DailyFlightStat::whereYear('date', $prevYear);
        if ($reqBranch) $peakDayPrevQuery->where('branch_code', $reqBranch);
        $peakDayPrev = $peakDayPrevQuery->max('total_flights') ?? 0;
        
        $growthPeak     = ($peakDayPrev > 0) ? round((($peakDayCurrent - $peakDayPrev) / $peakDayPrev) * 100, 1) : ($peakDayCurrent > 0 ? 100 : 0);

        // C. Average Daily Flights
        $avgCurrentQuery = DailyFlightStat::whereYear('date', $targetYear);
        if ($reqBranch) $avgCurrentQuery->where('branch_code', $reqBranch);
        $avgCurrent = $avgCurrentQuery->avg('total_flights') ?? 0;

        $avgPrevQuery = DailyFlightStat::whereYear('date', $prevYear);
        if ($reqBranch) $avgPrevQuery->where('branch_code', $reqBranch);
        $avgPrev = $avgPrevQuery->avg('total_flights') ?? 0;
        
        $growthAvg  = ($avgPrev > 0) ? round((($avgCurrent - $avgPrev) / $avgPrev) * 100, 1) : ($avgCurrent > 0 ? 100 : 0);

        // Bug 1 Fix: Calculate MoM Stats for comparison cards
        // Compare current month vs previous month (or same month last year if month is Jan)
        $momStats = ['current_peak' => 0, 'peak_growth' => 0, 'current_avg' => 0, 'avg_growth' => 0];
        if ($isFiltered && $month && $year) {
            $prevMonthDate  = Carbon::create($year, $month, 1)->subMonth();
            $prevMonthQuery = DailyFlightStat::whereYear('date', $prevMonthDate->year)
                ->whereMonth('date', $prevMonthDate->month);
            if ($reqBranch) $prevMonthQuery->where('branch_code', $reqBranch);
            $prevMonthStats = $prevMonthQuery->get();

            $currentPeak = $dailyStats->max('peak_hour_count') ?? 0;
            $prevPeak    = $prevMonthStats->max('peak_hour_count') ?? 0;
            $currentAvg  = $dailyStats->count() > 0 ? round($dailyStats->avg('total_flights')) : 0;
            $prevAvg     = $prevMonthStats->count() > 0 ? round($prevMonthStats->avg('total_flights')) : 0;

            $momStats = [
                'current_peak' => $currentPeak,
                'peak_growth'  => $prevPeak > 0 ? round((($currentPeak - $prevPeak) / $prevPeak) * 100, 1) : ($currentPeak > 0 ? 100 : 0),
                'current_avg'  => $currentAvg,
                'avg_growth'   => $prevAvg > 0 ? round((($currentAvg - $prevAvg) / $prevAvg) * 100, 1) : ($currentAvg > 0 ? 100 : 0),
            ];
        } else {
            // Unfiltered or Year-Only: compare latest month in DB vs previous month
            // If Year-only, grab the latest month in that specific year. If unfiltered, grab the absolute latest month.
            $latestDateQuery = $isFiltered ? DailyFlightStat::whereYear('date', $year) : DailyFlightStat::query();
            if ($reqBranch) $latestDateQuery->where('branch_code', $reqBranch);
            $latestDate = $latestDateQuery->max('date');
            
            if ($latestDate) {
                $latestCarbon   = Carbon::parse($latestDate);
                $prevMonthDate  = $latestCarbon->copy()->subMonth();
                
                $currMonthQuery = DailyFlightStat::whereYear('date', $latestCarbon->year)->whereMonth('date', $latestCarbon->month);
                if ($reqBranch) $currMonthQuery->where('branch_code', $reqBranch);
                $currMonthStats = $currMonthQuery->get();
                
                $prevMonthQuery = DailyFlightStat::whereYear('date', $prevMonthDate->year)->whereMonth('date', $prevMonthDate->month);
                if ($reqBranch) $prevMonthQuery->where('branch_code', $reqBranch);
                $prevMonthStats = $prevMonthQuery->get();

                $currentPeak = $currMonthStats->max('peak_hour_count') ?? 0;
                $prevPeak    = $prevMonthStats->max('peak_hour_count') ?? 0;
                $currentAvg  = $currMonthStats->count() > 0 ? round($currMonthStats->avg('total_flights')) : 0;
                $prevAvg     = $prevMonthStats->count() > 0 ? round($prevMonthStats->avg('total_flights')) : 0;

                $momStats = [
                    'current_peak' => $currentPeak,
                    'peak_growth'  => $prevPeak > 0 ? round((($currentPeak - $prevPeak) / $prevPeak) * 100, 1) : ($currentPeak > 0 ? 100 : 0),
                    'current_avg'  => $currentAvg,
                    'avg_growth'   => $prevAvg > 0 ? round((($currentAvg - $prevAvg) / $prevAvg) * 100, 1) : ($currentAvg > 0 ? 100 : 0),
                ];
            }
        }

        return view('dashboard', compact(
            'month', 'year', 'cabangs', 'reqBranch', 'availableDates', 'isFiltered', 'dataRange', 'labelType',
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

    public function saveNote(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'branch_code' => 'required|string',
            'note' => 'nullable|string'
        ]);

        DailyNote::updateOrCreate(
            ['date' => $request->date, 'branch_code' => $request->branch_code],
            ['note' => $request->note]
        );

        return response()->json(['success' => true, 'message' => 'Catatan berhasil disimpan']);
    }
}
