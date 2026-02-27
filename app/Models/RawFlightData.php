<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RawFlightData extends Model
{
    use HasFactory;
    protected $table = 'raw_flight_datas';

    protected $fillable = [
        'date', 'kode_cabang',
        'h00', 'h01', 'h02', 'h03', 'h04', 'h05', 'h06', 'h07',
        'h08', 'h09', 'h10', 'h11', 'h12', 'h13', 'h14', 'h15',
        'h16', 'h17', 'h18', 'h19', 'h20', 'h21', 'h22', 'h23'
    ];
}
