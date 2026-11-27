<x-layout.app :pageTitle="$pageTitle ?? null">
    @php($cid = $contactId)
    <div x-data="contactDetail({{ $cid }})">
        {{-- Not found --}}
        <template x-if="!contact">
            <div class="flex flex-col items-center justify-center rounded-2xl border border-themed py-16 text-center">
                <span class="flex h-14 w-14 items-center justify-center rounded-2xl bg-gray-100 text-muted dark:bg-gray-800"><x-icon name="users" :size="26" /></span>
                <p class="mt-4 text-sm font-medium text-heading" x-text="$t('contacts.notFound')"></p>
                <a href="{{ route('app.contacts') }}" class="mt-3 text-sm font-medium text-brand-600" x-text="$t('common.back')"></a>
            </div>
        </template>

        <template x-if="contact">
            <div>
                {{-- Header with breadcrumb --}}
                <div class="mb-6">
                    <nav aria-label="Breadcrumb" class="mb-2">
                        <ol class="flex flex-wrap items-center gap-1.5 text-sm">
                            <li class="flex items-center gap-1.5">
                                <a href="{{ route('app.contacts') }}" class="inline-flex items-center gap-1.5 text-muted transition hover:text-brand-600 dark:hover:text-brand-400" x-text="$t('appNav.contacts')"></a>
                                <x-icon name="chevron-right" :size="14" class="text-gray-400" />
                            </li>
                            <li><span aria-current="page" class="font-medium text-heading" x-text="contact.name"></span></li>
                        </ol>
                    </nav>
                    <h1 class="text-2xl font-bold text-heading" x-text="contact.name"></h1>
                </div>

                <div class="grid gap-5 lg:grid-cols-[320px_1fr]">
                    {{-- Sidebar card --}}
                    <aside class="surface-card h-fit rounded-2xl border p-6 text-center">
                        <span class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-brand-500 text-lg font-bold text-white"
                            x-text="contact.name.split(' ').slice(0,2).map(w => (w[0]||'').toUpperCase()).join('')"></span>
                        <h2 class="mt-4 text-lg font-bold text-heading" x-text="contact.name"></h2>
                        <p class="text-sm text-muted" x-text="contact.title"></p>
                        <div class="mt-3 flex justify-center">
                            <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-medium"
                                :class="contact.status === 'customer' ? 'bg-success-50 text-success-700 dark:bg-success-500/15 dark:text-success-500' : contact.status === 'inactive' ? 'bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-300' : 'bg-brand-50 text-brand-600 dark:bg-brand-500/15 dark:text-brand-400'"
                                x-text="$t('contacts.statuses.' + contact.status)"></span>
                        </div>
                        <div class="mt-4 flex flex-wrap justify-center gap-1.5" x-show="contact.tags.length">
                            <template x-for="tag in contact.tags" :key="tag"><x-crm.tag-pill tag="tag" /></template>
                        </div>
                        <div class="mt-5 flex justify-center gap-2">
                            <a :href="'mailto:' + contact.email" class="flex h-10 flex-1 items-center justify-center gap-2 rounded-lg border border-themed text-sm font-medium text-body hover:bg-gray-50 dark:hover:bg-gray-800">
                                <x-icon name="mail" :size="16" /> <span x-text="$t('contacts.email')"></span>
                            </a>
                            <a :href="'tel:' + contact.phone" class="flex h-10 flex-1 items-center justify-center gap-2 rounded-lg bg-brand-500 text-sm font-medium text-white hover:bg-brand-600">
                                <x-icon name="phone" :size="16" /> <span x-text="$t('contacts.call')"></span>
                            </a>
                        </div>
                    </aside>

                    {{-- Tabs --}}
                    <div class="surface-card rounded-2xl border p-6">
                        <div class="mb-5 flex flex-wrap gap-1 border-b border-themed">
                            <template x-for="t in tabs" :key="t.id">
                                <button type="button" @click="tab = t.id"
                                    class="flex items-center gap-2 border-b-2 px-4 py-2.5 text-sm font-medium transition"
                                    :class="tab === t.id ? 'border-brand-500 text-brand-600 dark:text-brand-400' : 'border-transparent text-muted hover:text-heading'"
                                    x-text="$t(t.labelKey)"></button>
                            </template>
                        </div>

                        {{-- Overview --}}
                        <div x-show="tab === 'overview'" class="grid gap-5 sm:grid-cols-2">
                            <template x-for="f in overviewFields" :key="f.label">
                                <div class="flex items-center gap-3 rounded-xl border border-themed p-4">
                                    <span class="flex h-10 w-10 items-center justify-center rounded-lg bg-brand-50 text-brand-500 dark:bg-brand-500/15" x-html="f.icon"></span>
                                    <div>
                                        <p class="text-xs text-muted" x-text="f.label"></p>
                                        <p class="text-sm font-medium text-heading" x-text="f.value"></p>
                                    </div>
                                </div>
                            </template>
                        </div>

                        {{-- Activities --}}
                        <div x-show="tab === 'activities'">
                            <template x-if="activities.length">
                                <x-crm.activity-timeline items="activities" />
                            </template>
                            <template x-if="!activities.length">
                                <p class="py-6 text-center text-sm text-muted" x-text="$t('activities.empty')"></p>
                            </template>
                        </div>

                        {{-- Notes --}}
                        <div x-show="tab === 'notes'">
                            <div class="flex gap-2">
                                <textarea x-model="noteText" rows="2" :placeholder="$t('notes.placeholder')" class="focus-ring flex-1 rounded-lg border border-themed bg-transparent px-3 py-2 text-sm text-body"></textarea>
                                <button type="button" @click="submitNote()" class="self-end rounded-lg bg-brand-500 px-4 py-2 text-sm font-medium text-white hover:bg-brand-600" x-text="$t('notes.add')"></button>
                            </div>
                            <template x-if="notes.length">
                                <ul class="mt-5 space-y-3">
                                    <template x-for="n in notes" :key="n.id">
                                        <li class="rounded-xl border border-themed p-4">
                                            <p class="text-sm text-body" x-text="n.body"></p>
                                            <p class="mt-2 text-xs text-muted"><span x-text="n.author"></span> · <span x-text="$fromNow(n.createdAt)"></span></p>
                                        </li>
                                    </template>
                                </ul>
                            </template>
                            <template x-if="!notes.length">
                                <p class="mt-6 text-center text-sm text-muted" x-text="$t('notes.empty')"></p>
                            </template>
                        </div>

                        {{-- Deals --}}
                        <div x-show="tab === 'deals'">
                            <template x-if="deals.length">
                                <ul class="space-y-3">
                                    <template x-for="d in deals" :key="d.id">
                                        <li class="flex items-center justify-between rounded-xl border border-themed p-4">
                                            <div>
                                                <p class="text-sm font-medium text-heading" x-text="d.title"></p>
                                                <div class="mt-1"><x-crm.stage-pill stage="d.stage" size="sm" /></div>
                                            </div>
                                            <span class="text-sm font-semibold text-heading" x-text="$currency(d.value)"></span>
                                        </li>
                                    </template>
                                </ul>
                            </template>
                            <template x-if="!deals.length">
                                <p class="py-6 text-center text-sm text-muted" x-text="$t('deals.empty')"></p>
                            </template>
                        </div>
                    </div>
                </div>
            </div>
        </template>
    </div>

    @push('scripts')
        <script>
            function contactDetail(cid) {
                return {
                    cid,
                    tab: 'overview',
                    noteText: '',
                    tabs: [
                        { id: 'overview', labelKey: 'contacts.tabs.overview' },
                        { id: 'activities', labelKey: 'contacts.tabs.activities' },
                        { id: 'notes', labelKey: 'contacts.tabs.notes' },
                        { id: 'deals', labelKey: 'contacts.tabs.deals' },
                    ],
                    get contact() { return $store.crm.contacts.find(c => c.id === this.cid) || null; },
                    get activities() {
                        return $store.crm.activities.filter(a => a.contactId === this.cid).slice().sort((a, b) => new Date(b.date) - new Date(a.date));
                    },
                    get deals() { return $store.crm.deals.filter(d => d.contactId === this.cid); },
                    get notes() {
                        return $store.crm.notes.filter(n => n.contactId === this.cid).slice().sort((a, b) => new Date(b.createdAt) - new Date(a.createdAt));
                    },
                    get overviewFields() {
                        const c = this.contact; if (!c) return [];
                        return [
                            { icon: '<svg width=18 height=18 viewBox="0 0 24 24" fill=none stroke=currentColor stroke-width=1.8 stroke-linecap=round stroke-linejoin=round><path d="M4 4h16a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2z"/><polyline points="22 6 12 13 2 6"/></svg>', label: $t('contacts.columns.email'), value: c.email },
                            { icon: '<svg width=18 height=18 viewBox="0 0 24 24" fill=none stroke=currentColor stroke-width=1.8 stroke-linecap=round stroke-linejoin=round><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.13.96.36 1.9.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.91.34 1.85.57 2.81.7A2 2 0 0 1 22 16.92z"/></svg>', label: $t('contacts.columns.phone'), value: c.phone },
                            { icon: '<svg width=18 height=18 viewBox="0 0 24 24" fill=none stroke=currentColor stroke-width=1.8 stroke-linecap=round stroke-linejoin=round><rect x=2 y=7 width=20 height=14 rx=2/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>', label: $t('contacts.columns.company'), value: $store.crm.companyName(c.companyId) },
                            { icon: '<svg width=18 height=18 viewBox="0 0 24 24" fill=none stroke=currentColor stroke-width=1.8 stroke-linecap=round stroke-linejoin=round><circle cx=12 cy=8 r=4/><path d="M4 21c0-4 4-7 8-7s8 3 8 7"/></svg>', label: $t('contacts.columns.title'), value: c.title },
                            { icon: '<svg width=18 height=18 viewBox="0 0 24 24" fill=none stroke=currentColor stroke-width=1.8 stroke-linecap=round stroke-linejoin=round><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx=9 cy=7 r=4/></svg>', label: $t('contacts.columns.owner'), value: c.owner },
                            { icon: '<svg width=18 height=18 viewBox="0 0 24 24" fill=none stroke=currentColor stroke-width=1.8 stroke-linecap=round stroke-linejoin=round><circle cx=12 cy=12 r=10/><polyline points="12 6 12 12 16 14"/></svg>', label: $t('contacts.createdAt'), value: $date(c.createdAt) },
                        ];
                    },
                    submitNote() {
                        if (!this.noteText.trim()) return;
                        $store.crm.addNote({ body: this.noteText.trim(), contactId: this.cid, dealId: null, author: $store.auth.user?.name || 'Você' });
                        this.noteText = '';
                        $store.ui.notifySuccess($t('notes.added'));
                    },
                };
            }
        </script>
    @endpush
</x-layout.app>
