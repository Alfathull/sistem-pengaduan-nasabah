<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Laporan Rekapitulasi Pengaduan</title>
    <style>
        * { font-family: DejaVu Sans, sans-serif; }
        body { color: #1f2937; font-size: 12px; margin: 0; }
        .header { border-bottom: 3px solid #1e3a8a; padding-bottom: 10px; margin-bottom: 16px; }
        .brand { color: #1e3a8a; font-size: 20px; font-weight: bold; }
        .subtitle { color: #6b7280; font-size: 11px; margin-top: 2px; }
        h2 { color: #1e3a8a; font-size: 14px; margin: 18px 0 6px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        th, td { border: 1px solid #d1d5db; padding: 6px 8px; text-align: left; }
        th { background: #1e3a8a; color: #fff; font-size: 11px; }
        td { font-size: 11px; }
        .summary-table td { text-align: center; }
        .total-box { background: #eff6ff; border: 1px solid #bfdbfe; padding: 10px; border-radius: 4px; margin-bottom: 10px; }
        .total-box .label { color: #6b7280; font-size: 11px; }
        .total-box .value { color: #1e3a8a; font-size: 22px; font-weight: bold; }
        .meta { color: #6b7280; font-size: 10px; }
        .footer { margin-top: 20px; border-top: 1px solid #d1d5db; padding-top: 8px; color: #9ca3af; font-size: 9px; text-align: center; }
    </style>
</head>
<body>
    <div class="header">
        <div class="brand">SiPeNa</div>
        <div class="subtitle">Laporan Rekapitulasi Pengaduan Nasabah &mdash; Bank Sumsel Babel Cabang Indralaya</div>
    </div>

    <p class="meta">
        Periode:
        {{ $report['dateFrom'] ? \Illuminate\Support\Carbon::parse($report['dateFrom'])->translatedFormat('d M Y') : 'Awal' }}
        s.d.
        {{ $report['dateTo'] ? \Illuminate\Support\Carbon::parse($report['dateTo'])->translatedFormat('d M Y') : 'Sekarang' }}
        <br>
        Dicetak: {{ $generatedAt->translatedFormat('d F Y H:i') }}
    </p>

    <div class="total-box">
        <div class="label">Total Pengaduan</div>
        <div class="value">{{ $report['total'] }}</div>
    </div>

    <h2>Rekap per Status</h2>
    <table class="summary-table">
        <thead><tr><th>Status</th><th>Jumlah</th></tr></thead>
        <tbody>
            @foreach ($report['byStatus'] as $label => $count)
                <tr><td style="text-align:left">{{ $label }}</td><td>{{ $count }}</td></tr>
            @endforeach
        </tbody>
    </table>

    <h2>Rekap per Kategori</h2>
    <table class="summary-table">
        <thead><tr><th>Kategori</th><th>Jumlah</th></tr></thead>
        <tbody>
            @foreach ($report['byCategory'] as $label => $count)
                <tr><td style="text-align:left">{{ $label }}</td><td>{{ $count }}</td></tr>
            @endforeach
        </tbody>
    </table>

    <h2>Rekap per Departemen (Eskalasi)</h2>
    <table class="summary-table">
        <thead><tr><th>Departemen</th><th>Jumlah</th></tr></thead>
        <tbody>
            @foreach ($report['byDepartment'] as $label => $count)
                <tr><td style="text-align:left">{{ $label }}</td><td>{{ $count }}</td></tr>
            @endforeach
        </tbody>
    </table>

    <h2>Tren Pengaduan per Bulan</h2>
    <table class="summary-table">
        <thead><tr><th>Bulan</th><th>Jumlah</th></tr></thead>
        <tbody>
            @foreach ($report['monthlyTrend'] as $label => $count)
                <tr><td style="text-align:left">{{ $label }}</td><td>{{ $count }}</td></tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Dokumen ini dihasilkan otomatis oleh sistem SiPeNa.
    </div>
</body>
</html>
