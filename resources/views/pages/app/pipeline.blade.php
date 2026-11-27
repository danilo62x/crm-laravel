<x-layout.app :pageTitle="$pageTitle ?? null">
    <div x-data="pipelineBoard()">
        {{-- Page header --}}
        <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-heading" x-text="$t('pipeline.title')"></h1>
                <p class="mt-1 text-sm text-muted" x-text="$t('pipeline.subtitle')"></p>
            </div>
            <button type="button" @click="openModal()"
                class="flex h-10 shrink-0 items-center gap-2 rounded-lg bg-brand-500 px-4 text-sm font-medium text-white transition hover:bg-brand-600">
                <x-icon name="plus" :size="16" /> <span x-text="$t('deals.new')"></span>
            </button>
        </div>

        {{-- Kanban columns --}}
        <div class="flex gap-4 overflow-x-auto pb-4">
            <template x-for="stage in stages" :key="stage.id">
                <div
                    @dragover.prevent="$event.dataTransfer.dropEffect = 'move'; overStage = stage.id"
                    @dragleave="if (!$event.currentTarget.contains($event.relatedTarget)) overStage = null"
                    @drop.prevent="onDrop(stage.id)"
                    class="flex w-72 shrink-0 flex-col rounded-2xl border p-3 transition"
                    :class="overStage === stage.id ? 'border-brand-400 bg-brand-50/60 dark:bg-brand-500/10' : 'border-themed surface-card'">
                    <div class="mb-3 flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="h-2.5 w-2.5 rounded-full" :style="`background-color: ${stage.color}`"></span>
                            <span class="text-sm font-semibold text-heading" x-text="$t('stages.' + stage.nameKey)"></span>
                            <span class="rounded-full bg-gray-100 px-2 py-0.5 text-xs font-medium text-muted dark:bg-gray-800" x-text="dealsFor(stage.id).length"></span>
                        </div>
                    </div>
                    <p class="mb-3 text-xs font-medium text-muted" x-text="$currency(stageTotal(stage.id))"></p>

                    <div class="flex flex-1 flex-col gap-2.5 overflow-y-auto no-scrollbar" style="min-height: 80px">
                        <template x-for="deal in dealsFor(stage.id)" :key="deal.id">
                            <x-crm.deal-card />
                        </template>
                        <template x-if="dealsFor(stage.id).length === 0">
                            <div class="flex flex-1 items-center justify-center rounded-xl border border-dashed border-themed py-6 text-xs text-muted" x-text="$t('pipeline.dropHere')"></div>
                        </template>
                    </div>
                </div>
            </template>
        </div>

        {{-- New deal modal --}}
        <div x-show="modalOpen" x-cloak class="fixed inset-0 z-[80] flex items-center justify-center p-4" @keydown.escape.window="modalOpen = false">
            <div class="absolute inset-0 bg-gray-900/50" @click="modalOpen = false"></div>
            <div class="surface-card relative w-full max-w-lg rounded-2xl border p-6 shadow-theme-xl" x-transition>
                <div class="mb-5 flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-heading" x-text="$t('deals.newModalTitle')"></h3>
                    <button type="button" @click="modalOpen = false" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200"><x-icon name="x" :size="20" /></button>
                </div>
                <form class="space-y-4" @submit.prevent="submit()">
                    <div class="space-y-1.5">
                        <label class="block text-sm font-medium text-heading" x-text="$t('deals.columns.title')"></label>
                        <input x-model="form.title" :placeholder="$t('deals.titlePlaceholder')" class="focus-ring w-full rounded-lg border bg-transparent px-3.5 py-2.5 text-sm text-body" :class="errors.title ? 'border-error-500' : 'border-themed'" />
                        <p x-show="errors.title" x-cloak class="text-xs text-error-500" x-text="errors.title"></p>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-1.5">
                            <label class="block text-sm font-medium text-heading" x-text="$t('deals.columns.value')"></label>
                            <input type="number" min="0" x-model.number="form.value" class="focus-ring w-full rounded-lg border bg-transparent px-3.5 py-2.5 text-sm text-body" :class="errors.value ? 'border-error-500' : 'border-themed'" />
                            <p x-show="errors.value" x-cloak class="text-xs text-error-500" x-text="errors.value"></p>
                        </div>
                        <div class="space-y-1.5">
                            <label class="block text-sm font-medium text-heading" x-text="$t('deals.columns.stage')"></label>
                            <select x-model="form.stage" class="focus-ring w-full rounded-lg border border-themed bg-transparent px-3.5 py-2.5 text-sm text-body">
                                <template x-for="s in stages" :key="s.id"><option :value="s.id" x-text="$t('stages.' + s.nameKey)"></option></template>
                            </select>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-1.5">
                            <label class="block text-sm font-medium text-heading" x-text="$t('deals.columns.contact')"></label>
                            <select x-model="form.contactId" class="focus-ring w-full rounded-lg border border-themed bg-transparent px-3.5 py-2.5 text-sm text-body">
                                <option value="">—</option>
                                <template x-for="c in $store.crm.contacts" :key="c.id"><option :value="c.id" x-text="c.name"></option></template>
                            </select>
                        </div>
                        <div class="space-y-1.5">
                            <label class="block text-sm font-medium text-heading" x-text="$t('deals.columns.company')"></label>
                            <select x-model="form.companyId" class="focus-ring w-full rounded-lg border border-themed bg-transparent px-3.5 py-2.5 text-sm text-body">
                                <option value="">—</option>
                                <template x-for="c in $store.crm.companies" :key="c.id"><option :value="c.id" x-text="c.name"></option></template>
                            </select>
                        </div>
                    </div>
                    <div class="space-y-1.5">
                        <label class="block text-sm font-medium text-heading" x-text="$t('deals.columns.owner')"></label>
                        <select x-model="form.owner" class="focus-ring w-full rounded-lg border border-themed bg-transparent px-3.5 py-2.5 text-sm text-body">
                            <template x-for="o in owners" :key="o"><option :value="o" x-text="o"></option></template>
                        </select>
                    </div>
                    <div class="flex justify-end gap-2 pt-2">
                        <button type="button" @click="modalOpen = false" class="rounded-lg border border-themed px-4 py-2 text-sm font-medium text-body hover:bg-gray-50 dark:hover:bg-gray-800" x-text="$t('common.cancel')"></button>
                        <button type="submit" class="rounded-lg bg-brand-500 px-4 py-2 text-sm font-medium text-white hover:bg-brand-600" x-text="$t('deals.create')"></button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            function pipelineBoard() {
                return {
                    stages: window.crmHelpers.stages(),
                    owners: window.crmHelpers.owners(),
                    dragId: null,
                    overStage: null,
                    modalOpen: false,
                    form: {},
                    errors: {},

                    dealsFor(stageId) {
                        return $store.crm.deals.filter(d => d.stage === stageId);
                    },
                    stageTotal(stageId) {
                        return this.dealsFor(stageId).reduce((s, d) => s + d.value, 0);
                    },
                    onDragStart(e, id) {
                        this.dragId = id;
                        e.dataTransfer.effectAllowed = 'move';
                        e.dataTransfer.setData('text/plain', String(id));
                    },
                    onDragEnd() {
                        this.dragId = null;
                        this.overStage = null;
                    },
                    onDrop(stageId) {
                        if (this.dragId != null) {
                            const deal = $store.crm.deals.find(d => d.id === this.dragId);
                            if (deal && deal.stage !== stageId) {
                                $store.crm.moveDealStage(this.dragId, stageId);
                                const s = window.crmHelpers.getStage(stageId);
                                $store.ui.notifySuccess($t('pipeline.moved', { stage: $t('stages.' + s.nameKey) }));
                            }
                        }
                        this.dragId = null;
                        this.overStage = null;
                    },
                    openDeal() {
                        window.location.href = '{{ route('app.deals') }}';
                    },
                    openModal() {
                        this.form = { title: '', value: 0, stage: 'lead', contactId: '', companyId: '', owner: this.owners[0] };
                        this.errors = {};
                        this.modalOpen = true;
                    },
                    submit() {
                        const e = {};
                        if (!this.form.title.trim()) e.title = $t('deals.errors.title');
                        if (this.form.value === '' || this.form.value < 0 || isNaN(this.form.value)) e.value = $t('deals.errors.value');
                        this.errors = e;
                        if (Object.keys(e).length) return;
                        const s = window.crmHelpers.getStage(this.form.stage);
                        $store.crm.addDeal({
                            title: this.form.title,
                            value: Number(this.form.value),
                            stage: this.form.stage,
                            probability: s.probability,
                            contactId: this.form.contactId ? Number(this.form.contactId) : null,
                            companyId: this.form.companyId ? Number(this.form.companyId) : null,
                            owner: this.form.owner,
                            expectedClose: new Date(Date.now() + 30 * 86400000).toISOString().slice(0, 10),
                        });
                        $store.ui.notifySuccess($t('deals.created'));
                        this.modalOpen = false;
                    },
                };
            }
        </script>
    @endpush
</x-layout.app>
