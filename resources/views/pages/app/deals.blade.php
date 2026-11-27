<x-layout.app :pageTitle="$pageTitle ?? null">
    <div x-data="dealsPage()">
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-heading" x-text="$t('deals.title')"></h1>
            <p class="mt-1 text-sm text-muted" x-text="$t('deals.subtitle')"></p>
        </div>

        {{-- Filters --}}
        <div class="mb-4 flex flex-wrap gap-3">
            <select x-model="stageFilter" class="h-10 rounded-lg border border-themed surface-card px-3 text-sm text-body focus-ring">
                <option value="" x-text="$t('deals.filters.allStages')"></option>
                <template x-for="s in stages" :key="s.id"><option :value="s.id" x-text="$t('stages.' + s.nameKey)"></option></template>
            </select>
            <select x-model="ownerFilter" class="h-10 rounded-lg border border-themed surface-card px-3 text-sm text-body focus-ring">
                <option value="" x-text="$t('deals.filters.allOwners')"></option>
                <template x-for="o in owners" :key="o"><option :value="o" x-text="o"></option></template>
            </select>
        </div>

        <div class="surface-card overflow-hidden rounded-2xl border">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead>
                        <tr class="border-b border-themed text-muted">
                            <th class="px-5 py-3 font-medium" x-text="$t('deals.columns.title')"></th>
                            <th class="px-5 py-3 font-medium" x-text="$t('deals.columns.company')"></th>
                            <th class="px-5 py-3 font-medium" x-text="$t('deals.columns.stage')"></th>
                            <th class="px-5 py-3 font-medium" x-text="$t('deals.columns.owner')"></th>
                            <th class="px-5 py-3 font-medium" x-text="$t('deals.columns.value')"></th>
                            <th class="px-5 py-3 font-medium" x-text="$t('deals.columns.probability')"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="d in paged" :key="d.id">
                            <tr class="border-b border-themed last:border-0 hover:bg-gray-50 dark:hover:bg-gray-800/50">
                                <td class="px-5 py-3">
                                    <button type="button" @click="selected = d" class="text-left font-medium text-heading hover:text-brand-600" x-text="d.title"></button>
                                </td>
                                <td class="px-5 py-3 text-muted" x-text="$store.crm.companyName(d.companyId)"></td>
                                <td class="px-5 py-3"><x-crm.stage-pill stage="d.stage" size="sm" /></td>
                                <td class="px-5 py-3 text-muted" x-text="d.owner"></td>
                                <td class="px-5 py-3 font-semibold text-heading" x-text="$currency(d.value)"></td>
                                <td class="px-5 py-3 text-muted" x-text="d.probability + '%'"></td>
                            </tr>
                        </template>
                        <template x-if="filtered.length === 0">
                            <tr><td colspan="6" class="px-5 py-10 text-center text-sm text-muted" x-text="$t('deals.empty')"></td></tr>
                        </template>
                    </tbody>
                </table>
            </div>
            <div class="flex items-center justify-between border-t border-themed px-5 py-3 text-sm" x-show="totalPages > 1">
                <button type="button" @click="page = Math.max(1, page - 1)" :disabled="page === 1" class="rounded-lg border border-themed px-3 py-1.5 text-body disabled:opacity-50" x-text="$t('common.previous')"></button>
                <span class="text-muted" x-text="page + ' / ' + totalPages"></span>
                <button type="button" @click="page = Math.min(totalPages, page + 1)" :disabled="page === totalPages" class="rounded-lg border border-themed px-3 py-1.5 text-body disabled:opacity-50" x-text="$t('common.next')"></button>
            </div>
        </div>

        {{-- Detail modal --}}
        <div x-show="selected" x-cloak class="fixed inset-0 z-[80] flex items-center justify-center p-4" @keydown.escape.window="selected = null">
            <div class="absolute inset-0 bg-gray-900/50" @click="selected = null"></div>
            <div class="surface-card relative w-full max-w-lg rounded-2xl border p-6 shadow-theme-xl" x-transition x-show="selected">
                <template x-if="selected">
                    <div>
                        <div class="mb-5 flex items-center justify-between">
                            <h3 class="text-lg font-semibold text-heading" x-text="selected.title"></h3>
                            <button type="button" @click="selected = null" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200"><x-icon name="x" :size="20" /></button>
                        </div>
                        <div class="space-y-5">
                            <div class="grid grid-cols-2 gap-4 text-sm">
                                <template x-for="f in detailFields" :key="f.label">
                                    <div class="rounded-lg border border-themed p-3">
                                        <p class="text-xs text-muted" x-text="f.label"></p>
                                        <p class="mt-0.5 font-medium text-heading" x-text="f.value"></p>
                                    </div>
                                </template>
                            </div>
                            <div>
                                <p class="mb-2 text-sm font-medium text-heading" x-text="$t('deals.moveStage')"></p>
                                <div class="flex flex-wrap gap-2">
                                    <template x-for="s in stages" :key="s.id">
                                        <button type="button" @click="moveStage(s.id)"
                                            class="rounded-lg border px-3 py-1.5 text-xs font-medium transition"
                                            :class="selected.stage === s.id ? 'border-brand-400 bg-brand-50 text-brand-600 dark:bg-brand-500/15' : 'border-themed text-muted hover:border-brand-300'"
                                            x-text="$t('stages.' + s.nameKey)"></button>
                                    </template>
                                </div>
                            </div>
                            <div class="flex items-center gap-2 rounded-lg bg-gray-50 p-3 text-xs text-muted dark:bg-gray-900/40">
                                <x-icon name="info" :size="14" /> <span x-text="$t('deals.detailHint')"></span>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            function dealsPage() {
                return {
                    stageFilter: '', ownerFilter: '',
                    page: 1, pageSize: 10,
                    stages: window.crmHelpers.stages(),
                    owners: window.crmHelpers.owners(),
                    selected: null,
                    get filtered() {
                        return $store.crm.deals.filter(d =>
                            (!this.stageFilter || d.stage === this.stageFilter) &&
                            (!this.ownerFilter || d.owner === this.ownerFilter)
                        );
                    },
                    get totalPages() { return Math.max(1, Math.ceil(this.filtered.length / this.pageSize)); },
                    get paged() {
                        const p = Math.min(this.page, this.totalPages);
                        return this.filtered.slice((p - 1) * this.pageSize, p * this.pageSize);
                    },
                    get detailFields() {
                        const d = this.selected; if (!d) return [];
                        return [
                            { label: $t('deals.columns.value'), value: $currency(d.value) },
                            { label: $t('deals.columns.probability'), value: d.probability + '%' },
                            { label: $t('deals.columns.contact'), value: $store.crm.contactName(d.contactId) },
                            { label: $t('deals.columns.company'), value: $store.crm.companyName(d.companyId) },
                            { label: $t('deals.columns.owner'), value: d.owner },
                            { label: $t('deals.expectedClose'), value: $date(d.expectedClose) },
                        ];
                    },
                    moveStage(stageId) {
                        $store.crm.moveDealStage(this.selected.id, stageId);
                        this.selected = $store.crm.deals.find(d => d.id === this.selected.id);
                        const s = window.crmHelpers.getStage(stageId);
                        $store.ui.notifySuccess($t('deals.stageChanged', { stage: $t('stages.' + s.nameKey) }));
                    },
                };
            }
        </script>
    @endpush
</x-layout.app>
