<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Services\ComplaintReportService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
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

        $report = $service->summary(
            $validated['dateFrom'] ?? null,
            $validated['dateTo'] ?? null,
        );

        $pdf = Pdf::loadView('pdf.report', [
            'report' => $report,
            'generatedAt' => Carbon::now(),
        ])->setPaper('a4', 'portrait');

        $filename = 'laporan-pengaduan-'.Carbon::now()->format('Ymd-His').'.pdf';

        return $pdf->download($filename);
    }
}
