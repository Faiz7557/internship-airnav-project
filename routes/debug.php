<?php

use Illuminate\Support\Facades\Route;
use App\Models\DailyFlightStat;
use Illuminate\Support\Facades\DB;

Route::get('/debug-data', function () {
    try {
        $count = DailyFlightStat::count();
        $first = DailyFlightStat::first();
        return response()->json([
            'count' => $count,
            'first_record' => $first,
            'db_name' => DB::connection()->getDatabaseName()
        ]);
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()]);
    }
});
