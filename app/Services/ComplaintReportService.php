<?php

namespace App\Services;

use App\Enums\ComplaintStatus;
use App\Models\Category;
use App\Models\Complaint;
use App\Models\Department;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

/**
 * Agregasi data untuk laporan rekapitulasi pimpinan.
 */
class ComplaintReportService
{
    /**
     * Rangkum data pengaduan untuk periode tertentu.
     *
     * @return array{
     *     total:int,
     *     byStatus:array<string,int>,
     *     byCategory:array<string,int>,
     *     byDepartment:array<string,int>,
     *     monthlyTrend:array<string,int>,
     *     dateFrom:?string,
     *     dateTo:?string
     * }
     */
    public function summary(?string $dateFrom = null, ?string $dateTo = null): array
    {
        $base = Complaint::query();

        if ($dateFrom) {
            $base->whereDate('created_at', '>=', $dateFrom);
        }

        if ($dateTo) {
            $base->whereDate('created_at', '<=', $dateTo);
        }

        return [
            'total' => (clone $base)->count(),
            'byStatus' => $this->byStatus(clone $base),
            'byCategory' => $this->byCategory(clone $base),
            'byDepartment' => $this->byDepartment(clone $base),
            'monthlyTrend' => $this->monthlyTrend(clone $base),
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
        ];
    }

    /**
     * @param  Builder<Complaint>  $query
     * @return array<string, int>
     */
    private function byStatus($query): array
    {
        $counts = $query->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $result = [];
        foreach (ComplaintStatus::cases() as $status) {
            $result[$status->label()] = (int) ($counts[$status->value] ?? 0);
        }

        return $result;
    }

    /**
     * @param  Builder<Complaint>  $query
     * @return array<string, int>
     */
    private function byCategory($query): array
    {
        $counts = $query->selectRaw('category_id, count(*) as total')
            ->groupBy('category_id')
            ->pluck('total', 'category_id');

        $result = [];
        foreach (Category::orderBy('name')->get() as $category) {
            $result[$category->name] = (int) ($counts[$category->id] ?? 0);
        }

        return $result;
    }

    /**
     * @param  Builder<Complaint>  $query
     * @return array<string, int>
     */
    private function byDepartment($query): array
    {
        $counts = $query->whereNotNull('department_id')
            ->selectRaw('department_id, count(*) as total')
            ->groupBy('department_id')
            ->pluck('total', 'department_id');

        $result = [];
        foreach (Department::orderBy('name')->get() as $department) {
            $result[$department->name] = (int) ($counts[$department->id] ?? 0);
        }

        return $result;
    }

    /**
     * Tren jumlah pengaduan per bulan (12 bulan terakhir sampai dateTo).
     *
     * @param  Builder<Complaint>  $query
     * @return array<string, int>
     */
    private function monthlyTrend($query): array
    {
        // Agregasi per bulan secara database-agnostik (hindari fungsi SQL khusus vendor).
        $rows = $query->get(['created_at'])
            ->groupBy(fn ($complaint) => $complaint->created_at->format('Y-m'))
            ->map(fn ($group) => $group->count());

        // Susun 12 bulan terakhir agar grafik konsisten.
        $end = Carbon::now()->startOfMonth();
        $result = [];
        for ($i = 11; $i >= 0; $i--) {
            $month = $end->copy()->subMonths($i);
            $key = $month->format('Y-m');
            $result[$month->translatedFormat('M Y')] = (int) ($rows[$key] ?? 0);
        }

        return $result;
    }
}
