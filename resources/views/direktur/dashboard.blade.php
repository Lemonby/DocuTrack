@extends('layouts.app')

@section('content')
<main class="main-content font-poppins p-3 md:p-5 lg:p-6 -mt-10 md:-mt-20 max-w-[1600px] mx-auto w-full">

    <!-- Kontainer Dashboard (Compact but Readable - Zero Dropdown) -->
    <div class="bg-white rounded-[2.5rem] p-6 md:p-10 border border-slate-100 shadow-xl shadow-slate-200 relative overflow-hidden">
        
        <div class="relative z-10">
            
            <!-- HEADER & YEAR SELECTOR -->
            <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-6 mb-10">
                <div>
                    <h1 class="text-3xl md:text-4xl font-black text-slate-900 tracking-tighter">Executive Intelligence</h1>
                    <div class="flex items-center gap-2 mt-2">
                        <div class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></div>
                        <p class="text-slate-400 text-xs font-bold uppercase tracking-widest">Real-time Fiscal Overview</p>
                    </div>
                </div>

                <!-- Year Selector -->
                <div class="flex items-center p-1.5 bg-slate-50 rounded-2xl border border-slate-200 shadow-inner">
                    @foreach(['2024', '2025', '2026', '2027'] as $year)
                        <button class="year-pill px-6 py-2.5 rounded-xl text-xs font-black tracking-widest transition-all duration-300 {{ $year == '2026' ? 'bg-blue-600 text-white shadow-lg shadow-blue-100' : 'text-slate-500 hover:text-slate-700' }}">
                            {{ $year }}
                        </button>
                    @endforeach
                </div>
            </div>

            <!-- INTERACTIVE UNIT MATRIX -->
            <div class="mb-10">
                <div class="flex justify-between items-center mb-5 px-1">
                    <h3 class="text-[11px] font-black text-slate-400 uppercase tracking-widest">Unit Analysis Matrix</h3>
                    <span class="text-[11px] font-bold text-blue-600 uppercase italic">Select unit for drill-down</span>
                </div>
                <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-7 gap-4">
                    <button class="unit-tile active-unit group p-4 bg-blue-600 rounded-3xl border border-blue-500 shadow-xl shadow-blue-100 transition-all duration-300 flex flex-col items-center text-center">
                        <i class="fas fa-globe-asia text-xl text-white mb-2"></i>
                        <span class="text-[11px] font-black text-white uppercase tracking-tight leading-tight">Institusi</span>
                    </button>

                    @foreach(array_slice($list_jurusan, 0, 6) as $index => $jurusan)
                    <button class="unit-tile group p-4 bg-white rounded-3xl border border-slate-100 hover:border-blue-400 hover:shadow-lg transition-all duration-300 flex flex-col items-center text-center shadow-sm">
                        <i class="fas fa-{{ ['university', 'microchip', 'calculator', 'flask', 'balance-scale', 'tools'][$index] }} text-xl text-slate-400 group-hover:text-blue-600 mb-2 transition-colors"></i>
                        <span class="text-[10px] font-black text-slate-500 group-hover:text-slate-900 uppercase tracking-tight leading-tight truncate w-full">{{ $jurusan }}</span>
                    </button>
                    @endforeach
                </div>
            </div>

            <!-- FINANCIAL PULSE -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 mb-10">
                <!-- Pulse Card -->
                <div class="lg:col-span-8 bg-gradient-to-br from-slate-900 to-blue-950 rounded-[3rem] p-10 md:p-14 text-white relative overflow-hidden shadow-2xl shadow-blue-950/20 group">
                    <div class="absolute top-0 right-0 w-80 h-80 bg-blue-500/10 blur-[100px] rounded-full -mr-40 -mt-40"></div>
                    
                    <div class="relative z-10">
                        <div class="flex flex-col md:flex-row justify-between items-center gap-10 mb-14">
                            <div class="text-center md:text-left space-y-5">
                                <span class="px-5 py-2 bg-white/10 text-blue-300 rounded-xl text-xs font-black uppercase tracking-widest border border-white/5">Total Realisasi Dana</span>
                                <div>
                                    <h2 class="text-4xl md:text-5xl font-black tracking-tighter">Rp {{ number_format($budget['total_realized'], 0, ',', '.') }}</h2>
                                    <p class="text-slate-500 text-xs font-medium mt-2">Pagu Tahunan: Rp {{ number_format($budget['total_allocated'], 0, ',', '.') }}</p>
                                </div>
                            </div>
                            
                            <!-- Gauge -->
                            <div class="relative w-40 h-40 flex items-center justify-center">
                                <svg class="w-full h-full rotate-[-90deg]">
                                    <circle cx="80" cy="80" r="72" stroke="currentColor" stroke-width="10" fill="transparent" class="text-white/5"></circle>
                                    <circle cx="80" cy="80" r="72" stroke="currentColor" stroke-width="10" fill="transparent" stroke-dasharray="452.4" stroke-dashoffset="{{ 452.4 * (1 - $budget['percentage']/100) }}" class="text-blue-500 transition-all duration-1000"></circle>
                                </svg>
                                <div class="absolute flex flex-col items-center">
                                    <span class="text-3xl font-black">{{ $budget['percentage'] }}%</span>
                                    <span class="text-[10px] font-bold text-slate-500 uppercase tracking-widest mt-1">Serapan</span>
                                </div>
                            </div>
                        </div>

                        <!-- Stats Grid -->
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                            @php
                                $pulse_stats = [
                                    ['label' => 'Sisa Saldo', 'value' => 'Rp '.number_format($budget['remaining']/1000000, 1).'jt', 'icon' => 'wallet'],
                                    ['label' => 'Proyeksi', 'value' => '98.5%', 'icon' => 'chart-line'],
                                    ['label' => 'Total Usul', 'value' => $stats['total'], 'icon' => 'file-invoice'],
                                    ['label' => 'Health', 'value' => '94.2', 'icon' => 'shield-check']
                                ];
                            @endphp
                            @foreach($pulse_stats as $ps)
                            <div class="bg-white/5 p-6 rounded-3xl border border-white/5 hover:bg-white/10 transition-all text-center group cursor-default">
                                <i class="fas fa-{{ $ps['icon'] }} text-blue-400 text-lg mb-3 group-hover:scale-110 transition-transform"></i>
                                <p class="text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1">{{ $ps['label'] }}</p>
                                <h4 class="text-sm font-black tracking-tight">{{ $ps['value'] }}</h4>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Side Rail -->
                <div class="lg:col-span-4 flex flex-col gap-8">
                    <div class="bg-white rounded-[3rem] p-10 border border-slate-100 shadow-xl shadow-slate-100/50 flex-1 flex flex-col items-center justify-center text-center relative overflow-hidden">
                        <div class="absolute inset-0 bg-blue-50/30 opacity-50"></div>
                        <div class="relative z-10">
                            <div class="w-24 h-24 rounded-full border-[10px] border-slate-50 border-t-emerald-500 flex items-center justify-center shadow-inner mb-6 mx-auto bg-white">
                                <span class="text-3xl font-black text-slate-800">94.2</span>
                            </div>
                            <h4 class="text-xl font-black text-slate-800 mb-2">Fiscal Health</h4>
                            <p class="text-xs text-slate-400 font-medium px-6 leading-relaxed italic">Kinerja serapan dana berada dalam zona optimal.</p>
                        </div>
                    </div>

                    <div class="bg-blue-600 rounded-[2.5rem] p-8 text-white shadow-xl shadow-blue-200">
                        <div class="flex items-center gap-4 mb-4">
                            <i class="fas fa-lightbulb text-xl"></i>
                            <h4 class="text-xs font-black uppercase tracking-widest">Quick Insight</h4>
                        </div>
                        <p class="text-xs text-blue-50 font-medium leading-relaxed opacity-90">"Efisiensi operasional meningkat signifikan bulan ini."</p>
                    </div>
                </div>
            </div>

            <!-- ANALYTICS -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
                <div class="lg:col-span-8 bg-white rounded-[3rem] p-8 md:p-12 border border-slate-100 shadow-xl shadow-slate-200/40">
                    <div class="flex justify-between items-center mb-10">
                        <div>
                            <h3 class="text-xl font-black text-slate-900 tracking-tight">Trend Analysis</h3>
                            <p class="text-xs text-slate-400 font-bold uppercase tracking-widest mt-1">Monthly Realization Performance</p>
                        </div>
                        <div class="flex p-1.5 bg-slate-50 rounded-2xl border border-slate-200">
                            <button class="px-6 py-2 bg-white rounded-xl text-[10px] font-black uppercase tracking-widest text-blue-600 shadow-sm">Monthly</button>
                            <button class="px-6 py-2 text-[10px] font-black uppercase tracking-widest text-slate-400">Quarterly</button>
                        </div>
                    </div>
                    <div class="h-72">
                        <canvas id="trendChart"></canvas>
                    </div>
                </div>

                <div class="lg:col-span-4 bg-slate-950 rounded-[3rem] p-10 text-white shadow-2xl shadow-slate-900/40 relative overflow-hidden flex flex-col h-full">
                    <h3 class="text-xs font-black text-blue-400 uppercase tracking-widest mb-10">Strategic IKU Progress</h3>
                    <div class="space-y-8">
                        @foreach(array_slice($iku_achievements, 0, 4) as $iku)
                        <div class="group">
                            <div class="flex justify-between items-end mb-3">
                                <span class="text-[11px] font-bold text-slate-400 group-hover:text-white transition-colors uppercase leading-tight truncate w-3/4">{{ $iku['nama'] }}</span>
                                <span class="text-sm font-black text-blue-400">{{ $iku['capaian'] }}%</span>
                            </div>
                            <div class="w-full bg-white/5 h-2 rounded-full overflow-hidden border border-white/5">
                                <div class="h-full bg-blue-500 transition-all duration-1000 group-hover:bg-emerald-400 shadow-[0_0_10px_rgba(59,130,246,0.3)]" style="width: {{ ($iku['capaian']/$iku['target'])*100 }}%"></div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    
                    <div class="mt-auto pt-10 text-center border-t border-white/5">
                        <p class="text-[10px] font-black text-slate-500 uppercase tracking-widest mb-4">Sisa Waktu Anggaran</p>
                        <div class="flex justify-center items-end gap-3 group cursor-pointer">
                            <span class="text-4xl font-black text-white tracking-tighter group-hover:text-blue-500 transition-colors">224</span>
                            <span class="text-xs font-bold text-slate-500 mb-2 uppercase tracking-widest">Hari Kerja</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</main>

<style>
    .font-poppins { font-family: 'Poppins', sans-serif; }
    canvas { width: 100% !important; }
    .unit-tile { transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275); }
    .year-pill { transition: all 0.3s ease; }
    .unit-tile:active { transform: scale(0.95); }
    .scrollbar-hide::-webkit-scrollbar { display: none; }
</style>

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const monthlyTrend = @json($monthly_trend);
    Chart.defaults.font.family = "'Poppins', sans-serif";
    Chart.defaults.color = '#94a3b8';

    new Chart(document.getElementById('trendChart'), {
        type: 'line',
        data: {
            labels: monthlyTrend.labels,
            datasets: [{
                data: monthlyTrend.data,
                borderColor: '#2563eb',
                borderWidth: 6,
                backgroundColor: 'rgba(37, 99, 235, 0.05)',
                fill: true,
                tension: 0.5,
                pointRadius: 6,
                pointBackgroundColor: '#ffffff',
                pointBorderColor: '#2563eb',
                pointBorderWidth: 3
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: { display: false, beginAtZero: true },
                x: { grid: { display: false }, border: { display: false }, ticks: { font: { weight: '800', size: 10 }, color: '#64748b' } }
            }
        }
    });

    document.querySelectorAll('.unit-tile').forEach(tile => {
        tile.onclick = () => {
            document.querySelectorAll('.unit-tile').forEach(t => {
                t.classList.remove('bg-blue-600', 'border-blue-500', 'shadow-xl', 'shadow-blue-100', 'active-unit');
                t.classList.add('bg-white', 'border-slate-100');
                const icon = t.querySelector('i'); if(icon) icon.classList.replace('text-white', 'text-slate-400');
                const span = t.querySelector('span'); if(span) span.classList.replace('text-white', 'text-slate-500');
            });
            tile.classList.add('bg-blue-600', 'border-blue-500', 'shadow-xl', 'shadow-blue-100', 'active-unit');
            const icon = tile.querySelector('i'); if(icon) icon.classList.replace('text-slate-400', 'text-white');
            const span = tile.querySelector('span'); if(span) span.classList.replace('text-slate-500', 'text-white');
        };
    });
});
</script>
@endpush
@endsection
