<div wire:poll.2s class="bg-white rounded-2xl shadow-lg w-full max-w-md p-6">
    <div class="text-center mb-6">
        <h2 class="text-xl font-bold text-gray-800">Smart Enclosure</h2>
        <p class="text-sm text-gray-500">Status Saat Ini</p>
    </div>

    @if($log)
    <!-- Angka Utama -->
    <div class="flex justify-around items-center mb-6 text-center">
        <div>
            <p class="text-3xl font-extrabold text-gray-900">{{ $log->temperature }}<span class="text-lg">°C</span></p>
            <p class="text-xs text-gray-500 uppercase font-semibold">Suhu</p>
        </div>
        <div>
            <p class="text-3xl font-extrabold text-gray-900">{{ $log->humidity }}<span class="text-lg">%</span></p>
            <p class="text-xs text-gray-500 uppercase font-semibold">Lembab</p>
        </div>
        <div>
            <p class="text-3xl font-extrabold text-gray-900">{{ $log->gas_ppm }}</p>
            <p class="text-xs text-gray-500 uppercase font-semibold">Gas PPM</p>
        </div>
    </div>

    <!-- Sistem Online Indicator -->
    <div class="text-center mb-6">
        <span class="inline-flex items-center text-sm font-medium text-green-600">
            <span class="h-2 w-2 bg-green-500 rounded-full mr-2 animate-pulse"></span>
            Sistem Online
        </span>
    </div>

    <!-- Remote Control Box -->
    <div class="bg-gray-50 p-4 rounded-xl mb-6 border">
        <h3 class="text-sm font-bold text-gray-700 mb-3">Mode Kendali</h3>
        
        <div class="flex space-x-2 mb-4">
            <button wire:click="setMode('auto')" class="w-1/2 py-2 rounded-lg font-bold text-sm {{ $mode == 'auto' ? 'bg-blue-500 text-white' : 'bg-gray-200 text-gray-600' }}">Otomatis</button>
            <button wire:click="setMode('manual')" class="w-1/2 py-2 rounded-lg font-bold text-sm {{ $mode == 'manual' ? 'bg-blue-500 text-white' : 'bg-gray-200 text-gray-600' }}">Manual</button>
        </div>

        @if($mode == 'manual')
        <div class="flex space-x-2">
            <button wire:click="toggleFan" class="w-1/2 py-2 rounded-lg font-bold text-sm border {{ $manual_fan ? 'bg-green-100 text-green-700 border-green-400' : 'bg-white text-gray-600' }}">
                Kipas: {{ $manual_fan ? 'ON' : 'OFF' }}
            </button>
            <button wire:click="toggleVent" class="w-1/2 py-2 rounded-lg font-bold text-sm border {{ $manual_vent ? 'bg-green-100 text-green-700 border-green-400' : 'bg-white text-gray-600' }}">
                Vent: {{ $manual_vent ? 'OPEN' : 'CLOSE' }}
            </button>
        </div>
        @else
        <p class="text-xs text-center text-gray-500">Sistem bergerak otomatis berdasarkan sensor.</p>
        @endif
    </div>

    <!-- Blok Filter Riwayat -->
    <div class="bg-gray-50 p-4 rounded-xl mb-6 border">
        <h3 class="text-sm font-bold text-gray-700 mb-3">Filter Riwayat</h3>
        <div class="grid grid-cols-4 gap-2 mb-3 text-gray-700">
            <!-- Tanggal -->
            <div>
                <label class="text-[10px] text-gray-500 uppercase font-bold">Tanggal</label>
                <select wire:model="f_tanggal" class="w-full p-1 text-xs border rounded bg-white">
                    <option value="">Semua</option>
                    @for($i=1; $i<=31; $i++)
                        <option value="{{ str_pad($i, 2, '0', STR_PAD_LEFT) }}">{{ $i }}</option>
                    @endfor
                </select>
            </div>
            <!-- Bulan -->
            <div>
                <label class="text-[10px] text-gray-500 uppercase font-bold">Bulan</label>
                <select wire:model="f_bulan" class="w-full p-1 text-xs border rounded bg-white">
                    <option value="">Semua</option>
                    @for($i=1; $i<=12; $i++)
                        <option value="{{ str_pad($i, 2, '0', STR_PAD_LEFT) }}">{{ $i }}</option>
                    @endfor
                </select>
            </div>
            <!-- Tahun -->
            <div>
                <label class="text-[10px] text-gray-500 uppercase font-bold">Tahun</label>
                <select wire:model="f_tahun" class="w-full p-1 text-xs border rounded bg-white">
                    <option value="">Semua</option>
                    <option value="{{ date('Y') }}">{{ date('Y') }}</option>
                </select>
            </div>
            <!-- Waktu (30 Menit) -->
            <div>
                <label class="text-[10px] text-gray-500 uppercase font-bold">Waktu (30m)</label>
                <select wire:model="f_waktu" class="w-full p-1 text-xs border rounded bg-white">
                    <option value="">Semua</option>
                    @for($h=0; $h<24; $h++)
                        @php $jam = str_pad($h, 2, '0', STR_PAD_LEFT); @endphp
                        <option value="{{ $jam }}:00-{{ $jam }}:30">{{ $jam }}:00 - {{ $jam }}:30</option>
                        <option value="{{ $jam }}:30-{{ str_pad($h+1, 2, '0', STR_PAD_LEFT) }}:00">{{ $jam }}:30 - {{ str_pad($h+1, 2, '0', STR_PAD_LEFT) }}:00</option>
                    @endfor
                </select>
            </div>
        </div>
        <div class="flex space-x-2">
            <!-- Tombol filter dan reset -->
            <button wire:click="$refresh" class="w-1/2 bg-blue-500 text-white py-1.5 rounded-lg text-xs font-bold hover:bg-blue-600 transition">CARI</button>
            <button wire:click="resetFilter" class="w-1/2 bg-gray-500 text-white py-1.5 rounded-lg text-xs font-bold hover:bg-gray-600 transition">RESET</button>
        </div>
    </div>

    <!-- Riwayat Telemetri -->
    <div class="max-h-64 overflow-y-auto pr-2 space-y-3 scrollbar-thin">
            @foreach($history as $item)
            <div class="flex justify-between items-center border-b pb-2">
                <div class="w-full pr-3 text-xs">
                    <!-- Baris Atas: Sensor -->
                    <div class="flex justify-between font-bold text-gray-800 mb-1">
                        <span>🌡️ {{ $item->temperature }}°C</span>
                        <span class="text-blue-600">💧 {{ $item->humidity }}%</span>
                        <span class="text-purple-600">💨 {{ $item->gas_ppm }} PPM</span>
                    </div>
                    
                    <!-- Baris Bawah: Waktu & Aktuator -->
                    <div class="flex justify-between items-center text-[10px]">
                        <span class="text-gray-500">{{ $item->created_at->format('d-m-Y H:i') }}</span>
                        <div class="font-semibold">
                            <span class="{{ $item->fan_status ? 'text-green-600' : 'text-red-500' }}">Kipas: {{ $item->fan_status ? 'ON' : 'OFF' }}</span>
                            <span class="mx-1 text-gray-300">|</span>
                            <span class="{{ $item->vent_status ? 'text-green-600' : 'text-red-500' }}">Vent: {{ $item->vent_status ? 'OPEN' : 'CLOSE' }}</span>
                        </div>
                    </div>
                </div>
                
                <!-- Tombol Hapus -->
                <button wire:click="hapusData({{ $item->id }})" class="bg-blue-500 hover:bg-blue-600 text-white text-[10px] font-bold py-1 px-2 rounded h-fit">
                    Hapus
                </button>
            </div>
            @endforeach
        </div>
    </div>
    @else
        <p class="text-center text-gray-500">Menunggu data dari ESP32...</p>
    @endif
</div>