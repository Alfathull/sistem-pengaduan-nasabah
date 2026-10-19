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
    <div class="mt-6 rounded-2xl border border-line bg-white p-4 shadow-card dark:border-zinc-700 dark:bg-zinc-900">
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
        <x-stat-card :label="__('Total Pengaduan')" :value="$report['total']" />
        @foreach (array_slice($report['byStatus'], 0, 3, true) as $label => $count)
            <x-stat-card :label="$label" :value="$count" wire:key="card-{{ $label }}" />
        @endforeach
    </div>

    <!-- Grafik -->
    <div
        wire:key="charts-{{ md5(json_encode($report)) }}"
        x-data="reportCharts(@js($report))"
        x-init="render()"
        class="mt-6 grid gap-6 lg:grid-cols-2"
    >
        <div class="rounded-2xl border border-line bg-white p-5 shadow-card dark:border-zinc-700 dark:bg-zinc-900">
            <flux:heading size="sm">{{ __('Tren Pengaduan per Bulan') }}</flux:heading>
            <div class="mt-4"><canvas x-ref="trend" height="140"></canvas></div>
        </div>
        <div class="rounded-2xl border border-line bg-white p-5 shadow-card dark:border-zinc-700 dark:bg-zinc-900">
            <flux:heading size="sm">{{ __('Pengaduan per Status') }}</flux:heading>
            <div class="mt-4"><canvas x-ref="status" height="140"></canvas></div>
        </div>
        <div class="rounded-2xl border border-line bg-white p-5 shadow-card dark:border-zinc-700 dark:bg-zinc-900">
            <flux:heading size="sm">{{ __('Pengaduan per Kategori') }}</flux:heading>
            <div class="mt-4"><canvas x-ref="category" height="140"></canvas></div>
        </div>
        <div class="rounded-2xl border border-line bg-white p-5 shadow-card dark:border-zinc-700 dark:bg-zinc-900">
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
            palette: ['#0b1f3a', '#12346b', '#c8962e', '#2a528e', '#1f8a5b', '#c2410c', '#2563eb', '#5b6577', '#b7791f'],
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
                            borderColor: '#0b1f3a',
                            backgroundColor: 'rgba(11,31,58,0.12)',
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
                        datasets: [{ label: 'Jumlah', data: Object.values(report.byCategory), backgroundColor: '#12346b' }],
                    },
                    options: { responsive: true, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true, ticks: { precision: 0 } } } },
                }));

                this.charts.push(new Chart(this.$refs.department, {
                    type: 'bar',
                    data: {
                        labels: Object.keys(report.byDepartment),
                        datasets: [{ label: 'Jumlah', data: Object.values(report.byDepartment), backgroundColor: '#c8962e' }],
                    },
                    options: { indexAxis: 'y', responsive: true, plugins: { legend: { display: false } }, scales: { x: { beginAtZero: true, ticks: { precision: 0 } } } },
                }));
            },
        }));
    </script>
    @endscript
</div>
