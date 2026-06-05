<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Antrean</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; font-weight: bold; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h2 { margin: 0; padding: 0; }
        .header p { margin: 5px 0; color: #555; }
    </style>
</head>
<body>
    <div class="header">
        <h2>Laporan Data Antrean</h2>
        <p>Dicetak pada: {{ now()->format('d M Y H:i:s') }}</p>
    </div>

    @if(isset($stats))
    <div style="margin-bottom: 20px;">
        <h3>Ringkasan Statistik</h3>
        <table style="width: 60%; margin-top: 5px;">
            <tr>
                <th style="width: 50%;">Total Antrean</th>
                <td>{{ $stats['totalQueue'] ?? 0 }}</td>
            </tr>
            <tr>
                <th>Selesai Dilayani</th>
                <td>{{ $stats['completedQueue'] ?? 0 }}</td>
            </tr>
            <tr>
                <th>Batal / Dilewati</th>
                <td>{{ $stats['cancelledQueue'] ?? 0 }}</td>
            </tr>
            <tr>
                <th>Tingkat Penyelesaian</th>
                <td>{{ $stats['completionRate'] ?? 0 }}%</td>
            </tr>
            <tr>
                <th>Rata-rata Waktu Tunggu</th>
                <td>{{ number_format($stats['avgWaitTime'] ?? 0, 1) }} menit</td>
            </tr>
            <tr>
                <th>Rata-rata Waktu Layanan</th>
                <td>{{ number_format($stats['avgServiceTime'] ?? 0, 1) }} menit</td>
            </tr>
        </table>
    </div>
    @endif

    <table>
        <thead>
            <tr>
                <th>No. Antrean</th>
                <th>Layanan</th>
                <th>Tipe</th>
                <th>Waktu Daftar</th>
                <th>Waktu Dilayani</th>
                <th>Waktu Selesai</th>
                <th>Durasi (menit)</th>
                <th>Operator</th>
                <th>Status</th>
                <th>Nama Pelanggan</th>
                <th>No. Telepon</th>
            </tr>
        </thead>
        <tbody>
            @foreach($queues as $queue)
                <tr>
                    <td>{{ $queue->queue_number }}</td>
                    <td>{{ $queue->service->service_name ?? '-' }}</td>
                    <td>{{ ucfirst($queue->queue_source) }}</td>
                    <td>{{ $queue->taken_time ? \Carbon\Carbon::parse($queue->queue_date . ' ' . $queue->taken_time)->format('d-m-Y H:i') : '-' }}</td>
                    <td>{{ $queue->service_start_time ? \Carbon\Carbon::parse($queue->service_start_time)->format('H:i') : '-' }}</td>
                    <td>{{ $queue->service_end_time ? \Carbon\Carbon::parse($queue->service_end_time)->format('H:i') : '-' }}</td>
                    <td>{{ $queue->service_duration ?? '-' }}</td>
                    <td>{{ $queue->counter->user->name ?? '-' }}</td>
                    <td>{{ ucfirst($queue->queue_status) }}</td>
                    <td>{{ $queue->customer->name ?? '-' }}</td>
                    <td>{{ $queue->customer->phone ?? '-' }}</td>
                </tr>
            @endforeach
            @if(count($queues) == 0)
                <tr>
                    <td colspan="11" style="text-align: center;">Tidak ada data pada periode ini</td>
                </tr>
            @endif
        </tbody>
    </table>
</body>
</html>
