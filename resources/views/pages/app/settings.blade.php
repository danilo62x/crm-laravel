<x-layout.app :pageTitle="$pageTitle ?? null">
    @php($stages = \App\Support\CrmData::stages())
    @php($owners = \App\Support\CrmData::owners())

    <div x-data="{ tab: 'profile' }">
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-heading" x-text="$t('settings.title')"></h1>
            <p class="mt-1 text-sm text-muted" x-text="$t('settings.subtitle')"></p>
        </div>

        <div class="surface-card rounded-2xl border p-6">
            {{-- Tabs --}}
            <div class="mb-6 flex flex-wrap gap-1 border-b border-themed">
                @php($tabs = [
                    ['id' => 'profile', 'key' => 'settings.tabs.profile', 'icon' => 'user'],
                    ['id' => 'account', 'key' => 'settings.tabs.account', 'icon' => 'lock'],
                    ['id' => 'team', 'key' => 'settings.tabs.team', 'icon' => 'users'],
                    ['id' => 'pipeline', 'key' => 'settings.tabs.pipeline', 'icon' => 'layers'],
                    ['id' => 'notifications', 'key' => 'settings.tabs.notifications', 'icon' => 'bell'],
                ])
                @foreach ($tabs as $t)
                    <button type="button" @click="tab = '{{ $t['id'] }}'"
                        class="flex items-center gap-2 border-b-2 px-4 py-2.5 text-sm font-medium transition"
                        :class="tab === '{{ $t['id'] }}' ? 'border-brand-500 text-brand-600 dark:text-brand-400' : 'border-transparent text-muted hover:text-heading'">
                        <x-icon name="{{ $t['icon'] }}" :size="16" /> <span x-text="$t('{{ $t['key'] }}')"></span>
                    </button>
                @endforeach
            </div>

            {{-- Profile --}}
            <div x-show="tab === 'profile'"
                x-data="{ form: { name: $store.auth.user?.name || '', email: $store.auth.user?.email || '', role: $store.auth.user?.role || '', company: $store.auth.user?.company || '' },
                    save() { $store.auth.updateProfile(this.form); $store.ui.notifySuccess($t('settings.profile.saved')); } }"
                @submit.prevent="save()">
                <form class="max-w-xl space-y-4">
                    <div class="flex items-center gap-4">
                        <span class="flex h-14 w-14 items-center justify-center rounded-full bg-brand-500 text-base font-bold text-white"
                            x-text="(form.name || '?').split(' ').slice(0,2).map(w => (w[0]||'').toUpperCase()).join('')"></span>
                        <button type="button" class="rounded-lg border border-themed px-4 py-2 text-sm font-medium text-body hover:bg-gray-50 dark:hover:bg-gray-800" x-text="$t('settings.profile.changePhoto')"></button>
                    </div>
                    <div class="grid gap-4 sm:grid-cols-2">
                        <div class="space-y-1.5">
                            <label class="block text-sm font-medium text-heading" x-text="$t('settings.profile.name')"></label>
                            <input x-model="form.name" class="focus-ring w-full rounded-lg border border-themed bg-transparent px-3.5 py-2.5 text-sm text-body" />
                        </div>
                        <div class="space-y-1.5">
                            <label class="block text-sm font-medium text-heading" x-text="$t('settings.profile.email')"></label>
                            <input type="email" x-model="form.email" class="focus-ring w-full rounded-lg border border-themed bg-transparent px-3.5 py-2.5 text-sm text-body" />
                        </div>
                    </div>
                    <div class="grid gap-4 sm:grid-cols-2">
                        <div class="space-y-1.5">
                            <label class="block text-sm font-medium text-heading" x-text="$t('settings.profile.role')"></label>
                            <input x-model="form.role" class="focus-ring w-full rounded-lg border border-themed bg-transparent px-3.5 py-2.5 text-sm text-body" />
                        </div>
                        <div class="space-y-1.5">
                            <label class="block text-sm font-medium text-heading" x-text="$t('settings.profile.company')"></label>
                            <input x-model="form.company" class="focus-ring w-full rounded-lg border border-themed bg-transparent px-3.5 py-2.5 text-sm text-body" />
                        </div>
                    </div>
                    <button type="submit" class="rounded-lg bg-brand-500 px-5 py-2.5 text-sm font-medium text-white hover:bg-brand-600" x-text="$t('settings.profile.save')"></button>
                </form>
            </div>

            {{-- Account --}}
            <div x-show="tab === 'account'" x-cloak>
                <form class="max-w-xl space-y-4" @submit.prevent="$store.ui.notifySuccess($t('settings.account.updated')); $el.reset()">
                    <div class="space-y-1.5">
                        <label class="block text-sm font-medium text-heading" x-text="$t('settings.account.current')"></label>
                        <input type="password" class="focus-ring w-full rounded-lg border border-themed bg-transparent px-3.5 py-2.5 text-sm text-body" />
                    </div>
                    <div class="grid gap-4 sm:grid-cols-2">
                        <div class="space-y-1.5">
                            <label class="block text-sm font-medium text-heading" x-text="$t('settings.account.new')"></label>
                            <input type="password" class="focus-ring w-full rounded-lg border border-themed bg-transparent px-3.5 py-2.5 text-sm text-body" />
                        </div>
                        <div class="space-y-1.5">
                            <label class="block text-sm font-medium text-heading" x-text="$t('settings.account.confirm')"></label>
                            <input type="password" class="focus-ring w-full rounded-lg border border-themed bg-transparent px-3.5 py-2.5 text-sm text-body" />
                        </div>
                    </div>
                    <button type="submit" class="rounded-lg bg-brand-500 px-5 py-2.5 text-sm font-medium text-white hover:bg-brand-600" x-text="$t('settings.account.update')"></button>
                    <div class="mt-6 rounded-xl border border-error-200 p-5 dark:border-error-500/30">
                        <h4 class="text-sm font-semibold text-error-600" x-text="$t('settings.account.dangerTitle')"></h4>
                        <p class="mt-1 text-sm text-muted" x-text="$t('settings.account.dangerDesc')"></p>
                        <button type="button" class="mt-3 rounded-lg border border-error-300 px-4 py-2 text-sm font-medium text-error-600 hover:bg-error-50 dark:hover:bg-error-500/10" x-text="$t('settings.account.deleteAccount')"></button>
                    </div>
                </form>
            </div>

            {{-- Team --}}
            <div x-show="tab === 'team'" x-cloak>
                <ul class="divide-y divide-themed">
                    @foreach ($owners as $i => $o)
                        <li class="flex items-center gap-3 py-3">
                            <x-ui.avatar name="{{ $o }}" size="sm" />
                            <div class="flex-1">
                                <p class="text-sm font-medium text-heading">{{ $o }}</p>
                                <p class="text-xs text-muted">{{ str_replace(' ', '.', strtolower($o)) }}@@nimbuscrm.com</p>
                            </div>
                            <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-medium {{ $i === 0 ? 'bg-brand-50 text-brand-600 dark:bg-brand-500/15 dark:text-brand-400' : 'bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-300' }}"
                                x-text="$t('settings.team.{{ $i === 0 ? 'admin' : 'member' }}')"></span>
                        </li>
                    @endforeach
                </ul>
                <button type="button" @click="$store.ui.notifySuccess($t('settings.team.invite'))" class="mt-4 rounded-lg bg-brand-500 px-5 py-2.5 text-sm font-medium text-white hover:bg-brand-600" x-text="$t('settings.team.invite')"></button>
            </div>

            {{-- Pipeline --}}
            <div x-show="tab === 'pipeline'" x-cloak>
                <p class="mb-4 text-sm text-muted" x-text="$t('settings.pipeline.desc')"></p>
                <ul class="space-y-2">
                    @foreach ($stages as $s)
                        <li class="flex items-center gap-3 rounded-xl border border-themed p-3">
                            <span class="h-3 w-3 rounded-full" style="background-color: {{ $s['color'] }}"></span>
                            <span class="flex-1 text-sm font-medium text-heading" x-text="$t('stages.{{ $s['nameKey'] }}')"></span>
                            <span class="text-xs text-muted"><span x-text="$t('settings.pipeline.probability')"></span>: {{ $s['probability'] }}%</span>
                        </li>
                    @endforeach
                </ul>
            </div>

            {{-- Notifications --}}
            <div x-show="tab === 'notifications'" x-cloak x-data="{ notif: { email: true, push: false, deals: true, tasks: true } }">
                <div class="max-w-xl">
                    @foreach (['email', 'push', 'deals', 'tasks'] as $k)
                        <div class="flex items-center justify-between border-b border-themed py-4 last:border-0">
                            <div>
                                <p class="text-sm font-medium text-heading" x-text="$t('settings.notifications.{{ $k }}')"></p>
                                <p class="text-xs text-muted" x-text="$t('settings.notifications.{{ $k }}Desc')"></p>
                            </div>
                            <button type="button" @click="notif.{{ $k }} = !notif.{{ $k }}"
                                class="relative inline-flex h-6 w-11 shrink-0 items-center rounded-full transition"
                                :class="notif.{{ $k }} ? 'bg-brand-500' : 'bg-gray-300 dark:bg-gray-700'">
                                <span class="inline-block h-4 w-4 transform rounded-full bg-white transition" :class="notif.{{ $k }} ? 'translate-x-6' : 'translate-x-1'"></span>
                            </button>
                        </div>
                    @endforeach
                    <button type="button" @click="$store.ui.notifySuccess($t('settings.notifications.saved'))" class="mt-5 rounded-lg bg-brand-500 px-5 py-2.5 text-sm font-medium text-white hover:bg-brand-600" x-text="$t('common.save')"></button>
                </div>
            </div>
        </div>
    </div>
</x-layout.app>
