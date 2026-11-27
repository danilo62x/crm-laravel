<x-layout.marketing :pageTitle="$pageTitle ?? null">
    @php($info = [
        ['key' => 'email', 'icon' => 'mail', 'value' => 'vendas@nimbuscrm.com'],
        ['key' => 'phone', 'icon' => 'phone', 'value' => '+55 11 4000-1234'],
        ['key' => 'address', 'icon' => 'map-pin', 'value' => ''],
    ])
    <div class="mx-auto max-w-6xl px-4 py-16 sm:px-6 lg:px-8" x-data>
        <div class="mx-auto max-w-2xl text-center">
            <h1 class="text-3xl font-bold text-heading sm:text-4xl" x-text="$t('contact.title')"></h1>
            <p class="mx-auto mt-3 text-muted" x-text="$t('contact.subtitle')"></p>
        </div>

        <div class="mt-12 grid gap-8 lg:grid-cols-[1fr_1.4fr]">
            <div class="space-y-4">
                @foreach ($info as $i)
                    <div class="surface-card flex items-start gap-4 rounded-2xl border p-5">
                        <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-brand-50 text-brand-500 dark:bg-brand-500/15">
                            <x-icon name="{{ $i['icon'] }}" :size="20" />
                        </span>
                        <div>
                            <p class="text-sm font-medium text-heading" x-text="$t('contact.info.{{ $i['key'] }}')"></p>
                            <p class="mt-0.5 text-sm text-muted">
                                @if ($i['key'] === 'address')
                                    <span x-text="$t('contact.info.addressValue')"></span>
                                @else
                                    {{ $i['value'] }}
                                @endif
                            </p>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="surface-card rounded-2xl border p-6 md:p-8"
                x-data="{
                    form: { name: '', email: '', subject: '', message: '' },
                    errors: {},
                    submitting: false,
                    validate() {
                        const e = {};
                        if (!this.form.name.trim()) e.name = $t('contact.form.errors.name');
                        if (!/^[^@\s]+@[^@\s]+\.[^@\s]+$/.test(this.form.email)) e.email = $t('contact.form.errors.email');
                        if (!this.form.subject.trim()) e.subject = $t('contact.form.errors.subject');
                        if (this.form.message.trim().length < 10) e.message = $t('contact.form.errors.message');
                        this.errors = e;
                        return Object.keys(e).length === 0;
                    },
                    submit() {
                        if (!this.validate()) return;
                        this.submitting = true;
                        setTimeout(() => {
                            $store.ui.notifySuccess($t('contact.form.success'));
                            this.form = { name: '', email: '', subject: '', message: '' };
                            this.errors = {};
                            this.submitting = false;
                        }, 600);
                    }
                }"
                @submit.prevent="submit()">
                <form class="space-y-5">
                    <div class="grid gap-5 sm:grid-cols-2">
                        <div class="space-y-1.5">
                            <label class="block text-sm font-medium text-heading" x-text="$t('contact.form.name')"></label>
                            <input x-model="form.name" placeholder="Maria Silva" class="focus-ring w-full rounded-lg border bg-transparent px-3.5 py-2.5 text-sm text-body" :class="errors.name ? 'border-error-500' : 'border-themed'" />
                            <p x-show="errors.name" x-cloak class="text-xs text-error-500" x-text="errors.name"></p>
                        </div>
                        <div class="space-y-1.5">
                            <label class="block text-sm font-medium text-heading" x-text="$t('contact.form.email')"></label>
                            <input type="email" x-model="form.email" placeholder="seu@email.com" class="focus-ring w-full rounded-lg border bg-transparent px-3.5 py-2.5 text-sm text-body" :class="errors.email ? 'border-error-500' : 'border-themed'" />
                            <p x-show="errors.email" x-cloak class="text-xs text-error-500" x-text="errors.email"></p>
                        </div>
                    </div>
                    <div class="space-y-1.5">
                        <label class="block text-sm font-medium text-heading" x-text="$t('contact.form.subject')"></label>
                        <input x-model="form.subject" class="focus-ring w-full rounded-lg border bg-transparent px-3.5 py-2.5 text-sm text-body" :class="errors.subject ? 'border-error-500' : 'border-themed'" />
                        <p x-show="errors.subject" x-cloak class="text-xs text-error-500" x-text="errors.subject"></p>
                    </div>
                    <div class="space-y-1.5">
                        <label class="block text-sm font-medium text-heading" x-text="$t('contact.form.message')"></label>
                        <textarea rows="5" x-model="form.message" class="focus-ring w-full rounded-lg border bg-transparent px-3.5 py-2.5 text-sm text-body" :class="errors.message ? 'border-error-500' : 'border-themed'"></textarea>
                        <p x-show="errors.message" x-cloak class="text-xs text-error-500" x-text="errors.message"></p>
                    </div>
                    <button type="submit" :disabled="submitting"
                        class="flex h-11 w-full items-center justify-center rounded-lg bg-brand-500 text-sm font-medium text-white transition hover:bg-brand-600 disabled:opacity-60"
                        x-text="submitting ? $t('contact.form.sending') : $t('contact.form.send')"></button>
                </form>
            </div>
        </div>
    </div>
</x-layout.marketing>
