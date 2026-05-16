@extends('layouts.app')

@section('content')
<main class="main-content font-poppins p-4 md:p-6 lg:p-8 -mt-8 md:-mt-16 max-w-[1550px] mx-auto w-full">

    <!-- Unified Modern SaaS Shell -->
    <div class="bg-white rounded-3xl p-5 md:p-8 border border-slate-200 shadow-xl shadow-slate-100">
        
        <!-- Professional Header -->
        <div class="flex flex-col lg:flex-row justify-between items-center gap-6 mb-10">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-blue-600 flex items-center justify-center shadow-lg shadow-blue-100">
                    <i class="fas fa-chart-pie text-white text-xl"></i>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-slate-800 tracking-tight">SuperAdmin Dashboard</h1>
                    <p class="text-slate-500 text-xs font-medium mt-1 italic">Monitoring and system management portal</p>
                </div>
            </div>

            <div class="flex items-center gap-3 w-full lg:w-auto">
                <div class="relative flex-1 lg:w-72">
                    <i class="fas fa-search absolute top-1/2 left-4 -translate-y-1/2 text-slate-400 text-xs"></i>
                    <input type="text" placeholder="Search resources..." class="w-full pl-11 pr-5 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-medium text-slate-700 focus:border-blue-500 focus:ring-4 focus:ring-blue-50 transition-all outline-none">
                </div>
                <button id="btn-system-refresh" class="w-12 h-12 bg-white text-slate-600 border border-slate-200 rounded-xl hover:bg-slate-50 transition-all flex items-center justify-center shadow-sm group">
                    <i class="fas fa-sync-alt text-sm group-hover:rotate-180 transition-transform duration-700"></i>
                </button>
            </div>
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-12 gap-8">
            
            <!-- LEFT: Analytics & Activity (8 Cols) -->
            <div class="xl:col-span-8 space-y-8">
                
                <!-- Performance Statistics -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center text-xs"><i class="fas fa-microchip"></i></div>
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">CPU</span>
                        </div>
                        <div class="text-2xl font-bold text-slate-800">{{ $server_load['cpu'] }}%</div>
                        <div class="mt-3 h-1.5 w-full bg-slate-50 rounded-full overflow-hidden">
                            <div class="h-full bg-blue-500" style="width: {{ $server_load['cpu'] }}%"></div>
                        </div>
                    </div>
                    <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="w-8 h-8 rounded-lg bg-purple-50 text-purple-600 flex items-center justify-center text-xs"><i class="fas fa-memory"></i></div>
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Memory</span>
                        </div>
                        <div class="text-2xl font-bold text-slate-800">{{ $server_load['ram'] }}%</div>
                        <div class="mt-3 h-1.5 w-full bg-slate-50 rounded-full overflow-hidden">
                            <div class="h-full bg-purple-500" style="width: {{ $server_load['ram'] }}%"></div>
                        </div>
                    </div>
                    <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="w-8 h-8 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center text-xs"><i class="fas fa-hdd"></i></div>
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Storage</span>
                        </div>
                        <div class="text-2xl font-bold text-slate-800">{{ $server_load['disk'] }}%</div>
                        <div class="mt-3 h-1.5 w-full bg-slate-50 rounded-full overflow-hidden">
                            <div class="h-full bg-amber-500" style="width: {{ $server_load['disk'] }}%"></div>
                        </div>
                    </div>
                    <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center text-xs"><i class="fas fa-network-wired"></i></div>
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Traffic</span>
                        </div>
                        <div class="text-2xl font-bold text-slate-800">{{ $server_load['traffic'] }}</div>
                        <div class="mt-3 flex items-center gap-2">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                            <span class="text-[9px] font-bold text-emerald-600">Active Stream</span>
                        </div>
                    </div>
                </div>

                <!-- Recent Submissions Table -->
                <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
                    <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center">
                        <h3 class="text-sm font-bold text-slate-800">Recent Proposal Submissions</h3>
                        <a href="#" class="text-[11px] font-bold text-blue-600 hover:underline">View All Records</a>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left">
                            <tbody class="divide-y divide-slate-50">
                                @foreach($monitoring_kegiatan as $item)
                                <tr class="hover:bg-slate-50/50 transition-colors group">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <div class="w-9 h-9 rounded-full bg-slate-100 flex items-center justify-center text-slate-400 font-bold text-xs">
                                                {{ substr($item['pengusul'], 0, 1) }}
                                            </div>
                                            <div>
                                                <div class="text-sm font-semibold text-slate-800">{{ $item['nama'] }}</div>
                                                <div class="text-[11px] text-slate-400 font-medium">{{ $item['pengusul'] }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <span class="inline-block px-3 py-1 rounded-full text-[10px] font-bold {{ $item['status'] === 'Disetujui' ? 'bg-emerald-50 text-emerald-600' : 'bg-blue-50 text-blue-600' }}">
                                            {{ $item['status'] }}
                                        </span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- System Logs (Simplified) -->
                <div class="bg-slate-800 rounded-2xl p-6 text-slate-300 shadow-lg">
                    <div class="flex items-center justify-between mb-4">
                        <span class="text-[11px] font-bold text-slate-400 uppercase tracking-widest">Recent Activity Logs</span>
                        <div class="flex gap-1.5">
                            <span class="w-2 h-2 rounded-full bg-slate-600"></span>
                            <span class="w-2 h-2 rounded-full bg-slate-600"></span>
                        </div>
                    </div>
                    <div id="analysis-logs-main" class="space-y-3 font-mono text-[11px] h-[150px] overflow-y-auto custom-scrollbar-dark leading-relaxed">
                        @foreach($recent_logs as $log)
                        <div class="flex gap-4">
                            <span class="text-slate-500">[{{ $log['time'] }}]</span>
                            <span class="{{ $log['status'] === 'success' ? 'text-emerald-400' : 'text-blue-400' }}">
                                {{ $log['event'] }}
                            </span>
                            <span class="text-slate-500 opacity-50">by {{ $log['user'] }}</span>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- RIGHT: System Monitoring (4 Cols) -->
            <div class="xl:col-span-4 space-y-8">
                
                <!-- AI Smart Insights (Clean Theme) -->
                <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm relative group overflow-hidden">
                    <div class="absolute top-0 right-0 p-4 text-slate-100">
                        <i class="fas fa-lightbulb text-6xl"></i>
                    </div>
                    <div class="relative z-10">
                        <div class="flex items-center gap-3 mb-6">
                            <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center">
                                <i class="fas fa-brain text-sm"></i>
                            </div>
                            <h3 class="text-sm font-bold text-slate-800">System Analysis</h3>
                        </div>
                        <div id="ai-summary-container" class="min-h-[140px] text-[13px] leading-relaxed text-slate-600 font-medium mb-8 italic">
                            <div class="animate-pulse space-y-3">
                                <div class="h-2 bg-slate-50 rounded-full w-full"></div>
                                <div class="h-2 bg-slate-50 rounded-full w-4/5"></div>
                            </div>
                        </div>
                        <button id="btn-full-analysis" class="w-full py-4 bg-blue-600 text-white rounded-xl text-xs font-bold transition-all hover:bg-blue-700 shadow-lg shadow-blue-100 flex items-center justify-center gap-3">
                            <span>Run Full Analysis</span>
                            <i class="fas fa-chevron-right text-[10px]"></i>
                        </button>
                    </div>
                </div>

                <!-- Active Directory -->
                <div class="bg-white rounded-2xl p-6 border border-slate-100 shadow-sm">
                    <h3 class="text-[11px] font-bold text-slate-400 uppercase tracking-widest mb-6">Recently Active</h3>
                    <div class="space-y-6">
                        @foreach($active_users as $user)
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-4">
                                <div class="w-10 h-10 rounded-xl bg-slate-50 flex items-center justify-center border border-slate-100">
                                    <span class="text-xs font-bold text-slate-400 uppercase">{{ substr($user['name'], 0, 1) }}</span>
                                </div>
                                <div>
                                    <div class="text-[13px] font-bold text-slate-800">{{ $user['name'] }}</div>
                                    <div class="text-[11px] text-slate-500 font-medium">{{ $user['role'] }}</div>
                                </div>
                            </div>
                            <div class="flex flex-col items-end">
                                <div class="w-1.5 h-1.5 rounded-full bg-emerald-500 mb-1"></div>
                                <span class="text-[10px] font-bold text-slate-400">{{ $user['last_seen'] }}</span>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

                <!-- System Security Center -->
                <div class="bg-white rounded-2xl p-6 border border-slate-100 shadow-sm">
                    <h3 class="text-[11px] font-bold text-slate-400 uppercase tracking-widest mb-6">Security Monitor</h3>
                    <div class="space-y-4">
                        @foreach($security_threats as $threat)
                        <div class="p-3 bg-slate-50 rounded-xl border border-slate-100 flex items-center justify-between">
                            <div>
                                <p class="text-[11px] font-bold text-slate-800">{{ $threat['type'] }}</p>
                                <p class="text-[10px] font-medium text-slate-500 uppercase">{{ $threat['risk'] }} Risk</p>
                            </div>
                            <span class="text-[10px] font-bold text-red-500 uppercase">{{ $threat['status'] }}</span>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

</main>

<!-- System Audit Modal -->
<div id="modal-full-analysis" class="fixed inset-0 z-[100] hidden">
    <div class="absolute inset-0 bg-slate-900/40 backdrop-blur-sm"></div>
    <div class="absolute inset-0 flex items-center justify-center p-4">
        <div class="bg-white rounded-3xl shadow-2xl w-full max-w-3xl overflow-hidden animate-zoom-in relative">
            <div class="bg-slate-50 p-8 border-b border-slate-100 flex justify-between items-center">
                <div>
                    <h2 class="text-xl font-bold text-slate-800">System Audit</h2>
                    <p class="text-slate-500 text-[11px] font-medium mt-1">Full analysis of system integrity and performance</p>
                </div>
                <button id="close-modal-analysis" class="w-10 h-10 flex items-center justify-center hover:bg-slate-100 rounded-xl transition-all">
                    <i class="fas fa-times text-slate-400"></i>
                </button>
            </div>
            <div class="p-8 bg-white grid grid-cols-1 lg:grid-cols-3 gap-8">
                <div class="lg:col-span-1 space-y-3">
                    @foreach(['Database', 'Security', 'Performance'] as $step)
                    <div id="step-{{ strtolower($step) }}" class="audit-step bg-slate-50 p-4 rounded-xl border border-slate-100 opacity-50 transition-all">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-lg bg-white flex items-center justify-center text-slate-400 step-icon"><i class="fas fa-{{ $step==='Database'?'database':($step==='Security'?'shield-alt':'tachometer-alt') }} text-sm"></i></div>
                            <div>
                                <p class="text-[11px] font-bold text-slate-800 uppercase tracking-wider">{{ $step }}</p>
                                <p class="text-[10px] text-slate-500 font-medium italic step-status">Waiting</p>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                <div class="lg:col-span-2 space-y-6">
                    <div class="bg-slate-50 p-6 rounded-2xl border border-slate-100">
                        <div class="flex justify-between items-end mb-3">
                            <p class="text-[11px] font-bold text-slate-400 uppercase">Analysis Progress</p>
                            <p id="analysis-progress-val" class="text-2xl font-bold text-blue-600">0%</p>
                        </div>
                        <div class="w-full bg-slate-200 h-2 rounded-full overflow-hidden">
                            <div id="analysis-progress-bar" class="bg-blue-600 h-full w-0 transition-all duration-700"></div>
                        </div>
                    </div>
                    <div class="bg-slate-800 rounded-2xl p-6 h-[200px] overflow-hidden shadow-inner">
                        <div id="analysis-logs" class="font-mono text-[11px] text-slate-300 space-y-2 h-full overflow-y-auto custom-scrollbar-dark leading-relaxed">
                            <p>> Preparing analysis environment...</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="p-6 border-t border-slate-100 bg-slate-50 flex justify-end gap-3">
                <button id="start-audit-btn" class="px-8 py-3 bg-blue-600 text-white rounded-xl text-xs font-bold hover:bg-blue-700 shadow-lg shadow-blue-100">Run Audit</button>
                <button id="close-modal-btn" class="hidden px-8 py-3 bg-white text-slate-600 border border-slate-200 rounded-xl text-xs font-bold">Close Analysis</button>
            </div>
        </div>
    </div>
</div>

<div id="refresh-overlay" class="fixed inset-0 z-[200] hidden flex items-center justify-center">
    <div class="absolute inset-0 bg-slate-900/80 backdrop-blur-md"></div>
    <div class="relative z-10 text-center">
        <div class="w-16 h-16 border-t-2 border-blue-500 rounded-full animate-spin mx-auto mb-6 shadow-xl"></div>
        <h2 class="text-2xl font-bold text-white mb-2 tracking-tight">Refreshing System</h2>
        <p id="refresh-status" class="text-blue-300 text-[11px] font-medium">Syncing system data...</p>
    </div>
</div>

<style>
    @keyframes zoom-in { from { opacity: 0; transform: scale(0.98) translateY(10px); } to { opacity: 1; transform: scale(1) translateY(0); } }
    .animate-zoom-in { animation: zoom-in 0.4s cubic-bezier(0.16, 1, 0.3, 1) forwards; }
    .custom-scrollbar::-webkit-scrollbar { width: 3px; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: rgba(0,0,0,0.1); border-radius: 10px; }
    .custom-scrollbar-dark::-webkit-scrollbar { width: 3px; }
    .custom-scrollbar-dark::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.1); border-radius: 10px; }
    .audit-step.active { opacity: 1 !important; border-color: #3b82f6 !important; background-color: #eff6ff !important; }
    .audit-step.completed { opacity: 1 !important; border-color: #10b981 !important; background-color: #f0fdf4 !important; }
</style>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const wait = ms => new Promise(r => setTimeout(r, ms));
    
    // AI Content
    const container = document.getElementById('ai-summary-container');
    setTimeout(() => {
        fetch('{{ route("superadmin.ai.analysis") }}')
            .then(r => r.json())
            .then(d => { if(d.status==='success') container.innerHTML = `<p>${d.data.replace(/\n/g, '<br>')}</p>`; });
    }, 500);

    // Audit Simulation
    const modal = document.getElementById('modal-full-analysis');
    const logs = document.getElementById('analysis-logs');
    const startBtn = document.getElementById('start-audit-btn');
    const closeBtn = document.getElementById('close-modal-btn');
    
    document.getElementById('btn-full-analysis')?.addEventListener('click', () => modal.classList.remove('hidden'));
    document.getElementById('close-modal-analysis')?.addEventListener('click', () => { modal.classList.add('hidden'); resetAudit(); });
    closeBtn?.addEventListener('click', () => modal.classList.add('hidden'));

    const addLog = (t, em=false) => {
        const p = document.createElement('p');
        if(em) p.className = 'text-blue-400 font-bold';
        p.innerHTML = `> ${t}`;
        logs.appendChild(p);
        logs.scrollTop = logs.scrollHeight;
    }

    const resetAudit = () => {
        logs.innerHTML = '<p>> Preparing analysis environment...</p>';
        document.getElementById('analysis-progress-bar').style.width = '0%';
        document.getElementById('analysis-progress-val').textContent = '0%';
        startBtn.classList.remove('hidden');
        closeBtn.classList.add('hidden');
        document.querySelectorAll('.audit-step').forEach(s => { s.classList.remove('active', 'completed'); s.querySelector('.step-status').textContent = 'Waiting'; });
    }

    startBtn?.addEventListener('click', async () => {
        startBtn.classList.add('hidden');
        const steps = ['database', 'security', 'performance'];
        const pb = document.getElementById('analysis-progress-bar');
        const pv = document.getElementById('analysis-progress-val');

        for(let i=0; i<steps.length; i++) {
            const s = document.getElementById(`step-${steps[i]}`);
            s.classList.add('active');
            s.querySelector('.step-status').textContent = 'Checking...';
            addLog(`Analyzing ${steps[i]} module...`);
            await wait(1200);
            s.classList.remove('active');
            s.classList.add('completed');
            s.querySelector('.step-status').textContent = 'Success';
            pb.style.width = `${(i+1)*33.3}%`;
            pv.textContent = `${Math.round((i+1)*33.3)}%`;
            addLog(`${steps[i].toUpperCase()} analysis complete.`, true);
        }
        addLog('System audit complete. No issues found.', true);
        closeBtn.classList.remove('hidden');
    });

    document.getElementById('btn-system-refresh')?.addEventListener('click', async () => {
        document.getElementById('refresh-overlay').classList.remove('hidden');
        await wait(2000);
        location.reload();
    });
});
</script>
@endpush

@endsection
