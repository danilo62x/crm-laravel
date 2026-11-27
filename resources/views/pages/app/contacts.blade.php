<x-layout.app :pageTitle="$pageTitle ?? null">
    <div x-data="contactsPage()">
        {{-- Page header --}}
        <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-heading" x-text="$t('contacts.title')"></h1>
                <p class="mt-1 text-sm text-muted" x-text="$t('contacts.subtitle')"></p>
            </div>
            <button type="button" @click="openCreate()"
                class="flex h-10 shrink-0 items-center gap-2 rounded-lg bg-brand-500 px-4 text-sm font-medium text-white transition hover:bg-brand-600">
                <x-icon name="plus" :size="16" /> <span x-text="$t('contacts.new')"></span>
            </button>
        </div>

        {{-- Filters --}}
        <div class="mb-4 flex flex-wrap gap-3">
            <select x-model="statusFilter" class="h-10 rounded-lg border border-themed surface-card px-3 text-sm text-body focus-ring">
                <option value="" x-text="$t('contacts.filters.allStatuses')"></option>
                <template x-for="s in ['lead','active','customer','inactive']" :key="s"><option :value="s" x-text="$t('contacts.statuses.' + s)"></option></template>
            </select>
            <select x-model="ownerFilter" class="h-10 rounded-lg border border-themed surface-card px-3 text-sm text-body focus-ring">
                <option value="" x-text="$t('contacts.filters.allOwners')"></option>
                <template x-for="o in owners" :key="o"><option :value="o" x-text="o"></option></template>
            </select>
            <select x-model="tagFilter" class="h-10 rounded-lg border border-themed surface-card px-3 text-sm text-body focus-ring">
                <option value="" x-text="$t('contacts.filters.allTags')"></option>
                <template x-for="t in allTags" :key="t"><option :value="t" x-text="tagLabel(t)"></option></template>
            </select>
        </div>

        {{-- Table --}}
        <div class="surface-card overflow-hidden rounded-2xl border">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead>
                        <tr class="border-b border-themed text-muted">
                            <th class="px-5 py-3 font-medium" x-text="$t('contacts.columns.name')"></th>
                            <th class="px-5 py-3 font-medium" x-text="$t('contacts.columns.title')"></th>
                            <th class="px-5 py-3 font-medium" x-text="$t('contacts.columns.company')"></th>
                            <th class="px-5 py-3 font-medium" x-text="$t('contacts.columns.tags')"></th>
                            <th class="px-5 py-3 font-medium" x-text="$t('contacts.columns.status')"></th>
                            <th class="px-5 py-3"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="c in paged" :key="c.id">
                            <tr class="border-b border-themed last:border-0 hover:bg-gray-50 dark:hover:bg-gray-800/50">
                                <td class="px-5 py-3">
                                    <a :href="'{{ url('/app/contatos') }}/' + c.id" class="flex items-center gap-3 text-left">
                                        <span class="flex h-9 w-9 items-center justify-center rounded-full bg-brand-500 text-xs font-bold text-white"
                                            x-text="c.name.split(' ').slice(0,2).map(w => (w[0]||'').toUpperCase()).join('')"></span>
                                        <div>
                                            <p class="font-medium text-heading hover:text-brand-600" x-text="c.name"></p>
                                            <p class="text-xs text-muted" x-text="c.email"></p>
                                        </div>
                                    </a>
                                </td>
                                <td class="px-5 py-3 text-muted" x-text="c.title"></td>
                                <td class="px-5 py-3 text-muted" x-text="$store.crm.companyName(c.companyId)"></td>
                                <td class="px-5 py-3">
                                    <div class="flex flex-wrap gap-1">
                                        <template x-for="tag in c.tags.slice(0,2)" :key="tag"><x-crm.tag-pill tag="tag" /></template>
                                    </div>
                                </td>
                                <td class="px-5 py-3">
                                    <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-medium"
                                        :class="({ lead: 'bg-warning-50 text-warning-700 dark:bg-warning-500/15 dark:text-warning-500', active: 'bg-brand-50 text-brand-600 dark:bg-brand-500/15 dark:text-brand-400', customer: 'bg-success-50 text-success-700 dark:bg-success-500/15 dark:text-success-500', inactive: 'bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-300' })[c.status]"
                                        x-text="$t('contacts.statuses.' + c.status)"></span>
                                </td>
                                <td class="px-5 py-3">
                                    <div class="flex justify-end gap-1">
                                        <button type="button" @click="openEdit(c)" :aria-label="$t('common.edit')" class="rounded-md p-1.5 text-muted hover:bg-gray-100 hover:text-brand-600 dark:hover:bg-gray-800"><x-icon name="edit" :size="16" /></button>
                                        <button type="button" @click="$store.crm.removeContact(c.id); $store.ui.notifySuccess($t('contacts.removed'))" :aria-label="$t('common.delete')" class="rounded-md p-1.5 text-muted hover:bg-error-50 hover:text-error-600 dark:hover:bg-error-500/10"><x-icon name="trash" :size="16" /></button>
                                    </div>
                                </td>
                            </tr>
                        </template>
                        <template x-if="filtered.length === 0">
                            <tr><td colspan="6" class="px-5 py-10 text-center text-sm text-muted" x-text="$t('common.noResults')"></td></tr>
                        </template>
                    </tbody>
                </table>
            </div>
            {{-- Pagination --}}
            <div class="flex items-center justify-between border-t border-themed px-5 py-3 text-sm" x-show="totalPages > 1">
                <button type="button" @click="page = Math.max(1, page - 1)" :disabled="page === 1" class="rounded-lg border border-themed px-3 py-1.5 text-body disabled:opacity-50" x-text="$t('common.previous')"></button>
                <span class="text-muted" x-text="page + ' / ' + totalPages"></span>
                <button type="button" @click="page = Math.min(totalPages, page + 1)" :disabled="page === totalPages" class="rounded-lg border border-themed px-3 py-1.5 text-body disabled:opacity-50" x-text="$t('common.next')"></button>
            </div>
        </div>

        {{-- Create/Edit modal --}}
        <div x-show="modalOpen" x-cloak class="fixed inset-0 z-[80] flex items-center justify-center p-4" @keydown.escape.window="modalOpen = false">
            <div class="absolute inset-0 bg-gray-900/50" @click="modalOpen = false"></div>
            <div class="surface-card relative w-full max-w-2xl rounded-2xl border p-6 shadow-theme-xl" x-transition>
                <div class="mb-5 flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-heading" x-text="editing ? $t('contacts.editTitle') : $t('contacts.newTitle')"></h3>
                    <button type="button" @click="modalOpen = false" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200"><x-icon name="x" :size="20" /></button>
                </div>
                <form class="space-y-4" @submit.prevent="submit()">
                    <div class="grid gap-4 sm:grid-cols-2">
                        <div class="space-y-1.5">
                            <label class="block text-sm font-medium text-heading" x-text="$t('contacts.columns.name')"></label>
                            <input x-model="form.name" placeholder="Maria Silva" class="focus-ring w-full rounded-lg border bg-transparent px-3.5 py-2.5 text-sm text-body" :class="errors.name ? 'border-error-500' : 'border-themed'" />
                            <p x-show="errors.name" x-cloak class="text-xs text-error-500" x-text="errors.name"></p>
                        </div>
                        <div class="space-y-1.5">
                            <label class="block text-sm font-medium text-heading" x-text="$t('contacts.columns.email')"></label>
                            <input type="email" x-model="form.email" placeholder="maria@empresa.com" class="focus-ring w-full rounded-lg border bg-transparent px-3.5 py-2.5 text-sm text-body" :class="errors.email ? 'border-error-500' : 'border-themed'" />
                            <p x-show="errors.email" x-cloak class="text-xs text-error-500" x-text="errors.email"></p>
                        </div>
                    </div>
                    <div class="grid gap-4 sm:grid-cols-2">
                        <div class="space-y-1.5">
                            <label class="block text-sm font-medium text-heading" x-text="$t('contacts.columns.phone')"></label>
                            <input x-model="form.phone" placeholder="+55 11 90000-0000" class="focus-ring w-full rounded-lg border border-themed bg-transparent px-3.5 py-2.5 text-sm text-body" />
                        </div>
                        <div class="space-y-1.5">
                            <label class="block text-sm font-medium text-heading" x-text="$t('contacts.columns.title')"></label>
                            <input x-model="form.title" class="focus-ring w-full rounded-lg border border-themed bg-transparent px-3.5 py-2.5 text-sm text-body" />
                        </div>
                    </div>
                    <div class="grid gap-4 sm:grid-cols-2">
                        <div class="space-y-1.5">
                            <label class="block text-sm font-medium text-heading" x-text="$t('contacts.columns.company')"></label>
                            <select x-model="form.companyId" class="focus-ring w-full rounded-lg border border-themed bg-transparent px-3.5 py-2.5 text-sm text-body">
                                <option value="">—</option>
                                <template x-for="c in $store.crm.companies" :key="c.id"><option :value="c.id" x-text="c.name"></option></template>
                            </select>
                        </div>
                        <div class="space-y-1.5">
                            <label class="block text-sm font-medium text-heading" x-text="$t('contacts.columns.owner')"></label>
                            <select x-model="form.owner" class="focus-ring w-full rounded-lg border border-themed bg-transparent px-3.5 py-2.5 text-sm text-body">
                                <template x-for="o in owners" :key="o"><option :value="o" x-text="o"></option></template>
                            </select>
                        </div>
                    </div>
                    <div class="grid gap-4 sm:grid-cols-2">
                        <div class="space-y-1.5">
                            <label class="block text-sm font-medium text-heading" x-text="$t('contacts.columns.status')"></label>
                            <select x-model="form.status" class="focus-ring w-full rounded-lg border border-themed bg-transparent px-3.5 py-2.5 text-sm text-body">
                                <template x-for="s in ['lead','active','customer','inactive']" :key="s"><option :value="s" x-text="$t('contacts.statuses.' + s)"></option></template>
                            </select>
                        </div>
                        <div class="space-y-1.5">
                            <label class="block text-sm font-medium text-heading" x-text="$t('contacts.tagsLabel')"></label>
                            <input x-model="form.tags" placeholder="vip, hot" class="focus-ring w-full rounded-lg border border-themed bg-transparent px-3.5 py-2.5 text-sm text-body" />
                            <p class="text-xs text-muted" x-text="$t('contacts.tagsHint')"></p>
                        </div>
                    </div>
                    <div class="flex justify-end gap-2 pt-2">
                        <button type="button" @click="modalOpen = false" class="rounded-lg border border-themed px-4 py-2 text-sm font-medium text-body hover:bg-gray-50 dark:hover:bg-gray-800" x-text="$t('common.cancel')"></button>
                        <button type="submit" class="rounded-lg bg-brand-500 px-4 py-2 text-sm font-medium text-white hover:bg-brand-600" x-text="$t('common.save')"></button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            function contactsPage() {
                return {
                    statusFilter: '', ownerFilter: '', tagFilter: '',
                    page: 1, pageSize: 8,
                    owners: window.crmHelpers.owners(),
                    modalOpen: false, editing: null, form: {}, errors: {},

                    get allTags() {
                        return [...new Set($store.crm.contacts.flatMap(c => c.tags))].sort();
                    },
                    tagLabel(t) { const v = $t('tags.' + t); return v === 'tags.' + t ? t : v; },
                    get filtered() {
                        return $store.crm.contacts.filter(c =>
                            (!this.statusFilter || c.status === this.statusFilter) &&
                            (!this.ownerFilter || c.owner === this.ownerFilter) &&
                            (!this.tagFilter || c.tags.includes(this.tagFilter))
                        );
                    },
                    get totalPages() { return Math.max(1, Math.ceil(this.filtered.length / this.pageSize)); },
                    get paged() {
                        const p = Math.min(this.page, this.totalPages);
                        return this.filtered.slice((p - 1) * this.pageSize, p * this.pageSize);
                    },
                    openCreate() {
                        this.editing = null;
                        this.form = { name: '', email: '', phone: '', title: '', companyId: '', owner: this.owners[0], status: 'lead', tags: '' };
                        this.errors = {};
                        this.modalOpen = true;
                    },
                    openEdit(c) {
                        this.editing = c;
                        this.form = { name: c.name, email: c.email, phone: c.phone, title: c.title, companyId: c.companyId ?? '', owner: c.owner, status: c.status, tags: c.tags.join(', ') };
                        this.errors = {};
                        this.modalOpen = true;
                    },
                    submit() {
                        const e = {};
                        if (!this.form.name.trim()) e.name = $t('contacts.errors.name');
                        if (!/^[^@\s]+@[^@\s]+\.[^@\s]+$/.test(this.form.email)) e.email = $t('contacts.errors.email');
                        this.errors = e;
                        if (Object.keys(e).length) return;
                        const tags = this.form.tags.split(',').map(s => s.trim().toLowerCase()).filter(Boolean);
                        const payload = {
                            name: this.form.name, email: this.form.email, phone: this.form.phone, title: this.form.title,
                            companyId: this.form.companyId ? Number(this.form.companyId) : null,
                            owner: this.form.owner, status: this.form.status, tags,
                        };
                        if (this.editing) {
                            $store.crm.updateContact(this.editing.id, payload);
                            $store.ui.notifySuccess($t('contacts.updated'));
                        } else {
                            $store.crm.addContact(payload);
                            $store.ui.notifySuccess($t('contacts.created'));
                        }
                        this.modalOpen = false;
                    },
                };
            }
        </script>
    @endpush
</x-layout.app>
