<x-layout.app :pageTitle="$pageTitle ?? null">
    @php($revenue = \App\Support\CrmData::revenueSeries())
    @php($ranking = \App\Support\CrmData::salesRanking())
    @php($stages = \App\Support\CrmData::stages())

    @push('head')
        <script src="https://cdn.jsdelivr.net/npm/apexcharts@3.49.1/dist/apexcharts.min.js"></script>
    @endpush

    <div x-data="reportsPage()">
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-heading" x-text="$t('reports.title')"></h1>
            <p class="mt-1 text-sm text-muted" x-text="$t('reports.subtitle')"></p>
        </div>

        <div class="mb-5 flex flex-wrap gap-3">
            <select x-model="period" @change="renderAll()" class="h-10 rounded-lg border border-themed surface-card px-3 text-sm text-body focus-ring">
                <option value="3m" x-text="$t('reports.periods.3m')"></option>
                <option value="6m" x-text="$t('reports.periods.6m')"></option>
            </select>
            <select x-model="owner" @change="renderAll()" class="h-10 rounded-lg border border-themed surface-card px-3 text-sm text-body focus-ring">
                <option value="" x-text="$t('reports.allReps')"></option>
                <template x-for="o in owners" :key="o"><option :value="o" x-text="o"></option></template>
            </select>
        </div>

        <div class="grid gap-5 xl:grid-cols-2">
            <div class="surface-card rounded-2xl border p-5">
                <h3 class="text-base font-semibold text-heading" x-text="$t('reports.conversionTitle')"></h3>
                <div class="mt-4" x-ref="conversion"></div>
            </div>
            <div class="surface-card rounded-2xl border p-5">
                <h3 class="text-base font-semibold text-heading" x-text="$t('reports.revenueTitle')"></h3>
                <div class="mt-4" x-ref="revenue"></div>
            </div>
            <div class="surface-card rounded-2xl border p-5 xl:col-span-2">
                <h3 class="text-base font-semibold text-heading" x-text="$t('reports.repTitle')"></h3>
                <div class="mt-4" x-ref="rep"></div>
                <div class="mt-4 overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead>
                            <tr class="border-b border-themed text-muted">
                                <th class="pb-2 pr-4 font-medium" x-text="$t('reports.repColumns.name')"></th>
                                <th class="pb-2 pr-4 font-medium" x-text="$t('reports.repColumns.deals')"></th>
                                <th class="pb-2 text-right font-medium" x-text="$t('reports.repColumns.revenue')"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($ranking as $r)
                                <tr class="border-b border-themed last:border-0">
                                    <td class="py-3 pr-4 font-medium text-heading">{{ $r['name'] }}</td>
                                    <td class="py-3 pr-4 text-muted">{{ $r['deals'] }}</td>
                                    <td class="py-3 text-right font-semibold text-heading" x-text="$currency({{ $r['revenue'] }})"></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            const REP_REVENUE = @json($revenue);
            const REP_RANKING = @json($ranking);
            const REP_STAGES = @json($stages);

            function reportsPage() {
                return {
                    period: '6m', owner: '',
                    owners: window.crmHelpers.owners(),
                    charts: {},
                    base() {
                        const dark = Alpine.store('ui').darkMode;
                        const fg = dark ? '#98a2b3' : '#667085';
                        const grid = dark ? '#1f2937' : '#e4e7ec';
                        return {
                            chart: { fontFamily: 'Outfit, sans-serif', foreColor: fg, toolbar: { show: false }, zoom: { enabled: false } },
                            grid: { borderColor: grid, strokeDashArray: 4 },
                            tooltip: { theme: dark ? 'dark' : 'light' },
                            legend: { show: false },
                        };
                    },
                    init() { this.$nextTick(() => this.renderAll()); },
                    renderAll() {
                        Object.values(this.charts).forEach(c => c && c.destroy());
                        this.charts = {};
                        const months = this.period === '3m' ? 3 : 6;
                        const deals = this.owner ? $store.crm.deals.filter(d => d.owner === this.owner) : $store.crm.deals;
                        const funnel = REP_STAGES.filter(s => s.id !== 'lost').map(s => ({
                            stage: s, count: deals.filter(d => d.stage === s.id).length,
                        }));

                        this.charts.conversion = new ApexCharts(this.$refs.conversion, {
                            ...this.base(),
                            chart: { ...this.base().chart, type: 'bar', height: 300 },
                            plotOptions: { bar: { horizontal: true, borderRadius: 4, distributed: true, barHeight: '55%' } },
                            colors: funnel.map(f => f.stage.color),
                            dataLabels: { enabled: true },
                            xaxis: { categories: funnel.map(f => $t('stages.' + f.stage.nameKey)) },
                            series: [{ name: $t('appNav.deals'), data: funnel.map(f => f.count) }],
                        });
                        this.charts.conversion.render();

                        this.charts.revenue = new ApexCharts(this.$refs.revenue, {
                            ...this.base(),
                            chart: { ...this.base().chart, type: 'bar', height: 300 },
                            plotOptions: { bar: { columnWidth: '45%', borderRadius: 4 } },
                            colors: ['#465fff'],
                            dataLabels: { enabled: false },
                            xaxis: { categories: REP_REVENUE.categories.slice(-months) },
                            yaxis: { labels: { formatter: (v) => window.crmHelpers.formatCompact(v) } },
                            series: [{ name: $t('reports.revenueTitle'), data: REP_REVENUE.closed.slice(-months) }],
                        });
                        this.charts.revenue.render();

                        this.charts.rep = new ApexCharts(this.$refs.rep, {
                            ...this.base(),
                            chart: { ...this.base().chart, type: 'bar', height: 300 },
                            plotOptions: { bar: { columnWidth: '45%', borderRadius: 4, distributed: true } },
                            colors: ['#465fff', '#12b76a', '#f79009', '#0ba5ec'],
                            dataLabels: { enabled: false },
                            xaxis: { categories: REP_RANKING.map(r => r.name.split(' ')[0]) },
                            yaxis: { labels: { formatter: (v) => window.crmHelpers.formatCompact(v) } },
                            series: [{ name: $t('reports.repTitle'), data: REP_RANKING.map(r => r.revenue) }],
                        });
                        this.charts.rep.render();
                    },
                };
            }
        </script>
    @endpush
</x-layout.app>
