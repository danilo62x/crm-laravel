<x-layout.app :pageTitle="$pageTitle ?? null">
    <div x-data="activitiesPage()">
        <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-heading" x-text="$t('activities.title')"></h1>
                <p class="mt-1 text-sm text-muted" x-text="$t('activities.subtitle')"></p>
            </div>
            <button type="button" @click="openCreate()"
                class="flex h-10 shrink-0 items-center gap-2 rounded-lg bg-brand-500 px-4 text-sm font-medium text-white transition hover:bg-brand-600">
                <x-icon name="plus" :size="16" /> <span x-text="$t('activities.new')"></span>
            </button>
        </div>

        <div class="mb-4 flex flex-wrap gap-3">
            <select x-model="typeFilter" class="h-10 rounded-lg border border-themed surface-card px-3 text-sm text-body focus-ring">
                <option value="" x-text="$t('activities.filters.allTypes')"></option>
                <template x-for="t in ['call','email','meeting','task']" :key="t"><option :value="t" x-text="$t('activities.types.' + t)"></option></template>
            </select>
        </div>

        <div class="grid gap-5 lg:grid-cols-3">
            <template x-for="sec in [{ key: 'today', items: today }, { key: 'upcoming', items: upcoming }, { key: 'done', items: done }]" :key="sec.key">
                <div class="surface-card rounded-2xl border p-5">
                    <div class="mb-4 flex items-center gap-2">
                        <h3 class="text-base font-semibold text-heading" x-text="$t('activities.sections.' + sec.key)"></h3>
                        <span class="rounded-full bg-gray-100 px-2 py-0.5 text-xs font-medium text-muted dark:bg-gray-800" x-text="sec.items.length"></span>
                    </div>
                    <template x-if="sec.items.length">
                        <x-crm.activity-timeline items="sec.items" />
                    </template>
                    <template x-if="!sec.items.length">
                        <p class="py-4 text-center text-sm text-muted" x-text="$t('activities.empty')"></p>
                    </template>
                </div>
            </template>
        </div>

        {{-- Create modal --}}
        <div x-show="modalOpen" x-cloak class="fixed inset-0 z-[80] flex items-center justify-center p-4" @keydown.escape.window="modalOpen = false">
            <div class="absolute inset-0 bg-gray-900/50" @click="modalOpen = false"></div>
            <div class="surface-card relative w-full max-w-lg rounded-2xl border p-6 shadow-theme-xl" x-transition>
                <div class="mb-5 flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-heading" x-text="$t('activities.newTitle')"></h3>
                    <button type="button" @click="modalOpen = false" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200"><x-icon name="x" :size="20" /></button>
                </div>
                <form class="space-y-4" @submit.prevent="submit()">
                    <div class="space-y-1.5">
                        <label class="block text-sm font-medium text-heading" x-text="$t('activities.columns.title')"></label>
                        <input x-model="form.title" :placeholder="$t('activities.titlePlaceholder')" class="focus-ring w-full rounded-lg border bg-transparent px-3.5 py-2.5 text-sm text-body" :class="errors.title ? 'border-error-500' : 'border-themed'" />
                        <p x-show="errors.title" x-cloak class="text-xs text-error-500" x-text="errors.title"></p>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-1.5">
                            <label class="block text-sm font-medium text-heading" x-text="$t('activities.columns.type')"></label>
                            <select x-model="form.type" class="focus-ring w-full rounded-lg border border-themed bg-transparent px-3.5 py-2.5 text-sm text-body">
                                <template x-for="t in ['call','email','meeting','task']" :key="t"><option :value="t" x-text="$t('activities.types.' + t)"></option></template>
                            </select>
                        </div>
                        <div class="space-y-1.5">
                            <label class="block text-sm font-medium text-heading" x-text="$t('activities.columns.date')"></label>
                            <input type="date" x-model="form.date" class="focus-ring w-full rounded-lg border border-themed bg-transparent px-3.5 py-2.5 text-sm text-body" />
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-1.5">
                            <label class="block text-sm font-medium text-heading" x-text="$t('activities.columns.contact')"></label>
                            <select x-model="form.contactId" class="focus-ring w-full rounded-lg border border-themed bg-transparent px-3.5 py-2.5 text-sm text-body">
                                <option value="">—</option>
                                <template x-for="c in $store.crm.contacts" :key="c.id"><option :value="c.id" x-text="c.name"></option></template>
                            </select>
                        </div>
                        <div class="space-y-1.5">
                            <label class="block text-sm font-medium text-heading" x-text="$t('activities.columns.owner')"></label>
                            <select x-model="form.owner" class="focus-ring w-full rounded-lg border border-themed bg-transparent px-3.5 py-2.5 text-sm text-body">
                                <template x-for="o in owners" :key="o"><option :value="o" x-text="o"></option></template>
                            </select>
                        </div>
                    </div>
                    <div class="flex justify-end gap-2 pt-2">
                        <button type="button" @click="modalOpen = false" class="rounded-lg border border-themed px-4 py-2 text-sm font-medium text-body hover:bg-gray-50 dark:hover:bg-gray-800" x-text="$t('common.cancel')"></button>
                        <button type="submit" class="rounded-lg bg-brand-500 px-4 py-2 text-sm font-medium text-white hover:bg-brand-600" x-text="$t('activities.create')"></button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            function activitiesPage() {
                return {
                    typeFilter: '',
                    owners: window.crmHelpers.owners(),
                    modalOpen: false, form: {}, errors: {},
                    get filtered() {
                        return this.typeFilter ? $store.crm.activities.filter(a => a.type === this.typeFilter) : $store.crm.activities;
                    },
                    isToday(iso) { return new Date(iso).toDateString() === new Date().toDateString(); },
                    get open() { return this.filtered.filter(a => a.status === 'open'); },
                    get today() { return this.open.filter(a => this.isToday(a.date)).slice().sort((a, b) => new Date(a.date) - new Date(b.date)); },
                    get upcoming() { return this.open.filter(a => !this.isToday(a.date) && new Date(a.date) > Date.now()).slice().sort((a, b) => new Date(a.date) - new Date(b.date)); },
                    get done() { return this.filtered.filter(a => a.status === 'done').slice().sort((a, b) => new Date(b.date) - new Date(a.date)); },
                    openCreate() {
                        this.form = { title: '', type: 'task', contactId: '', owner: this.owners[0], date: new Date().toISOString().slice(0, 10) };
                        this.errors = {};
                        this.modalOpen = true;
                    },
                    submit() {
                        const e = {};
                        if (!this.form.title.trim()) e.title = $t('activities.errors.title');
                        this.errors = e;
                        if (Object.keys(e).length) return;
                        $store.crm.addActivity({
                            title: this.form.title,
                            type: this.form.type,
                            status: 'open',
                            date: new Date(this.form.date).toISOString(),
                            contactId: this.form.contactId ? Number(this.form.contactId) : null,
                            dealId: null,
                            owner: this.form.owner,
                        });
                        $store.ui.notifySuccess($t('activities.created'));
                        this.modalOpen = false;
                    },
                };
            }
        </script>
    @endpush
</x-layout.app>
