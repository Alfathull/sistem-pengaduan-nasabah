<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Laporan Rekapitulasi Pengaduan</title>
    <style>
        * { font-family: DejaVu Sans, sans-serif; }
        body { color: #0b1f3a; font-size: 12px; margin: 0; }
        .header { border-bottom: 3px solid #0b1f3a; padding-bottom: 10px; margin-bottom: 16px; }
        .header-table { width: 100%; border: none; border-collapse: collapse; margin: 0; }
        .header-table td { border: none; padding: 0; vertical-align: middle; }
        .mark { width: 40px; }
        .brand { color: #0b1f3a; font-size: 20px; font-weight: bold; letter-spacing: -0.5px; }
        .brand .accent { color: #c8962e; }
        .subtitle { color: #5b6577; font-size: 11px; margin-top: 2px; }
        h2 { color: #0b1f3a; font-size: 14px; margin: 18px 0 6px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        th, td { border: 1px solid #e3e7ee; padding: 6px 8px; text-align: left; }
        th { background: #0b1f3a; color: #fff; font-size: 11px; }
        td { font-size: 11px; }
        .summary-table td { text-align: center; }
        .total-box { background: #eef3fa; border: 1px solid #b4c8e6; padding: 10px; border-radius: 4px; margin-bottom: 10px; }
        .total-box .label { color: #5b6577; font-size: 11px; }
        .total-box .value { color: #0b1f3a; font-size: 22px; font-weight: bold; }
        .meta { color: #5b6577; font-size: 10px; }
        .footer { margin-top: 20px; border-top: 1px solid #e3e7ee; padding-top: 8px; color: #9aa3b2; font-size: 9px; text-align: center; }
    </style>
</head>
<body>
    <div class="header">
        <table class="header-table">
            <tr>
                <td class="mark">
                    <svg width="34" height="34" viewBox="0 0 48 48" xmlns="http://www.w3.org/2000/svg">
                        <path d="M24 3.5 7 9.2v12.5c0 10.6 6.9 18.4 17 22.8 10.1-4.4 17-12.2 17-22.8V9.2L24 3.5Z" fill="#0B1F3A"/>
                        <path d="M16 16.5h16c1.1 0 2 .9 2 2v8.5c0 1.1-.9 2-2 2H22.6l-4.3 3.6c-.7.6-1.8.1-1.8-.8v-2.8H16c-1.1 0-2-.9-2-2V18.5c0-1.1.9-2 2-2Z" fill="#FFFFFF"/>
                        <path d="m19.5 22.8 3 3 5.4-5.6" stroke="#C8962E" stroke-width="2.4" fill="none" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </td>
                <td>
                    <div class="brand">Si<span class="accent">Pe</span>Na</div>
                    <div class="subtitle">Laporan Rekapitulasi Pengaduan Nasabah &mdash; Bank Sumsel Babel Cabang Indralaya</div>
                </td>
            </tr>
        </table>
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
