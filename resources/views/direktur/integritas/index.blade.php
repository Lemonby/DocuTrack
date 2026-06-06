@extends('layouts.app')

@section('content')
<main class="main-content font-poppins p-3 md:p-5 lg:p-6 -mt-10 md:-mt-20 max-w-[1600px] mx-auto w-full">

    <!-- Kontainer SPK MAUT (Desain WOW/Premium) -->
    <div class="bg-white rounded-[2.5rem] p-6 md:p-10 border border-slate-100 shadow-xl shadow-slate-200 relative overflow-hidden">
        
        <div class="relative z-10">
            
            <!-- HEADER & SUBTITLE -->
            <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-6 mb-10">
                <div>
                    <h1 class="text-3xl md:text-4xl font-black text-slate-900 tracking-tighter">Integritas Jurusan</h1>
                    <div class="flex items-center gap-2 mt-2">
                        <div class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></div>
                        <p class="text-slate-400 text-xs font-bold uppercase tracking-widest">Multi-Attribute Utility Theory (MAUT) Analytics</p>
                    </div>
                </div>

                <!-- Info Metode SPK MAUT -->
                <div class="flex items-center gap-3 bg-slate-50 border border-slate-100 rounded-2xl p-4 max-w-md shadow-sm">
                    <i class="fas fa-info-circle text-blue-600 text-lg"></i>
                    <p class="text-[11px] text-slate-500 leading-normal font-medium">
                        Pemeringkatan dihitung berdasarkan **4 kriteria (C1-C4)** dengan bobot masing-masing **25%** untuk KAK/kegiatan yang telah mengumpulkan LPJ.
                    </p>
                </div>
            </div>

            @if($rankings->isEmpty())
                <!-- EMPTY STATE -->
                <div class="text-center py-20 bg-slate-50 rounded-[2rem] border border-dashed border-slate-200">
                    <div class="w-20 h-20 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-6">
                        <i class="fas fa-folder-open text-slate-400 text-3xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-slate-700">Belum Ada LPJ Ter-Submit</h3>
                    <p class="text-slate-400 text-sm max-w-md mx-auto mt-2 leading-relaxed">
                        Data evaluasi integritas SPK MAUT akan otomatis muncul setelah ada jurusan yang mengumpulkan LPJ di sistem.
                    </p>
                </div>
            @else
                <!-- LEADERBOARD TOP 3 PODIUM -->
                <div class="mb-14">
                    <h3 class="text-[11px] font-black text-slate-400 uppercase tracking-widest mb-6 px-1">Top Academic Units (Podium Integritas)</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 items-end">
                        
                        <!-- Peringkat 2 (Silver) -->
                        @if($rankings->count() >= 2)
                        @php $rank2 = $rankings->get(1); @endphp
                        <a href="{{ route('direktur.integritas.index', ['jurusan' => $rank2['jurusan']]) }}" 
                           class="group relative bg-gradient-to-br from-slate-50 to-slate-100/50 rounded-[2.5rem] p-8 border border-slate-200/60 shadow-lg hover:shadow-xl transition-all duration-300 flex flex-col items-center text-center overflow-hidden order-2 md:order-1 {{ $selectedJurusan === $rank2['jurusan'] ? 'ring-2 ring-slate-400 bg-slate-100' : '' }}">
                            
                            <div class="absolute top-0 right-0 w-24 h-24 bg-slate-200/30 rounded-full -mr-10 -mt-10 blur-xl"></div>
                            
                            <!-- Trophy / Rank Badge -->
                            <div class="w-16 h-16 bg-slate-200 rounded-full flex items-center justify-center shadow-inner mb-4 relative">
                                <i class="fas fa-trophy text-slate-400 text-2xl"></i>
                                <span class="absolute -bottom-2 bg-slate-500 text-white font-black text-xs px-2.5 py-0.5 rounded-full border-2 border-white">2</span>
                            </div>
                            
                            <h4 class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-1">Silver Integrity</h4>
                            <h2 class="text-base font-black text-slate-800 tracking-tight line-clamp-1 group-hover:text-blue-600 transition-colors uppercase">{{ $rank2['jurusan'] }}</h2>
                            
                            <div class="mt-4 px-5 py-2 bg-slate-200/50 rounded-xl text-slate-700 text-sm font-black tracking-tight">
                                Skor: {{ number_format($rank2['average_score'], 2) }}
                            </div>
                            <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider mt-2">
                                {{ $rank2['kegiatan_count'] }} Kegiatan Evaluasi
                            </span>
                        </a>
                        @endif

                        <!-- Peringkat 1 (Gold) -->
                        @php $rank1 = $rankings->first(); @endphp
                        <a href="{{ route('direktur.integritas.index', ['jurusan' => $rank1['jurusan']]) }}" 
                           class="group relative bg-gradient-to-br from-amber-50 to-yellow-50/30 rounded-[3rem] p-10 border border-yellow-200 shadow-2xl shadow-yellow-100/50 hover:shadow-yellow-200/60 hover:-translate-y-1 transition-all duration-300 flex flex-col items-center text-center overflow-hidden order-1 md:order-2 {{ $selectedJurusan === $rank1['jurusan'] ? 'ring-2 ring-yellow-400 bg-yellow-100' : '' }}">
                            
                            <div class="absolute top-0 right-0 w-32 h-32 bg-yellow-200/30 rounded-full -mr-12 -mt-12 blur-xl animate-pulse"></div>
                            
                            <!-- Medal / Rank Badge -->
                            <div class="w-20 h-20 bg-yellow-100 rounded-full flex items-center justify-center shadow-lg shadow-yellow-200/30 mb-6 relative">
                                <i class="fas fa-crown text-yellow-500 text-3xl animate-bounce"></i>
                                <span class="absolute -bottom-2 bg-yellow-500 text-white font-black text-sm px-3.5 py-0.5 rounded-full border-2 border-white shadow-sm">1</span>
                            </div>
                            
                            <span class="px-4 py-1.5 bg-yellow-500/10 text-yellow-700 rounded-full text-[10px] font-black uppercase tracking-widest border border-yellow-300/30 mb-2">Prime Leader</span>
                            <h2 class="text-xl font-black text-slate-900 tracking-tight leading-tight uppercase group-hover:text-blue-600 transition-colors">{{ $rank1['jurusan'] }}</h2>
                            
                            <div class="mt-5 px-6 py-2.5 bg-yellow-500 text-white rounded-2xl text-base font-black tracking-tight shadow-md shadow-yellow-500/20">
                                Skor: {{ number_format($rank1['average_score'], 2) }}
                            </div>
                            <span class="text-[10px] text-yellow-600/80 font-black uppercase tracking-wider mt-3">
                                {{ $rank1['kegiatan_count'] }} Kegiatan Evaluasi
                            </span>
                        </a>

                        <!-- Peringkat 3 (Bronze) -->
                        @if($rankings->count() >= 3)
                        @php $rank3 = $rankings->get(2); @endphp
                        <a href="{{ route('direktur.integritas.index', ['jurusan' => $rank3['jurusan']]) }}" 
                           class="group relative bg-gradient-to-br from-amber-50/40 to-amber-100/20 rounded-[2.5rem] p-8 border border-amber-200/40 shadow-lg hover:shadow-xl transition-all duration-300 flex flex-col items-center text-center overflow-hidden order-3 {{ $selectedJurusan === $rank3['jurusan'] ? 'ring-2 ring-amber-400 bg-amber-100' : '' }}">
                            
                            <div class="absolute top-0 right-0 w-24 h-24 bg-amber-200/20 rounded-full -mr-10 -mt-10 blur-xl"></div>
                            
                            <!-- Trophy / Rank Badge -->
                            <div class="w-16 h-16 bg-amber-100/50 rounded-full flex items-center justify-center shadow-inner mb-4 relative">
                                <i class="fas fa-award text-amber-600 text-2xl"></i>
                                <span class="absolute -bottom-2 bg-amber-600 text-white font-black text-xs px-2.5 py-0.5 rounded-full border-2 border-white">3</span>
                            </div>
                            
                            <h4 class="text-xs font-bold text-amber-500 uppercase tracking-widest mb-1">Bronze Integrity</h4>
                            <h2 class="text-base font-black text-slate-800 tracking-tight line-clamp-1 group-hover:text-blue-600 transition-colors uppercase">{{ $rank3['jurusan'] }}</h2>
                            
                            <div class="mt-4 px-5 py-2 bg-amber-200/30 rounded-xl text-amber-800 text-sm font-black tracking-tight">
                                Skor: {{ number_format($rank3['average_score'], 2) }}
                            </div>
                            <span class="text-[10px] text-amber-600/70 font-bold uppercase tracking-wider mt-2">
                                {{ $rank3['kegiatan_count'] }} Kegiatan Evaluasi
                            </span>
                        </a>
                        @endif

                    </div>
                </div>

                <!-- ALL DEPARTMENTS MATRIX -->
                <div class="mb-14">
                    <div class="flex justify-between items-center mb-5 px-1">
                        <h3 class="text-[11px] font-black text-slate-400 uppercase tracking-widest">Select Department for Drill-down</h3>
                        <span class="text-[11px] font-bold text-blue-600 uppercase italic">Klik jurusan untuk melihat detil</span>
                    </div>
                    
                    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
                        @foreach($rankings as $index => $rank)
                            <a href="{{ route('direktur.integritas.index', ['jurusan' => $rank['jurusan']]) }}" 
                               class="unit-tile group p-5 rounded-3xl border transition-all duration-300 flex flex-col items-center text-center shadow-sm relative overflow-hidden {{ $selectedJurusan === $rank['jurusan'] ? 'bg-blue-600 border-blue-500 shadow-xl shadow-blue-100 text-white active-unit' : 'bg-white border-slate-100 hover:border-blue-400 hover:shadow-lg text-slate-500' }}">
                                
                                @if($index < 3)
                                    <div class="absolute -top-1 -right-1 w-6 h-6 rotate-45 flex items-center justify-center {{ $index == 0 ? 'bg-yellow-500' : ($index == 1 ? 'bg-slate-400' : 'bg-amber-600') }} text-white text-[9px] font-black">
                                        {{ $index + 1 }}
                                    </div>
                                @endif

                                <i class="fas fa-{{ ['university', 'microchip', 'calculator', 'flask', 'balance-scale', 'tools'][$index % 6] }} text-2xl mb-3 transition-colors {{ $selectedJurusan === $rank['jurusan'] ? 'text-white' : 'text-slate-400 group-hover:text-blue-600' }}"></i>
                                
                                <span class="text-[10px] font-black uppercase tracking-tight leading-tight truncate w-full mb-1 {{ $selectedJurusan === $rank['jurusan'] ? 'text-white' : 'text-slate-600 group-hover:text-slate-900' }}">{{ $rank['jurusan'] }}</span>
                                
                                <span class="text-[11px] font-black tracking-tight {{ $selectedJurusan === $rank['jurusan'] ? 'text-blue-150' : 'text-blue-600' }}">
                                    Skor: {{ number_format($rank['average_score'], 2) }}
                                </span>
                            </a>
                        @endforeach
                    </div>
                </div>

                @if($selectedRankData)
                <!-- DRILL DOWN AREA FOR SELECTED JURUSAN -->
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 mb-10">
                    
                    <!-- CRITERIA SCORES GRAPH & STATS -->
                    <div class="lg:col-span-7 bg-gradient-to-br from-slate-900 to-blue-950 rounded-[3rem] p-8 md:p-10 text-white relative overflow-hidden shadow-2xl shadow-blue-950/20 group flex flex-col justify-between">
                        <div class="absolute top-0 right-0 w-80 h-80 bg-blue-500/10 blur-[100px] rounded-full -mr-40 -mt-40"></div>
                        
                        <div class="relative z-10">
                            <span class="px-5 py-2 bg-white/10 text-blue-300 rounded-xl text-xs font-black uppercase tracking-widest border border-white/5 inline-block mb-6">
                                Evaluasi Kriteria Rata-rata
                            </span>
                            
                            <div class="flex flex-col md:flex-row justify-between items-center gap-8 mb-8">
                                <div class="text-center md:text-left">
                                    <p class="text-slate-500 text-xs font-bold uppercase tracking-widest">Selected Department</p>
                                    <h2 class="text-2xl md:text-3xl font-black tracking-tighter uppercase mt-1">{{ $selectedJurusan }}</h2>
                                    
                                    <div class="flex items-center gap-3 mt-4">
                                        <div class="px-4 py-2 bg-white/5 rounded-xl border border-white/5">
                                            <span class="text-[10px] text-slate-400 font-bold block uppercase tracking-wider">Average Score</span>
                                            <span class="text-lg font-black text-emerald-400">{{ number_format($selectedRankData['average_score'], 2) }}</span>
                                        </div>
                                        <div class="px-4 py-2 bg-white/5 rounded-xl border border-white/5">
                                            <span class="text-[10px] text-slate-400 font-bold block uppercase tracking-wider">Rank</span>
                                            <span class="text-lg font-black text-yellow-400">#{{ $rankings->search(fn($r) => $r['jurusan'] === $selectedJurusan) + 1 }} dari {{ $rankings->count() }}</span>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Mini Circular Gauge -->
                                <div class="relative w-32 h-32 flex items-center justify-center">
                                    <svg class="w-full h-full rotate-[-90deg]">
                                        <circle cx="64" cy="64" r="56" stroke="currentColor" stroke-width="8" fill="transparent" class="text-white/5"></circle>
                                        <circle cx="64" cy="64" r="56" stroke="currentColor" stroke-width="8" fill="transparent" stroke-dasharray="351.8" stroke-dashoffset="{{ 351.8 * (1 - $selectedRankData['average_score']) }}" class="text-blue-500 transition-all duration-1000"></circle>
                                    </svg>
                                    <div class="absolute flex flex-col items-center">
                                        <span class="text-2xl font-black">{{ number_format($selectedRankData['average_score'], 2) }}</span>
                                        <span class="text-[9px] font-bold text-slate-500 uppercase tracking-widest">MAUT</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Criteria Pills Grid -->
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                <div class="bg-white/5 p-4 rounded-2xl border border-white/5">
                                    <span class="text-[9px] font-black text-slate-500 uppercase tracking-widest block mb-1">C1: Waktu Pelaksana</span>
                                    <h4 class="text-lg font-black tracking-tight text-white">{{ number_format($selectedRankData['avg_c1'], 2) }}</h4>
                                </div>
                                <div class="bg-white/5 p-4 rounded-2xl border border-white/5">
                                    <span class="text-[9px] font-black text-slate-500 uppercase tracking-widest block mb-1">C2: Serapan Anggaran</span>
                                    <h4 class="text-lg font-black tracking-tight text-white">{{ number_format($selectedRankData['avg_c2'], 2) }}</h4>
                                </div>
                                <div class="bg-white/5 p-4 rounded-2xl border border-white/5">
                                    <span class="text-[9px] font-black text-slate-500 uppercase tracking-widest block mb-1">C3: Dukung IKU</span>
                                    <h4 class="text-lg font-black tracking-tight text-white">{{ number_format($selectedRankData['avg_c3'], 2) }}</h4>
                                </div>
                                <div class="bg-white/5 p-4 rounded-2xl border border-white/5">
                                    <span class="text-[9px] font-black text-slate-500 uppercase tracking-widest block mb-1">C4: Pengajuan LPJ</span>
                                    <h4 class="text-lg font-black tracking-tight text-white">{{ number_format($selectedRankData['avg_c4'], 2) }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- CHART VISUALIZATION (Radar/Bar per Kriteria) -->
                    <div class="lg:col-span-5 bg-white rounded-[3rem] p-8 border border-slate-100 shadow-xl shadow-slate-100/50 flex flex-col justify-between">
                        <div>
                            <h3 class="text-base font-black text-slate-800 tracking-tight">Kinerja Profil Kriteria</h3>
                            <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest mt-1">Radar Analysis Comparison</p>
                        </div>
                        <div class="h-60 mt-4">
                            <canvas id="kriteriaRadarChart"></canvas>
                        </div>
                    </div>
                </div>

                <!-- ACTIVITIES SCORE TABLE BREAKDOWN -->
                <div class="bg-white rounded-[2.5rem] p-6 md:p-8 border border-slate-100 shadow-xl shadow-slate-150 relative overflow-hidden">
                    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6">
                        <div>
                            <h3 class="text-xl font-black text-slate-900 tracking-tight">Breakdown Evaluasi Kegiatan</h3>
                            <p class="text-xs text-slate-400 font-medium">Detail skor MAUT untuk seluruh KAK jurusan **{{ $selectedJurusan }}**</p>
                        </div>
                        <div class="px-4 py-2 bg-blue-50 text-blue-700 rounded-xl text-xs font-black uppercase tracking-wider border border-blue-100">
                            {{ $selectedKegiatans->count() }} KAK Dievaluasi
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="border-b border-slate-100">
                                    <th class="py-4 px-3 text-[10px] font-black uppercase tracking-widest text-slate-400">Nama Kegiatan</th>
                                    <th class="py-4 px-3 text-[10px] font-black uppercase tracking-widest text-slate-400 text-center">C1: Durasi Riil</th>
                                    <th class="py-4 px-3 text-[10px] font-black uppercase tracking-widest text-slate-400 text-center">C2: Budget</th>
                                    <th class="py-4 px-3 text-[10px] font-black uppercase tracking-widest text-slate-400 text-center">C3: IKU</th>
                                    <th class="py-4 px-3 text-[10px] font-black uppercase tracking-widest text-slate-400 text-center">C4: LPJ Submit</th>
                                    <th class="py-4 px-3 text-[10px] font-black uppercase tracking-widest text-slate-400 text-center">Skor MAUT</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-50">
                                @forelse($selectedKegiatans as $keg)
                                    @php 
                                        $sc = $keg->spk_scores;
                                        // Tentukan badge warna skor MAUT
                                        $badgeColor = 'bg-red-50 text-red-600 border-red-100';
                                        if($keg->final_score >= 0.8) {
                                            $badgeColor = 'bg-emerald-50 text-emerald-600 border-emerald-100';
                                        } elseif($keg->final_score >= 0.6) {
                                            $badgeColor = 'bg-blue-50 text-blue-600 border-blue-100';
                                        } elseif($keg->final_score >= 0.4) {
                                            $badgeColor = 'bg-amber-50 text-amber-600 border-amber-100';
                                        }
                                    @endphp
                                    <tr class="hover:bg-slate-50/50 transition-colors">
                                        <!-- Nama Kegiatan -->
                                        <td class="py-4 px-3 max-w-xs">
                                            <p class="font-bold text-slate-800 text-sm leading-snug line-clamp-2 uppercase">{{ $keg->nama_kegiatan }}</p>
                                            <span class="text-[10px] text-slate-400 font-bold block mt-1 uppercase">PJ: {{ $keg->nama_pj }}</span>
                                        </td>
                                        
                                        <!-- C1: Durasi -->
                                        @php
                                            $planned = $keg->tanggal_mulai->diffInDays($keg->tanggal_selesai);
                                            $real = $keg->lpj->realisasi_tanggal_mulai->diffInDays($keg->lpj->realisasi_tanggal_selesai);
                                            $diff = abs($planned - $real);
                                        @endphp
                                        <td class="py-4 px-3 text-center">
                                            <div class="font-black text-slate-800 text-sm">{{ number_format($sc['c1'], 2) }}</div>
                                            <span class="text-[9px] text-slate-400 font-medium block mt-0.5 whitespace-nowrap">
                                                Plan: {{ $planned }}d | Real: {{ $real }}d ({{ $diff === 0 ? 'Tepat' : '±'.$diff.'d' }})
                                            </span>
                                        </td>

                                        <!-- C2: Penyerapan -->
                                        @php
                                            $dicairkan = (float) $keg->jumlah_dicairkan;
                                            $realisasi = (float) $keg->lpj->grand_total_realisasi;
                                            $rate = $dicairkan > 0 ? ($realisasi / $dicairkan) * 100 : 0;
                                        @endphp
                                        <td class="py-4 px-3 text-center">
                                            <div class="font-black text-slate-800 text-sm">{{ number_format($sc['c2'], 2) }}</div>
                                            <span class="text-[9px] text-slate-400 font-medium block mt-0.5 whitespace-nowrap">
                                                Serapan: {{ number_format($rate, 1) }}%
                                            </span>
                                        </td>

                                        <!-- C3: IKU -->
                                        @php
                                            $ikus = $keg->kak ? $keg->kak->ikus->count() : 0;
                                        @endphp
                                        <td class="py-4 px-3 text-center">
                                            <div class="font-black text-slate-800 text-sm">{{ number_format($sc['c3'], 2) }}</div>
                                            <span class="text-[9px] text-slate-400 font-medium block mt-0.5 whitespace-nowrap">
                                                Dukung {{ $ikus }} IKU
                                            </span>
                                        </td>

                                        <!-- C4: LPJ Submission -->
                                        @php
                                            $sub = $keg->lpj->submitted_at;
                                            $dl = $keg->lpj->tenggat_lpj;
                                            $isTelat = $sub > $dl;
                                            $diffTelat = $isTelat ? $dl->diffInDays($sub) : 0;
                                        @endphp
                                        <td class="py-4 px-3 text-center">
                                            <div class="font-black text-slate-800 text-sm">{{ number_format($sc['c4'], 2) }}</div>
                                            <span class="text-[9px] text-slate-400 font-medium block mt-0.5 whitespace-nowrap">
                                                @if($isTelat)
                                                    Telat {{ $diffTelat }} hari
                                                @else
                                                    Tepat waktu
                                                @endif
                                            </span>
                                        </td>

                                        <!-- Skor MAUT -->
                                        <td class="py-4 px-3 text-center">
                                            <div class="inline-block px-3 py-1.5 rounded-xl text-xs font-black tracking-tight border {{ $badgeColor }}">
                                                {{ number_format($keg->final_score, 2) }}
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-8 text-slate-400 text-sm font-medium">
                                            Tidak ada kegiatan evaluasi untuk jurusan ini.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @endif
            @endif

        </div>
    </div>

</main>

<style>
    .font-poppins { font-family: 'Poppins', sans-serif; }
    canvas { width: 100% !important; }
    .unit-tile { transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275); }
    .unit-tile:active { transform: scale(0.95); }
</style>

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    Chart.defaults.font.family = "'Poppins', sans-serif";
    Chart.defaults.color = '#64748b';

    @if($selectedRankData)
        // radar chart data for selected department
        const kriteriaCtx = document.getElementById('kriteriaRadarChart').getContext('2d');
        new Chart(kriteriaCtx, {
            type: 'radar',
            data: {
                labels: ['C1: Waktu Pelaksana', 'C2: Budget Serap', 'C3: Dukung IKU', 'C4: Pengajuan LPJ'],
                datasets: [{
                    label: 'Skor Kriteria',
                    data: [
                        {{ $selectedRankData['avg_c1'] }},
                        {{ $selectedRankData['avg_c2'] }},
                        {{ $selectedRankData['avg_c3'] }},
                        {{ $selectedRankData['avg_c4'] }}
                    ],
                    fill: true,
                    backgroundColor: 'rgba(37, 99, 235, 0.15)',
                    borderColor: '#2563eb',
                    pointBackgroundColor: '#2563eb',
                    pointBorderColor: '#fff',
                    pointHoverBackgroundColor: '#fff',
                    pointHoverBorderColor: '#2563eb',
                    borderWidth: 3
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    r: {
                        angleLines: { color: 'rgba(0, 0, 0, 0.05)' },
                        grid: { color: 'rgba(0, 0, 0, 0.05)' },
                        pointLabels: {
                            font: { size: 10, weight: '700' },
                            color: '#475569'
                        },
                        ticks: {
                                                            backdropColor: 'transparent',
                                                            font: { size: 9 },
                                                            color: '#94a3b8'
                                                        },
                                                        suggestedMin: 0,
                                                        suggestedMax: 1
                    }
                }
            }
        });
    @endif
});
</script>
@endpush
@endsection
