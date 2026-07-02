<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Struk Antrean Kiosk</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            margin: 0;
            padding: 20px;
            color: #111;
        }

        .header {
            border-bottom: 2px dashed #999;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }

        .instansi {
            font-size: 18px;
            font-weight: bold;
            margin: 0;
            text-transform: uppercase;
        }

        .layanan {
            font-size: 14px;
            color: #444;
            margin-top: 5px;
        }

        .nomor-label {
            font-size: 14px;
            margin-bottom: 5px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .nomor-antrean {
            font-size: 48px;
            font-weight: bold;
            margin: 10px 0;
        }

        .nama {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 20px;
        }

        .footer {
            border-top: 2px dashed #999;
            padding-top: 10px;
            font-size: 11px;
            color: #555;
            margin-top: 20px;
            line-height: 1.4;
        }

        .info {
            font-size: 11px;
            color: #555;
            margin: 3px 0;
            line-height: 1.3;
        }

        .socmed {
            margin-top: 15px;
            font-size: 10px;
            color: #666;
            border-top: 1px dashed #ddd;
            padding-top: 8px;
        }

        .socmed p {
            margin: 2px 0;
        }

        .status-antrean {
            margin-top: 15px;
            padding-top: 10px;
            border-top: 1px dotted #ccc;
            font-size: 12px;
            color: #333;
        }

        .status-antrean p {
            margin: 4px 0;
        }
    </style>
</head>

<body>
    <div class="header">
        <p class="instansi">{{ $instance->instance_name }}</p>

        @if ($instance->address)
            <p class="info">{{ $instance->address }}</p>
        @endif

        <p class="info">
            @if ($instance->phone)
                Telp: {{ $instance->phone }}
            @endif
            @if ($instance->phone && $instance->whatsapp_number)
                |
            @endif
            @if ($instance->whatsapp_number)
                WA: {{ $instance->whatsapp_number }}
            @endif
        </p>

        <p class="layanan">{{ $layanan }}</p>
    </div>

    <p class="nomor-label">Nomor Antrean Anda</p>
    <p class="nomor-antrean">{{ $nomor }}</p>
    <p class="nama">{{ $nama }}</p>

    <div class="status-antrean">
        <p>Sedang Dilayani: <strong>{{ $sedang_dilayani }}</strong></p>
        <p>Antrean Terakhir: <strong>{{ $total_antrean }}</strong></p>
    </div>

    <div class="footer">
        <p style="margin-bottom: 8px;">Tanggal Cetak: {{ $tanggal }}</p>
        <p>Silakan menuju ruang tunggu dan perhatikan panggilan pada layar monitor.</p>

        @if ($instance->email || $instance->instagram || $instance->facebook)
            <div class="socmed">
                @if ($instance->email)
                    <p>Email: {{ $instance->email }}</p>
                @endif
                @if ($instance->instagram)
                    <p>IG: {{ $instance->instagram }}</p>
                @endif
                @if ($instance->facebook)
                    <p>FB: {{ $instance->facebook }}</p>
                @endif
            </div>
        @endif
    </div>
</body>

</html>
