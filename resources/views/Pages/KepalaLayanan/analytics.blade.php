{{-- ===== TAB: ANALYTICS ===== --}}
<div x-show="activeTab === 'analytics'" class="space-y-6">

    {{-- Service Distribution Bar Chart --}}
    <div class="bg-white rounded-2xl border shadow-sm">
        <div class="p-5 border-b">
            <h3 class="font-bold">Distribusi per Layanan</h3>
            <p class="text-sm text-gray-500 mt-0.5">Total antrean per jenis layanan</p>
        </div>
        <div class="p-5">
            <canvas id="chart-service-distribution" style="height:300px"></canvas>
        </div>
    </div>

    {{-- Hourly Trend Line Chart --}}
    <div class="bg-white rounded-2xl border shadow-sm">
        <div class="p-5 border-b">
            <h3 class="font-bold">Tren Per Jam</h3>
            <p class="text-sm text-gray-500 mt-0.5">Jumlah pengunjung per jam</p>
        </div>
        <div class="p-5">
            <canvas id="chart-hourly-trend" style="height:300px"></canvas>
        </div>
    </div>

</div>

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {

            // ===== Bar Chart: Service Distribution =====
            const barCanvas = document.getElementById('chart-service-distribution');
            if (barCanvas) {
                const serviceData = [{
                        name: 'Pembuatan KTP',
                        completed: 50,
                        waiting: 10
                    },
                    {
                        name: 'Pembayaran Pajak',
                        completed: 40,
                        waiting: 5
                    },
                    {
                        name: 'Pengaduan',
                        completed: 30,
                        waiting: 15
                    },
                ];
                new Chart(barCanvas.getContext('2d'), {
                    type: 'bar',
                    data: {
                        labels: serviceData.map(d => d.name),
                        datasets: [{
                                label: 'Selesai',
                                data: serviceData.map(d => d.completed),
                                backgroundColor: '#10B981',
                                borderRadius: 4,
                            },
                            {
                                label: 'Menunggu',
                                data: serviceData.map(d => d.waiting),
                                backgroundColor: '#F59E0B',
                                borderRadius: 4,
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'top'
                            }
                        },
                        scales: {
                            x: {
                                grid: {
                                    display: false
                                }
                            },
                            y: {
                                beginAtZero: true,
                                grid: {
                                    color: '#f1f5f9'
                                }
                            }
                        }
                    }
                });
            }

            // ===== Line Chart: Hourly Trend =====
            const lineCanvas = document.getElementById('chart-hourly-trend');
            if (lineCanvas) {
                const hourlyData = [{
                        hour: '08:00',
                        count: 10
                    },
                    {
                        hour: '09:00',
                        count: 20
                    },
                    {
                        hour: '10:00',
                        count: 30
                    },
                    {
                        hour: '11:00',
                        count: 25
                    },
                    {
                        hour: '12:00',
                        count: 15
                    },
                ];
                new Chart(lineCanvas.getContext('2d'), {
                    type: 'line',
                    data: {
                        labels: hourlyData.map(d => d.hour),
                        datasets: [{
                            label: 'Jumlah Pengunjung',
                            data: hourlyData.map(d => d.count),
                            borderColor: '#3B82F6',
                            backgroundColor: 'rgba(59,130,246,0.1)',
                            borderWidth: 2,
                            tension: 0.4,
                            fill: true,
                            pointRadius: 4,
                            pointBackgroundColor: '#3B82F6',
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'top'
                            }
                        },
                        scales: {
                            x: {
                                grid: {
                                    display: false
                                }
                            },
                            y: {
                                beginAtZero: true,
                                grid: {
                                    color: '#f1f5f9'
                                }
                            }
                        }
                    }
                });
            }
        });
    </script>
@endpush
