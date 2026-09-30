<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\EnclosureLog;
use Illuminate\Support\Facades\Cache;

class Dashboard extends Component
{
    public $mode, $manual_fan, $manual_vent;
    
    // Variabel Filter
    public $f_tanggal = '';
    public $f_bulan = '';
    public $f_tahun = '';
    public $f_waktu = '';

    public function mount()
    {
        $this->mode = Cache::get('control_mode', 'auto');
        $this->manual_fan = Cache::get('manual_fan', false);
        $this->manual_vent = Cache::get('manual_vent', false);
    }

    public function setMode($newMode)
    {
        $this->mode = $newMode;
        Cache::put('control_mode', $newMode);
    }

    public function toggleFan()
    {
        $this->manual_fan = !$this->manual_fan;
        Cache::put('manual_fan', $this->manual_fan);
    }

    public function toggleVent()
    {
        $this->manual_vent = !$this->manual_vent;
        Cache::put('manual_vent', $this->manual_vent);
    }

    public function hapusData($id)
    {
        EnclosureLog::find($id)->delete();
    }

    public function resetFilter()
    {
        $this->reset(['f_tanggal', 'f_bulan', 'f_tahun', 'f_waktu']);
    }

    public function render()
    {
        $latestLog = EnclosureLog::latest()->first();
        
        // Membangun Query Database berdasarkan filter yang aktif
        $query = EnclosureLog::query();

        if ($this->f_tahun) $query->whereYear('created_at', $this->f_tahun);
        if ($this->f_bulan) $query->whereMonth('created_at', $this->f_bulan);
        if ($this->f_tanggal) $query->whereDay('created_at', $this->f_tanggal);
        
        if ($this->f_waktu) {
            $times = explode('-', $this->f_waktu);
            if (count($times) == 2) {
                $query->whereTime('created_at', '>=', trim($times[0]))
                      ->whereTime('created_at', '<', trim($times[1]));
            }
        }

        $history = $query->orderBy('id', 'desc')->limit(50)->get();

        return view('components.dashboard', [
            'log' => $latestLog,
            'history' => $history
        ]);
    }
}