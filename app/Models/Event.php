<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;
    
    protected $guarded = [];
    
    // Auto cast dates
    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];
}
