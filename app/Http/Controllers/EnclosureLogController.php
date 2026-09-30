<?php

namespace App\Http\Controllers;

use App\Models\EnclosureLog;
use Illuminate\Http\Request;

class EnclosureLogController extends Controller
{
    public function store(Request $request)
    {
        $mode = \Illuminate\Support\Facades\Cache::get('control_mode', 'auto');
        
        $fanStatus = $request->fan_status;
        $ventStatus = $request->vent_status;

        // KOREKSI DATABASE: Jika mode Manual, abaikan data dari ESP32, 
        // timpa dengan status tombol di dashboard!
        if ($mode == 'manual') {
            $fanStatus = \Illuminate\Support\Facades\Cache::get('manual_fan', false) ? 1 : 0;
            $ventStatus = \Illuminate\Support\Facades\Cache::get('manual_vent', false) ? 1 : 0;
        }

        // Simpan data ke database
        $log = EnclosureLog::create([
            'temperature' => $request->temperature,
            'humidity' => $request->humidity,
            'gas_ppm' => $request->gas_ppm,
            'fan_status' => $fanStatus,
            'vent_status' => $ventStatus,
        ]);

        return response()->json([
            'message' => 'Data berhasil disimpan', 
            'data' => $log,
            'mode' => $mode,
            'fan' => \Illuminate\Support\Facades\Cache::get('manual_fan', false),
            'vent' => \Illuminate\Support\Facades\Cache::get('manual_vent', false)
        ], 201);
    }
}
