<x-layout.app :pageTitle="$pageTitle ?? null">
    <div x-data="companiesPage()">
        <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-heading" x-text="$t('companies.title')"></h1>
                <p class="mt-1 text-sm text-muted" x-text="$t('companies.subtitle')"></p>
            </div>
            <button type="button" @click="openCreate()"
                class="flex h-10 shrink-0 items-center gap-2 rounded-lg bg-brand-500 px-4 text-sm font-medium text-white transition hover:bg-brand-600">
                <x-icon name="plus" :size="16" /> <span x-text="$t('companies.new')"></span>
            </button>
        </div>

        <div class="surface-card overflow-hidden rounded-2xl border">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead>
                        <tr class="border-b border-themed text-muted">
                            <th class="px-5 py-3 font-medium" x-text="$t('companies.columns.name')"></th>
                            <th class="px-5 py-3 font-medium" x-text="$t('companies.columns.industry')"></th>
                            <th class="px-5 py-3 font-medium" x-text="$t('companies.columns.size')"></th>
                            <th class="px-5 py-3 font-medium" x-text="$t('companies.columns.contacts')"></th>
                            <th class="px-5 py-3 font-medium" x-text="$t('companies.columns.value')"></th>
                            <th class="px-5 py-3"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="c in paged" :key="c.id">
                            <tr class="border-b border-themed last:border-0 hover:bg-gray-50 dark:hover:bg-gray-800/50">
                                <td class="px-5 py-3">
                                    <div class="flex items-center gap-3">
                                        <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-brand-50 font-bold text-brand-500 dark:bg-brand-500/15" x-text="c.name.charAt(0)"></span>
                                        <div>
                                            <p class="font-medium text-heading" x-text="c.name"></p>
                                            <p class="text-xs text-muted" x-text="c.website"></p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-5 py-3 text-muted" x-text="c.industry"></td>
                                <td class="px-5 py-3">
                                    <span class="inline-flex items-center gap-1.5 rounded-full bg-gray-100 px-2.5 py-1 text-xs font-medium text-gray-700 dark:bg-gray-800 dark:text-gray-300" x-text="$t('companies.sizes.' + c.size)"></span>
                                </td>
                                <td class="px-5 py-3 text-muted" x-text="contactCount(c.id)"></td>
                                <td class="px-5 py-3 font-semibold text-heading" x-text="$currency(dealValue(c.id))"></td>
                                <td class="px-5 py-3">
                                    <div class="flex justify-end">
                                        <button type="button" @click="$store.crm.removeCompany(c.id); $store.ui.notifySuccess($t('companies.removed'))" :aria-label="$t('common.delete')" class="rounded-md p-1.5 text-muted hover:bg-error-50 hover:text-error-600 dark:hover:bg-error-500/10"><x-icon name="trash" :size="16" /></button>
                                    </div>
                                </td>
                            </tr>
                        </template>
                        <template x-if="$store.crm.companies.length === 0">
                            <tr><td colspan="6" class="px-5 py-10 text-center text-sm text-muted" x-text="$t('common.noResults')"></td></tr>
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

        {{-- Create modal --}}
        <div x-show="modalOpen" x-cloak class="fixed inset-0 z-[80] flex items-center justify-center p-4" @keydown.escape.window="modalOpen = false">
            <div class="absolute inset-0 bg-gray-900/50" @click="modalOpen = false"></div>
            <div class="surface-card relative w-full max-w-lg rounded-2xl border p-6 shadow-theme-xl" x-transition>
                <div class="mb-5 flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-heading" x-text="$t('companies.newTitle')"></h3>
                    <button type="button" @click="modalOpen = false" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200"><x-icon name="x" :size="20" /></button>
                </div>
                <form class="space-y-4" @submit.prevent="submit()">
                    <div class="space-y-1.5">
                        <label class="block text-sm font-medium text-heading" x-text="$t('companies.columns.name')"></label>
                        <input x-model="form.name" placeholder="Acme Inc." class="focus-ring w-full rounded-lg border bg-transparent px-3.5 py-2.5 text-sm text-body" :class="errors.name ? 'border-error-500' : 'border-themed'" />
                        <p x-show="errors.name" x-cloak class="text-xs text-error-500" x-text="errors.name"></p>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-1.5">
                            <label class="block text-sm font-medium text-heading" x-text="$t('companies.columns.industry')"></label>
                            <input x-model="form.industry" placeholder="Software" class="focus-ring w-full rounded-lg border border-themed bg-transparent px-3.5 py-2.5 text-sm text-body" />
                        </div>
                        <div class="space-y-1.5">
                            <label class="block text-sm font-medium text-heading" x-text="$t('companies.columns.size')"></label>
                            <select x-model="form.size" class="focus-ring w-full rounded-lg border border-themed bg-transparent px-3.5 py-2.5 text-sm text-body">
                                <template x-for="s in ['small','medium','large','enterprise']" :key="s"><option :value="s" x-text="$t('companies.sizes.' + s)"></option></template>
                            </select>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-1.5">
                            <label class="block text-sm font-medium text-heading" x-text="$t('companies.columns.website')"></label>
                            <input x-model="form.website" placeholder="acme.com" class="focus-ring w-full rounded-lg border border-themed bg-transparent px-3.5 py-2.5 text-sm text-body" />
                        </div>
                        <div class="space-y-1.5">
                            <label class="block text-sm font-medium text-heading" x-text="$t('companies.columns.city')"></label>
                            <input x-model="form.city" placeholder="São Paulo" class="focus-ring w-full rounded-lg border border-themed bg-transparent px-3.5 py-2.5 text-sm text-body" />
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
            function companiesPage() {
                return {
                    page: 1, pageSize: 8,
                    modalOpen: false, form: {}, errors: {},
                    contactCount(id) { return $store.crm.contacts.filter(c => c.companyId === id).length; },
                    dealValue(id) { return $store.crm.deals.filter(d => d.companyId === id).reduce((s, d) => s + d.value, 0); },
                    get totalPages() { return Math.max(1, Math.ceil($store.crm.companies.length / this.pageSize)); },
                    get paged() {
                        const p = Math.min(this.page, this.totalPages);
                        return $store.crm.companies.slice((p - 1) * this.pageSize, p * this.pageSize);
                    },
                    openCreate() {
                        this.form = { name: '', industry: '', size: 'medium', website: '', city: '' };
                        this.errors = {};
                        this.modalOpen = true;
                    },
                    submit() {
                        const e = {};
                        if (!this.form.name.trim()) e.name = $t('companies.errors.name');
                        this.errors = e;
                        if (Object.keys(e).length) return;
                        $store.crm.addCompany({ ...this.form });
                        $store.ui.notifySuccess($t('companies.created'));
                        this.modalOpen = false;
                    },
                };
            }
        </script>
    @endpush
</x-layout.app>
