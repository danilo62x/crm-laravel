<x-layout.app :pageTitle="$pageTitle ?? null">
    @php($revenue = \App\Support\CrmData::revenueSeries())
    @php($activitySeries = \App\Support\CrmData::activitySeries())
    @php($ranking = \App\Support\CrmData::salesRanking())
    @php($recentDeals = \App\Support\CrmData::recentDeals())
    @php($stages = \App\Support\CrmData::stages())

    @push('head')
        <script src="https://cdn.jsdelivr.net/npm/apexcharts@3.49.1/dist/apexcharts.min.js"></script>
    @endpush

    <div x-data="{
        get metrics() {
            const deals = $store.crm.deals;
            const open = deals.filter(d => ['lead','qualified','proposal','negotiation'].includes(d.stage));
            const won = deals.filter(d => d.stage === 'won');
            const lost = deals.filter(d => d.stage === 'lost');
            const closed = won.length + lost.length;
            return {
                pipelineValue: open.reduce((s, d) => s + d.value, 0),
                openDeals: open.length,
                conversionRate: closed === 0 ? 0 : Math.round((won.length / closed) * 100),
                pendingTasks: $store.crm.activities.filter(a => a.status === 'open').length,
            };
        }
    }">
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-heading" x-text="$t('dashboard.title')"></h1>
            <p class="mt-1 text-sm text-muted" x-text="$t('dashboard.welcome', { name: $store.auth.user?.name || '' })"></p>
        </div>

        {{-- KPIs --}}
        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 xl:grid-cols-4">
            <x-ui.stats-card titleKey="dashboard.kpis.pipeline" value="$currency(metrics.pipelineValue)" icon="dollar" accent="brand" trend="up" trendValue="12%" />
            <x-ui.stats-card titleKey="dashboard.kpis.openDeals" value="metrics.openDeals" icon="layers" accent="info" trend="up" trendValue="5%" />
            <x-ui.stats-card titleKey="dashboard.kpis.conversion" value="metrics.conversionRate + '%'" icon="trending-up" accent="success" trend="up" trendValue="3%" />
            <x-ui.stats-card titleKey="dashboard.kpis.tasks" value="metrics.pendingTasks" icon="check-circle" accent="warning" />
        </div>

        {{-- Charts row 1: revenue + funnel --}}
        <div class="mt-5 grid grid-cols-1 gap-5 xl:grid-cols-3">
            <div class="surface-card rounded-2xl border p-5 xl:col-span-2">
                <h3 class="text-base font-semibold text-heading" x-text="$t('dashboard.revenueTitle')"></h3>
                <div class="mt-4" x-data="revenueChart()" x-init="render()" x-ref="el"></div>
            </div>
            <div class="surface-card rounded-2xl border p-5">
                <h3 class="text-base font-semibold text-heading" x-text="$t('dashboard.funnelTitle')"></h3>
                <div class="mt-4" x-data="funnelChart()" x-init="render()" x-ref="el"></div>
            </div>
        </div>

        {{-- Charts row 2: activities + ranking --}}
        <div class="mt-5 grid grid-cols-1 gap-5 xl:grid-cols-3">
            <div class="surface-card rounded-2xl border p-5 xl:col-span-2">
                <h3 class="text-base font-semibold text-heading" x-text="$t('dashboard.activityTitle')"></h3>
                <div class="mt-4" x-data="activityChart()" x-init="render()" x-ref="el"></div>
            </div>
            <div class="surface-card rounded-2xl border p-5">
                <h3 class="text-base font-semibold text-heading" x-text="$t('dashboard.rankingTitle')"></h3>
                <ul class="mt-4 space-y-4">
                    @foreach ($ranking as $i => $rep)
                        <li class="flex items-center gap-3">
                            <span class="text-sm font-bold text-muted">{{ $i + 1 }}</span>
                            <x-ui.avatar name="{{ $rep['name'] }}" size="sm" />
                            <div class="flex-1">
                                <p class="text-sm font-medium text-heading">{{ $rep['name'] }}</p>
                                <p class="text-xs text-muted" x-text="$t('dashboard.dealsWon', { count: {{ $rep['deals'] }} })"></p>
                            </div>
                            <span class="text-sm font-semibold text-brand-600 dark:text-brand-400" x-text="$compact({{ $rep['revenue'] }})"></span>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>

        {{-- Recent deals + today's tasks --}}
        <div class="mt-5 grid grid-cols-1 gap-5 xl:grid-cols-3">
            <div class="surface-card rounded-2xl border p-5 xl:col-span-2">
                <div class="flex items-center justify-between">
                    <h3 class="text-base font-semibold text-heading" x-text="$t('dashboard.recentDeals')"></h3>
                    <a href="{{ route('app.deals') }}" class="text-sm font-medium text-brand-600 hover:text-brand-700" x-text="$t('common.viewAll')"></a>
                </div>
                <div class="mt-4 overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead>
                            <tr class="border-b border-themed text-muted">
                                <th class="pb-2 pr-4 font-medium" x-text="$t('deals.columns.title')"></th>
                                <th class="pb-2 pr-4 font-medium" x-text="$t('deals.columns.stage')"></th>
                                <th class="pb-2 pr-4 font-medium" x-text="$t('deals.columns.contact')"></th>
                                <th class="pb-2 text-right font-medium" x-text="$t('deals.columns.value')"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($recentDeals as $d)
                                <tr class="border-b border-themed last:border-0">
                                    <td class="py-3 pr-4 font-medium text-heading">{{ $d['title'] }}</td>
                                    <td class="py-3 pr-4"><x-crm.stage-pill stage="'{{ $d['stage'] }}'" size="sm" /></td>
                                    <td class="py-3 pr-4 text-muted">{{ \App\Support\CrmData::contactName($d['contactId']) }}</td>
                                    <td class="py-3 text-right font-semibold text-heading" x-text="$currency({{ $d['value'] }})"></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="surface-card rounded-2xl border p-5"
                x-data="{ get tasks() {
                    const now = new Date().toDateString();
                    return $store.crm.activities.filter(a => a.status === 'open' && new Date(a.date).toDateString() === now).slice(0, 5);
                } }">
                <div class="flex items-center justify-between">
                    <h3 class="text-base font-semibold text-heading" x-text="$t('dashboard.todayTasks')"></h3>
                    <a href="{{ route('app.activities') }}" class="text-sm font-medium text-brand-600 hover:text-brand-700" x-text="$t('common.viewAll')"></a>
                </div>
                <template x-if="tasks.length === 0">
                    <p class="mt-6 text-center text-sm text-muted" x-text="$t('dashboard.noTasks')"></p>
                </template>
                <ul class="mt-4 divide-y divide-themed" x-show="tasks.length > 0">
                    <template x-for="a in tasks" :key="a.id">
                        <li class="flex items-center gap-3 py-3">
                            <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-brand-50 text-brand-500 dark:bg-brand-500/15">
                                <template x-if="a.type === 'call'"><x-icon name="phone" :size="15" /></template>
                                <template x-if="a.type === 'email'"><x-icon name="mail" :size="15" /></template>
                                <template x-if="a.type === 'meeting'"><x-icon name="users" :size="15" /></template>
                                <template x-if="a.type === 'task'"><x-icon name="check-circle" :size="15" /></template>
                            </span>
                            <p class="flex-1 text-sm text-body" x-text="a.title"></p>
                            <span class="text-xs text-muted" x-text="$fromNow(a.date)"></span>
                        </li>
                    </template>
                </ul>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            const CRM_REVENUE = @json($revenue);
            const CRM_ACTIVITY = @json($activitySeries);
            const CRM_STAGES = @json($stages);

            function chartBase() {
                const dark = Alpine.store('ui').darkMode;
                const fg = dark ? '#98a2b3' : '#667085';
                const grid = dark ? '#1f2937' : '#e4e7ec';
                return {
                    chart: { fontFamily: 'Outfit, sans-serif', foreColor: fg, toolbar: { show: false }, zoom: { enabled: false } },
                    grid: { borderColor: grid, strokeDashArray: 4 },
                    tooltip: { theme: dark ? 'dark' : 'light' },
                    legend: { labels: { colors: fg } },
                    dataLabels: { enabled: false },
                    stroke: { curve: 'smooth', width: 2 },
                };
            }

            function revenueChart() {
                return {
                    chart: null,
                    render() {
                        const base = chartBase();
                        this.chart = new ApexCharts(this.$refs.el, {
                            ...base,
                            chart: { ...base.chart, type: 'area', height: 300, stacked: false },
                            colors: ['#12b76a', '#465fff'],
                            fill: { type: 'gradient', gradient: { opacityFrom: 0.4, opacityTo: 0.05 } },
                            legend: { ...base.legend, show: true, position: 'top' },
                            xaxis: { categories: CRM_REVENUE.categories },
                            yaxis: { labels: { formatter: (v) => window.crmHelpers.formatCompact(v) } },
                            series: [
                                { name: $t('dashboard.revenueLegend.closed'), data: CRM_REVENUE.closed },
                                { name: $t('dashboard.revenueLegend.forecast'), data: CRM_REVENUE.forecast },
                            ],
                        });
                        this.chart.render();
                    },
                };
            }

            function funnelChart() {
                return {
                    chart: null,
                    render() {
                        const base = chartBase();
                        const deals = Alpine.store('crm').deals;
                        const funnel = CRM_STAGES.filter(s => s.id !== 'lost').map(s => ({
                            stage: s,
                            count: deals.filter(d => d.stage === s.id).length,
                        }));
                        this.chart = new ApexCharts(this.$refs.el, {
                            ...base,
                            chart: { ...base.chart, type: 'bar', height: 300 },
                            plotOptions: { bar: { horizontal: true, borderRadius: 4, distributed: true, barHeight: '60%' } },
                            colors: funnel.map(f => f.stage.color),
                            legend: { show: false },
                            dataLabels: { enabled: true },
                            xaxis: { categories: funnel.map(f => $t('stages.' + f.stage.nameKey)) },
                            series: [{ name: $t('appNav.deals'), data: funnel.map(f => f.count) }],
                        });
                        this.chart.render();
                    },
                };
            }

            function activityChart() {
                return {
                    chart: null,
                    render() {
                        const base = chartBase();
                        this.chart = new ApexCharts(this.$refs.el, {
                            ...base,
                            chart: { ...base.chart, type: 'bar', height: 280, stacked: true },
                            colors: ['#465fff', '#0ba5ec', '#f79009'],
                            plotOptions: { bar: { columnWidth: '45%', borderRadius: 3 } },
                            legend: { ...base.legend, show: true, position: 'top' },
                            xaxis: { categories: CRM_ACTIVITY.categories },
                            series: [
                                { name: $t('activities.types.call'), data: CRM_ACTIVITY.calls },
                                { name: $t('activities.types.email'), data: CRM_ACTIVITY.emails },
                                { name: $t('activities.types.meeting'), data: CRM_ACTIVITY.meetings },
                            ],
                        });
                        this.chart.render();
                    },
                };
            }
        </script>
    @endpush
</x-layout.app>
