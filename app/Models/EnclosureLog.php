<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EnclosureLog extends Model
{
    protected $fillable = [
    'temperature', 
    'humidity', 
    'gas_ppm', 
    'fan_status', 
    'vent_status'
    ];
}
