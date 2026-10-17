<?php

use App\Services\ComplaintReportService;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;

new #[Layout('layouts::manager')] #[Title('Laporan Rekapitulasi')] class extends Component {
    #[Url]
    public string $dateFrom = '';

    #[Url]
    public string $dateTo = '';

    public function resetFilters(): void
    {
        $this->reset(['dateFrom', 'dateTo']);
    }

    public function with(ComplaintReportService $service): array
    {
        $report = $service->summary($this->dateFrom ?: null, $this->dateTo ?: null);

        return [
            'report' => $report,
        ];
    }
}; ?>

<div>
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <flux:heading size="xl">{{ __('Laporan Rekapitulasi Pengaduan') }}</flux:heading>
            <flux:text class="mt-1">{{ __('Ringkasan statistik pengaduan nasabah.') }}</flux:text>
        </div>
        <flux:button
            variant="primary"
            icon="arrow-down-tray"
            href="{{ route('manager.report.pdf', ['dateFrom' => $dateFrom, 'dateTo' => $dateTo]) }}"
            target="_blank"
        >
            {{ __('Export PDF') }}
        </flux:button>
    </div>

    <!-- Filter periode -->
    <div class="mt-6 rounded-xl border border-zinc-200 p-4 dark:border-zinc-700">
        <div class="grid gap-4 sm:grid-cols-3">
            <flux:input type="date" wire:model.live="dateFrom" :label="__('Dari Tanggal')" />
            <flux:input type="date" wire:model.live="dateTo" :label="__('Sampai Tanggal')" />
            <div class="flex items-end">
                <flux:button variant="ghost" icon="x-mark" wire:click="resetFilters">{{ __('Reset Periode') }}</flux:button>
            </div>
        </div>
    </div>

    <!-- Kartu ringkasan -->
    <div class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <div class="rounded-xl border border-zinc-200 p-5 dark:border-zinc-700">
            <flux:text class="text-sm text-zinc-500">{{ __('Total Pengaduan') }}</flux:text>
            <flux:heading size="xl" class="mt-1">{{ $report['total'] }}</flux:heading>
        </div>
        @foreach (array_slice($report['byStatus'], 0, 3, true) as $label => $count)
            <div class="rounded-xl border border-zinc-200 p-5 dark:border-zinc-700" wire:key="card-{{ $label }}">
                <flux:text class="text-sm text-zinc-500">{{ $label }}</flux:text>
                <flux:heading size="xl" class="mt-1">{{ $count }}</flux:heading>
            </div>
        @endforeach
    </div>

    <!-- Grafik -->
    <div
        wire:key="charts-{{ md5(json_encode($report)) }}"
        x-data="reportCharts(@js($report))"
        x-init="render()"
        class="mt-6 grid gap-6 lg:grid-cols-2"
    >
        <div class="rounded-xl border border-zinc-200 p-5 dark:border-zinc-700">
            <flux:heading size="sm">{{ __('Tren Pengaduan per Bulan') }}</flux:heading>
            <div class="mt-4"><canvas x-ref="trend" height="140"></canvas></div>
        </div>
        <div class="rounded-xl border border-zinc-200 p-5 dark:border-zinc-700">
            <flux:heading size="sm">{{ __('Pengaduan per Status') }}</flux:heading>
            <div class="mt-4"><canvas x-ref="status" height="140"></canvas></div>
        </div>
        <div class="rounded-xl border border-zinc-200 p-5 dark:border-zinc-700">
            <flux:heading size="sm">{{ __('Pengaduan per Kategori') }}</flux:heading>
            <div class="mt-4"><canvas x-ref="category" height="140"></canvas></div>
        </div>
        <div class="rounded-xl border border-zinc-200 p-5 dark:border-zinc-700">
            <flux:heading size="sm">{{ __('Pengaduan per Departemen') }}</flux:heading>
            <div class="mt-4"><canvas x-ref="department" height="140"></canvas></div>
        </div>
    </div>

    @assets
        <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
    @endassets

    @script
    <script>
        Alpine.data('reportCharts', (report) => ({
            charts: [],
            palette: ['#1e3a8a', '#2563eb', '#f59e0b', '#9333ea', '#16a34a', '#dc2626', '#0ea5e9', '#64748b', '#db2777'],
            render() {
                this.charts.forEach(c => c.destroy());
                this.charts = [];

                this.charts.push(new Chart(this.$refs.trend, {
                    type: 'line',
                    data: {
                        labels: Object.keys(report.monthlyTrend),
                        datasets: [{
                            label: 'Jumlah',
                            data: Object.values(report.monthlyTrend),
                            borderColor: '#1e3a8a',
                            backgroundColor: 'rgba(30,58,138,0.15)',
                            tension: 0.3,
                            fill: true,
                        }],
                    },
                    options: { responsive: true, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true, ticks: { precision: 0 } } } },
                }));

                this.charts.push(new Chart(this.$refs.status, {
                    type: 'doughnut',
                    data: {
                        labels: Object.keys(report.byStatus),
                        datasets: [{ data: Object.values(report.byStatus), backgroundColor: this.palette }],
                    },
                    options: { responsive: true, plugins: { legend: { position: 'bottom' } } },
                }));

                this.charts.push(new Chart(this.$refs.category, {
                    type: 'bar',
                    data: {
                        labels: Object.keys(report.byCategory),
                        datasets: [{ label: 'Jumlah', data: Object.values(report.byCategory), backgroundColor: '#2563eb' }],
                    },
                    options: { responsive: true, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true, ticks: { precision: 0 } } } },
                }));

                this.charts.push(new Chart(this.$refs.department, {
                    type: 'bar',
                    data: {
                        labels: Object.keys(report.byDepartment),
                        datasets: [{ label: 'Jumlah', data: Object.values(report.byDepartment), backgroundColor: '#9333ea' }],
                    },
                    options: { indexAxis: 'y', responsive: true, plugins: { legend: { display: false } }, scales: { x: { beginAtZero: true, ticks: { precision: 0 } } } },
                }));
            },
        }));
    </script>
    @endscript
</div>
