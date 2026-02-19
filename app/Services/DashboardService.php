<?php

namespace App\Services;

use App\Models\DailyFlightStat;
use App\Models\Event;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardService
{
    /**
     * Get Query Builder with Filters
     */
    public function getFilteredQuery($month, $year)
    {
        $query = DailyFlightStat::query();
        if ($month && $year) {
            $query->whereMonth('date', $month)->whereYear('date', $year);
        }
        return $query;
    }

    /**
     * Get Events overlapping with the selected year
     */
    public function getEvents($year = null)
    {
        $eventQuery = Event::query();
        if ($year) {
            $eventQuery->whereYear('start_date', $year)
                       ->orWhereYear('end_date', $year);
        }
        $rawEvents = $eventQuery->get();

        if ($rawEvents->isEmpty()) {
            return [];
        }

        // Optimization: Fetch all potential stats in one go
        $starts = $rawEvents->pluck('start_date');
        $ends = $rawEvents->pluck('end_date');
        $minDate = $starts->min();
        $maxDate = $ends->max();

        // Fetch all stats within the total range
        $allStats = DailyFlightStat::whereBetween('date', [$minDate, $maxDate])
            ->get()
            ->groupBy(function($item) {
                return $item->date;
            }); // Keyed by Y-m-d

        $events = [];
        foreach ($rawEvents as $ev) {
            // Filter stats for this specific event from the memory collection
            $startDate = $ev->start_date->format('Y-m-d');
            $endDate = $ev->end_date->format('Y-m-d');

            $eventStats = $allStats->filter(function($stat, $date) use ($startDate, $endDate) {
                return $date >= $startDate && $date <= $endDate;
            })->flatten();

            if ($eventStats->isNotEmpty()) {
                $totalVal = $eventStats->sum('total_flights');
                $avgVal = round($eventStats->avg('total_flights')); 
                $peakNode = $eventStats->sortByDesc('total_flights')->first();
                $peakVal = $peakNode ? $peakNode->total_flights : 0;
                $peakDate = $peakNode ? Carbon::parse($peakNode->date)->format('d M Y') : '-';

                $events[] = [
                    'name' => $ev->name,
                    'start' => $startDate,
                    'end' => $endDate,
                    'color' => $ev->color . '1A', 
                    'borderColor' => $ev->color . '66',
                    'total' => number_format($totalVal),
                    'avg' => number_format($avgVal),
                    'peak' => number_format($peakVal),
                    'peakDate' => $peakDate
                ];
            }
        }
        return $events;
    }

    /**
     * Generate Hourly Profiles using Smart Estimation
     */
    public function generateHourlyProfiles($dailyStats)
    {
        $dailyProfiles = [];
        foreach ($dailyStats as $stat) {
            $dayNum = Carbon::parse($stat->date)->dayOfWeekIso; // 1-7

            if (!isset($dailyProfiles[$dayNum])) {
                $dailyProfiles[$dayNum] = [
                    'total_flights' => 0,
                    'peak_count' => 0,
                    'count' => 0,
                    'peak_hour_sum' => 0
                ];
            }
            $dailyProfiles[$dayNum]['total_flights'] += $stat->total_flights;
            $dailyProfiles[$dayNum]['peak_count'] += $stat->peak_hour_count;
            $dailyProfiles[$dayNum]['peak_hour_sum'] += (int)substr($stat->peak_hour, 0, 2);
            $dailyProfiles[$dayNum]['count']++;
        }

        // Standard Profiles
        $weekdayProfile = [
            0=>1, 1=>1, 2=>1, 3=>1, 4=>2, 5=>5, 
            6=>15, 7=>20, 8=>18, 9=>12, 10=>10, 11=>10, 
            12=>10, 13=>11, 14=>12, 15=>16, 16=>18, 17=>15, 
            18=>10, 19=>8, 20=>6, 21=>4, 22=>3, 23=>2
        ];
        $weekendProfile = [
            0=>2, 1=>1, 2=>1, 3=>1, 4=>2, 5=>4, 
            6=>8, 7=>12, 8=>15, 9=>18, 10=>20, 11=>20, 
            12=>19, 13=>18, 14=>16, 15=>14, 16=>12, 17=>10, 
            18=>8, 19=>6, 20=>5, 21=>4, 22=>3, 23=>2
        ];

        $hourlyProfiles = [];
        for ($i = 1; $i <= 7; $i++) {
            $profileStats = $dailyProfiles[$i] ?? ['total_flights' => 0, 'peak_count' => 0, 'count' => 0, 'peak_hour_sum' => 0];
            
            if ($profileStats['count'] == 0) {
                $hourlyProfiles[$i] = array_fill(0, 24, 0);
                continue;
            }

            $avgTotal = $profileStats['total_flights'] / $profileStats['count'];
            $avgPeakVal = $profileStats['peak_count'] / $profileStats['count'];
            $avgPeakHour = round($profileStats['peak_hour_sum'] / $profileStats['count']);

            $baseProfile = ($i >= 6) ? $weekendProfile : $weekdayProfile;
            
            // Shift
            $baseMaxWeight = max($baseProfile);
            $baseMaxHour = array_search($baseMaxWeight, $baseProfile);
            $shift = $avgPeakHour - $baseMaxHour;

            $shiftedProfile = [];
            for ($h = 0; $h < 24; $h++) {
                $sourceH = ($h - $shift + 24) % 24;
                $shiftedProfile[$h] = $baseProfile[$sourceH];
            }

            // Scale
            $currentSum = array_sum($shiftedProfile);
            $finalProfile = [];
            foreach ($shiftedProfile as $h => $w) {
                $val = ($w / $currentSum) * $avgTotal;
                $finalProfile[$h] = round($val);
            }
            $finalProfile[$avgPeakHour] = round($avgPeakVal);
            $hourlyProfiles[$i] = $finalProfile;
        }
        return $hourlyProfiles;
    }

    /**
     * Calculate Year-over-Year Growth
     */
    public function calculateGrowth($currentTotal, $targetYear, $month = null, $isFiltered = false)
    {
        $prevYear = $targetYear - 1;
        $previousTotal = 0;

        if ($isFiltered && $month) {
             $previousTotal = DailyFlightStat::whereMonth('date', $month)->whereYear('date', $prevYear)->sum('total_flights');
        } else {
             $previousTotal = DailyFlightStat::whereYear('date', $prevYear)->sum('total_flights');
        }

        if ($previousTotal > 0) {
            return round((($currentTotal - $previousTotal) / $previousTotal) * 100, 1);
        }
        return 0;
    }

    /**
     * Calculate KPI stats
     */
    public function getKpiStats($dailyStats, $isFiltered, $totalFlights)
    {
        $daysCount = $dailyStats->count(); 
        $avgDailyFlights = $daysCount > 0 ? round($totalFlights / $daysCount) : 0;
        
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

        $totalTra = $dailyStats->sum(fn($d) => $d->training_arr + $d->training_dep);
        $trainingImpact = ($totalFlights > 0) ? round(($totalTra / $totalFlights) * 100, 1) : 0;

        $avgPeakCount = $dailyStats->avg('peak_hour_count');
        $avgCapacity = $dailyStats->avg('runway_capacity'); 
        $capacityUtilization = ($avgCapacity > 0) ? round(($avgPeakCount / $avgCapacity) * 100, 1) : 0;

        return compact('avgDailyFlights', 'busiestDay', 'highestPeak', 'trainingImpact', 'capacityUtilization');
    }

    /**
     * Prepare Chart Data
     */
    public function prepareChartData($dailyStats, $isFiltered)
    {
        if ($isFiltered) {
            // Daily View
            $chartTrend = [
                'labels' => $dailyStats->map(fn($d) => Carbon::parse($d->date)->format('d'))->values(),
                'data' => $dailyStats->pluck('total_flights'),
                'dom' => $dailyStats->map(fn($d) => $d->dom_arr + $d->dom_dep)->values(),
                'int' => $dailyStats->map(fn($d) => $d->int_arr + $d->int_dep)->values(),
                'training' => $dailyStats->map(fn($d) => $d->training_arr + $d->training_dep)->values(),
            ];

            $chartArrDep = [
                'labels' => $chartTrend['labels'],
                'arr' => $dailyStats->pluck('total_arr'),
                'dep' => $dailyStats->pluck('total_dep'),
            ];
            
            return compact('chartTrend', 'chartArrDep');
        } 
        
        // Monthly Aggregation View
        $monthlyStats = $dailyStats->groupBy(function($d) {
            return Carbon::parse($d->date)->format('M Y');
        });

        $trendLabels = [];
        $trendData = [];
        $monthlyDom = [];
        $monthlyInt = [];
        $monthlyTraining = [];
        $monthlyArr = [];
        $monthlyDep = [];

        foreach ($monthlyStats as $key => $group) {
            $trendLabels[] = $key;
            $trendData[] = $group->sum('total_flights');
            
            $monthlyDom[] = (int) $group->sum(fn($d) => $d->dom_arr + $d->dom_dep);
            $monthlyInt[] = (int) $group->sum(fn($d) => $d->int_arr + $d->int_dep);
            $monthlyTraining[] = (int) $group->sum(fn($d) => $d->training_arr + $d->training_dep);

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

        $chartArrDep = [
            'labels' => $trendLabels,
            'arr' => $monthlyArr,
            'dep' => $monthlyDep,
        ];

        return compact('chartTrend', 'chartArrDep');
    }

    public function getYearlyComparison()
    {
        $yearlyComparison = [];
        $yearsData = DailyFlightStat::selectRaw('YEAR(date) as year, MONTH(date) as month, SUM(total_flights) as total')
            ->groupBy('year', 'month')
            ->get()
            ->groupBy('year');

        foreach ($yearsData as $y => $stats) {
            $monthlyData = array_fill(1, 12, 0);
            foreach ($stats as $stat) {
                $monthlyData[$stat->month] = (int)$stat->total;
            }
            $yearlyComparison[$y] = array_values($monthlyData);
        }
        return $yearlyComparison;
    }
}
