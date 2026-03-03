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
     * Generate Hourly Profiles using Actual Data from RawFlightData
     */
    public function generateHourlyProfiles($dailyStats)
    {
        $hourlyProfiles = [];
        
        // Initialize the structure with 24 hours of 0s for each day of the week
        // 1=Mon, 2=Tue, 3=Wed, 4=Thu, 5=Fri, 6=Sat, 7=Sun
        for ($i = 1; $i <= 7; $i++) {
            $hourlyProfiles[$i] = [
                'total_hours' => array_fill(0, 24, 0),
                'day_count' => 0
            ];
        }

        // Get the specific dates we are filtering by
        $dates = $dailyStats->pluck('date')->toArray();
        if (empty($dates)) {
            // Return empty profile layout if no dates exist
            $emptyProfiles = [];
            for ($i = 1; $i <= 7; $i++) {
                $emptyProfiles[$i] = array_fill(0, 24, 0);
            }
            return $emptyProfiles;
        }
        
        // Fetch actual hourly data directly from RawFlightData
        $rawRecords = \App\Models\RawFlightData::whereIn('date', $dates)->get();

        foreach ($rawRecords as $record) {
            $dayNum = Carbon::parse($record->date)->dayOfWeekIso; // 1-7
            $hourlyProfiles[$dayNum]['day_count']++;

            for ($h = 0; $h < 24; $h++) {
                $hourCol = 'h' . str_pad($h, 2, '0', STR_PAD_LEFT);
                $hourlyProfiles[$dayNum]['total_hours'][$h] += (int) $record->{$hourCol};
            }
        }

        // Calculate Average and Format for Return
        $finalProfiles = [];
        for ($i = 1; $i <= 7; $i++) {
            $count = $hourlyProfiles[$i]['day_count'];
            $averageHours = array_fill(0, 24, 0);
            
            if ($count > 0) {
                for ($h = 0; $h < 24; $h++) {
                    // Average it out and round to nearest whole flight representation
                    $averageHours[$h] = (int) round($hourlyProfiles[$i]['total_hours'][$h] / $count);
                }
            }
            $finalProfiles[$i] = $averageHours;
        }

        return $finalProfiles;
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
        } else if ($currentTotal > 0) {
            return 100;
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

        return compact('avgDailyFlights', 'busiestDay', 'highestPeak');
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
