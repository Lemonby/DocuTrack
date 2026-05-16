// public/assets/js/superadmin/dashboard.js

document.addEventListener('DOMContentLoaded', function() {
    // ===============================================
    // DATA INITIALIZATION
    // ===============================================
    // Variables declared in the view are accessed here
    const phpDataKAK = window.dataKAK || [];
    const phpDataLPJ = window.dataLPJ || [];
    const listProdi = window.listProdi || [];
    
    console.log('📊 Data Initialization:');
    console.log('- KAK:', phpDataKAK.length, 'items');
    console.log('- LPJ:', phpDataLPJ.length, 'items');
    console.log('- Prodi:', listProdi);

    // Generate simulation data if needed
    function generateSimulationDataProdi(count) {
        const statuses = ['menunggu', 'disetujui', 'ditolak', 'revisi'];
        const statusWeights = [0.35, 0.40, 0.15, 0.10];
        const data = [];
        const now = new Date();
        
        for (let i = 0; i < count; i++) {
            const randomDaysAgo = Math.floor(Math.random() * 365);
            const randomHours = Math.floor(Math.random() * 24);
            const date = new Date(now);
            date.setDate(date.getDate() - randomDaysAgo);
            date.setHours(randomHours, 0, 0, 0);
            
            const random = Math.random();
            let cumulativeWeight = 0;
            let selectedStatus = statuses[0];
            
            for (let j = 0; j < statuses.length; j++) {
                cumulativeWeight += statusWeights[j];
                if (random <= cumulativeWeight) {
                    selectedStatus = statuses[j];
                    break;
                }
            }
            
            const randomProdi = listProdi[Math.floor(Math.random() * listProdi.length)];
            
            data.push({
                id: i + 1,
                created_at: date.toISOString(),
                tanggal: date.toISOString().split('T')[0],
                status: selectedStatus,
                jurusan: randomProdi,
                nama: 'Usulan ' + (i + 1)
            });
        }
        
        return data.sort((a, b) => new Date(b.created_at) - new Date(a.created_at));
    }

    const hasValidData = phpDataKAK.length > 0 && phpDataKAK[0].hasOwnProperty('created_at');
    
    let chartDataKAK = phpDataKAK;
    // let chartDataLPJ = phpDataLPJ; // Not currently used in charts but kept for consistency

    if (!hasValidData || phpDataKAK.length < 10) {
        console.log('⚠️ Menggunakan data simulasi untuk grafik');
        chartDataKAK = generateSimulationDataProdi(80);
        // chartDataLPJ = generateSimulationDataProdi(60);
    } else {
        console.log('✅ Menggunakan data asli');
    }

    // ===============================================
    // HELPER FUNCTIONS
    // ===============================================
    function filterDataByPeriod(data, period) {
        const now = new Date();
        const today = new Date(now.getFullYear(), now.getMonth(), now.getDate());
        
        return data.filter(function(item) {
            const itemDate = new Date(item.created_at || item.tanggal);
            
            switch(period) {
                case 'today':
                    return itemDate >= today;
                case 'week':
                    const weekAgo = new Date(today);
                    weekAgo.setDate(weekAgo.getDate() - 7);
                    return itemDate >= weekAgo;
                case 'month':
                    const monthAgo = new Date(today);
                    monthAgo.setMonth(monthAgo.getMonth() - 1);
                    return itemDate >= monthAgo;
                case 'year':
                    const yearAgo = new Date(today);
                    yearAgo.setFullYear(yearAgo.getFullYear() - 1);
                    return itemDate >= yearAgo;
                default:
                    return true;
            }
        });
    }

    function getProdiData(period) {
        // Concatenate KAK and LPJ if desired, or just use KAK for now based on previous logic
        // The original script concatenated global window.dataKAK and window.dataLPJ (which were simulations in the original script block logic if empty)
        // Here we use the resolved chartDataKAK.
        
        const allData = chartDataKAK; 
        const filteredData = filterDataByPeriod(allData, period);
        
        const prodiCounts = {};
        listProdi.forEach(function(prodi) {
            prodiCounts[prodi] = 0;
        });
        
        filteredData.forEach(function(item) {
            const prodi = item.jurusan;
            if (prodiCounts.hasOwnProperty(prodi)) {
                prodiCounts[prodi]++;
            }
        });
        
        const sortedProdi = Object.keys(prodiCounts).sort((a, b) => prodiCounts[b] - prodiCounts[a]);
        
        const values = sortedProdi.map(prodi => prodiCounts[prodi]);
        
        return { labels: sortedProdi, data: values };
    }

    function updateSummaryStats(data) {
        const total = data.reduce((sum, val) => sum + val, 0);
        const max = Math.max(...data);
        const avg = data.length > 0 ? (total / data.length).toFixed(1) : 0;
        
        const elTotalProdi = document.getElementById('totalProdi');
        const elMaxUsulan = document.getElementById('maxUsulan');
        const elAvgUsulan = document.getElementById('avgUsulan');
        const elTotalUsulanProdi = document.getElementById('totalUsulanProdi');

        if(elTotalProdi) elTotalProdi.textContent = data.length;
        if(elMaxUsulan) elMaxUsulan.textContent = max === -Infinity ? 0 : max;
        if(elAvgUsulan) elAvgUsulan.textContent = avg;
        if(elTotalUsulanProdi) elTotalUsulanProdi.textContent = total;
    }

    // ===============================================
    // COLOR PALETTE
    // ===============================================
    const colorPalette = [
        'rgba(59, 130, 246, 0.85)',   // Blue
    ];

    const borderColorPalette = [
        'rgb(59, 130, 246)',
    ];

    // ===============================================
    // BAR CHART - USULAN PER PRODI
    // ===============================================
    const prodiChartCanvas = document.getElementById('prodiChart');
    let prodiChart;

    if (prodiChartCanvas) {
        const prodiCtx = prodiChartCanvas.getContext('2d');
        let currentPeriod = 'today';
        const initialProdiData = getProdiData(currentPeriod);
        
        prodiChart = new Chart(prodiCtx, {
            type: 'bar',
            data: {
                labels: initialProdiData.labels,
                datasets: [{
                    label: 'Jumlah Usulan',
                    data: initialProdiData.data,
                    backgroundColor: colorPalette,
                    borderColor: borderColorPalette,
                    borderWidth: 2,
                    borderRadius: 8,
                    borderSkipped: false,
                    barThickness: 'flex',
                    maxBarThickness: 60
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.85)',
                        padding: 16,
                        cornerRadius: 12,
                        titleFont: {
                            size: 15,
                            weight: 'bold'
                        },
                        bodyFont: {
                            size: 14
                        },
                        displayColors: true,
                        callbacks: {
                            label: function(context) {
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = total > 0 ? ((context.parsed.y / total) * 100).toFixed(1) : 0;
                                return 'Jumlah: ' + context.parsed.y + ' usulan (' + percentage + '%)';
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(6, 182, 212, 0.08)',
                            drawBorder: false
                        },
                        ticks: {
                            font: {
                                size: 12,
                                weight: '500'
                            },
                            color: '#6b7280',
                            padding: 8,
                            precision: 0
                        }
                    },
                    x: {
                        grid: {
                            display: false,
                            drawBorder: false
                        },
                        ticks: {
                            font: {
                                size: 11,
                                weight: '600'
                            },
                            color: '#4b5563',
                            maxRotation: 45,
                            minRotation: 25,
                            padding: 8
                        }
                    }
                },
                animation: {
                    duration: 750,
                    easing: 'easeInOutQuart'
                }
            }
        });

        updateSummaryStats(initialProdiData.data);

        // ===============================================
        // FILTER BUTTONS
        // ===============================================
        const filterButtons = document.querySelectorAll('.filter-btn-prodi');
        filterButtons.forEach(function(btn) {
            btn.addEventListener('click', function() {
                filterButtons.forEach(function(b) {
                    b.classList.remove('active');
                    b.classList.remove('bg-gradient-to-r', 'from-indigo-500', 'to-purple-600', 'text-white');
                    b.classList.add('bg-white', 'border-2', 'border-gray-200', 'text-gray-700');
                });
                this.classList.add('active');
                
                currentPeriod = this.getAttribute('data-filter');
                const newData = getProdiData(currentPeriod);
                
                prodiChart.data.labels = newData.labels;
                prodiChart.data.datasets[0].data = newData.data;
                prodiChart.update('active');
                
                updateSummaryStats(newData.data);
            });
        });
    }

    // ===============================================
    // DONUT CHART
    // ===============================================
    const donutChartCanvas = document.getElementById('donutChart');
    if (donutChartCanvas && window.donutStats) {
        const donutCtx = donutChartCanvas.getContext('2d');
        const stats = window.donutStats;

        new Chart(donutCtx, {
            type: 'doughnut',
            data: {
                labels: ['Disetujui', 'Menunggu', 'Revisi', 'Ditolak'],
                datasets: [{
                    data: [
                        stats.disetujui || 0,
                        stats.menunggu || 0,
                        stats.revisi || 0,
                        stats.ditolak || 0
                    ],
                    backgroundColor: [
                        '#22c55e',
                        '#3b82f6',
                        '#f97316',
                        '#ef4444'
                    ],
                    borderWidth: 4,
                    borderColor: '#ffffff',
                    hoverOffset: 12,
                    hoverBorderWidth: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                cutout: '72%',
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.85)',
                        padding: 16,
                        cornerRadius: 12,
                        titleFont: {
                            size: 15,
                            weight: 'bold'
                        },
                        bodyFont: {
                            size: 14
                        },
                        displayColors: true,
                        boxWidth: 15,
                        boxHeight: 15,
                        boxPadding: 8,
                        callbacks: {
                            label: function(context) {
                                let total = 0;
                                for (let i = 0; i < context.dataset.data.length; i++) {
                                    total += context.dataset.data[i];
                                }
                                const value = context.parsed;
                                const percentage = total > 0 ? ((value / total) * 100).toFixed(1) : 0;
                                return context.label + ': ' + value + ' (' + percentage + '%)';
                            }
                        }
                    }
                },
                animation: {
                    animateRotate: true,
                    animateScale: true,
                    duration: 1000,
                    easing: 'easeInOutQuart'
                }
            }
        });
    }
});
