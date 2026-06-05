<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Live Tracking - Supervisor</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; font-weight: bold; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h2 { margin: 0; padding: 0; }
        .header p { margin: 5px 0; color: #555; }
        .section-title { margin-top: 30px; font-size: 14px; font-weight: bold; border-bottom: 2px solid #333; padding-bottom: 5px; }
        .stats-grid { display: flex; gap: 10px; margin: 10px 0; }
        .stat-box { border: 1px solid #ddd; padding: 10px; text-align: center; flex: 1; }
        .stat-value { font-size: 20px; font-weight: bold; }
        .stat-label { font-size: 10px; color: #666; margin-top: 4px; }
        .badge-fast { background: #dcfce7; color: #15803d; padding: 2px 8px; border-radius: 4px; font-size: 10px; }
        .badge-normal { background: #fef3c7; color: #a16207; padding: 2px 8px; border-radius: 4px; font-size: 10px; }
        .badge-slow { background: #fee2e2; color: #b91c1c; padding: 2px 8px; border-radius: 4px; font-size: 10px; }
    </style>
</head>
<body>
    <div class="header">
        <h2>Laporan Live Tracking Supervisor</h2>
        <p>{{ $instance->instance_name ?? 'Instansi' }}</p>
        <p>Tanggal: {{ $date }} | Dicetak: {{ now()->format('d M Y H:i:s') }}</p>
    </div>

    {{-- Summary Stats --}}
    <div class="section-title">Ringkasan Hari Ini</div>
    <table style="width: 60%; margin-top: 5px;">
        @foreach($statCards as $card)
        <tr>
            <th style="width: 50%;">{{ $card['label'] }}</th>
            <td>{{ $card['value'] }}</td>
        </tr>
        @endforeach
    </table>

    {{-- Registration Type --}}
    <div class="section-title">Tipe Pendaftaran</div>
    <table style="width: 40%; margin-top: 5px;">
        <tr>
            <th>Online</th>
            <td>{{ $registrationTypes['online'] }}</td>
        </tr>
        <tr>
            <th>Onsite</th>
            <td>{{ $registrationTypes['onsite'] }}</td>
        </tr>
    </table>

    {{-- Operator Performance --}}
    <div class="section-title">Kinerja Operator</div>
    <table>
        <thead>
            <tr>
                <th>Loket</th>
                <th>Operator</th>
                <th>Rata-rata Waktu (menit)</th>
                <th>Total Dilayani</th>
                <th>Hari Ini</th>
                <th>Cepat (1-2m)</th>
                <th>Normal (3-5m)</th>
                <th>Lambat (6m+)</th>
                <th>Penilaian</th>
            </tr>
        </thead>
        <tbody>
            @forelse($operatorPerformance as $perf)
                <tr>
                    <td>{{ $perf->counter_name }}</td>
                    <td>{{ $perf->operator_name }}</td>
                    <td>{{ number_format($perf->avg_service_time, 1) }}</td>
                    <td>{{ $perf->total_served }}</td>
                    <td>{{ $perf->today_served }}</td>
                    <td>{{ $perf->fast_services }}</td>
                    <td>{{ $perf->medium_services }}</td>
                    <td>{{ $perf->slow_services }}</td>
                    <td>
                        @if($perf->avg_service_time <= 2)
                            <span class="badge-fast">Sangat Cepat</span>
                        @elseif($perf->avg_service_time <= 5)
                            <span class="badge-normal">Normal</span>
                        @else
                            <span class="badge-slow">Perlu Ditingkatkan</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" style="text-align: center;">Tidak ada data operator</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{-- Counter Status --}}
    <div class="section-title">Status Loket</div>
    <table>
        <thead>
            <tr>
                <th>Loket</th>
                <th>Operator</th>
                <th>Status</th>
                <th>Antrean Saat Ini</th>
            </tr>
        </thead>
        <tbody>
            @forelse($counters as $counter)
                <tr>
                    <td>{{ $counter->name }}</td>
                    <td>{{ $counter->operatorName ?? '-' }}</td>
                    <td>
                        @if($counter->status === 'serving')
                            Melayani
                        @elseif($counter->status === 'calling')
                            Memanggil
                        @else
                            Idle
                        @endif
                    </td>
                    <td>{{ $counter->current_queue ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" style="text-align: center;">Tidak ada loket aktif</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
