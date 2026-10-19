<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Services\ComplaintReportService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\File;
use Symfony\Component\HttpFoundation\Response;

class ReportController extends Controller
{
    /**
     * Export laporan rekapitulasi pengaduan ke PDF.
     */
    public function exportPdf(Request $request, ComplaintReportService $service): Response
    {
        $validated = $request->validate([
            'dateFrom' => ['nullable', 'date'],
            'dateTo' => ['nullable', 'date'],
        ]);

        // Pastikan direktori font cache dompdf ada & writable (penyebab umum 500 di produksi).
        $this->ensureFontCacheDirectory();

        $report = $service->summary(
            $validated['dateFrom'] ?? null,
            $validated['dateTo'] ?? null,
        );

        $pdf = Pdf::loadView('pdf.report', [
            'report' => $report,
            'generatedAt' => Carbon::now(),
            'logo' => $this->logoDataUri(),
        ])->setPaper('a4', 'portrait');

        $filename = 'laporan-pengaduan-'.Carbon::now()->format('Ymd-His').'.pdf';

        return $pdf->download($filename);
    }

    /**
     * Buat direktori font cache dompdf bila belum ada.
     */
    private function ensureFontCacheDirectory(): void
    {
        foreach (array_unique([config('dompdf.options.font_dir'), config('dompdf.options.font_cache')]) as $dir) {
            if ($dir && ! File::isDirectory($dir)) {
                File::makeDirectory($dir, 0775, true, true);
            }
        }
    }

    /**
     * Logo SiPeNa sebagai data URI base64 (dompdf tidak andal merender SVG inline).
     */
    private function logoDataUri(): ?string
    {
        $path = public_path('images/sipena-mark.png');

        if (! is_file($path)) {
            return null;
        }

        return 'data:image/png;base64,'.base64_encode((string) file_get_contents($path));
    }
}
