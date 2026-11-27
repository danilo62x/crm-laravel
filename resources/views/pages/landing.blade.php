<x-layout.marketing :pageTitle="$pageTitle ?? null">
    @php($features = [
        ['icon' => 'layers', 'key' => 'pipeline'],
        ['icon' => 'users', 'key' => 'contacts'],
        ['icon' => 'activity', 'key' => 'activities'],
        ['icon' => 'bar-chart', 'key' => 'reports'],
        ['icon' => 'zap', 'key' => 'automation'],
        ['icon' => 'shield-check', 'key' => 'security'],
    ])
    @php($steps = ['s1', 's2', 's3'])
    @php($faq = ['q1', 'q2', 'q3'])
    @php($stats = [
        ['value' => '38%', 'key' => 'conversion'],
        ['value' => '2.4x', 'key' => 'velocity'],
        ['value' => '12k+', 'key' => 'teams'],
        ['value' => '99.9%', 'key' => 'uptime'],
    ])

    {{-- Hero --}}
    <section class="relative overflow-hidden" x-data>
        <div class="absolute inset-0 -z-10">
            <div class="absolute -top-40 left-1/2 h-[28rem] w-[28rem] -translate-x-1/2 rounded-full bg-brand-200/40 blur-3xl dark:bg-brand-500/10"></div>
        </div>
        <div class="mx-auto max-w-7xl px-4 pb-16 pt-16 sm:px-6 lg:px-8 lg:pt-24">
            <div class="mx-auto max-w-3xl text-center">
                <span class="inline-flex items-center gap-2 rounded-full border border-themed bg-card-bg px-3 py-1 text-xs font-medium text-brand-600 surface-card">
                    <span class="h-1.5 w-1.5 rounded-full bg-brand-500"></span>
                    <span x-text="$t('landing.hero.badge')"></span>
                </span>
                <h1 class="mt-6 text-4xl font-bold tracking-tight text-heading sm:text-5xl lg:text-6xl" x-text="$t('landing.hero.title')"></h1>
                <p class="mx-auto mt-5 max-w-2xl text-lg text-muted" x-text="$t('landing.hero.subtitle')"></p>
                <div class="mt-8 flex flex-col items-center justify-center gap-3 sm:flex-row">
                    <a href="{{ route('login') }}" class="flex h-12 items-center justify-center gap-2 rounded-lg bg-brand-500 px-6 text-sm font-semibold text-white shadow-theme-md transition hover:bg-brand-600">
                        <span x-text="$t('landing.hero.ctaPrimary')"></span>
                        <x-icon name="arrow-right" :size="18" />
                    </a>
                    <a href="{{ route('contact') }}" class="flex h-12 items-center justify-center rounded-lg border border-themed bg-card-bg px-6 text-sm font-semibold text-body transition hover:bg-gray-50 surface-card dark:hover:bg-gray-800" x-text="$t('landing.hero.ctaSecondary')"></a>
                </div>
                <p class="mt-4 text-xs text-muted" x-text="$t('landing.hero.noCard')"></p>
            </div>

            {{-- Stats band --}}
            <div class="mt-16 grid grid-cols-2 gap-4 sm:grid-cols-4">
                @foreach ($stats as $s)
                    <div class="surface-card rounded-2xl border p-5 text-center">
                        <p class="text-2xl font-bold text-heading sm:text-3xl">{{ $s['value'] }}</p>
                        <p class="mt-1 text-xs text-muted" x-text="$t('landing.stats.{{ $s['key'] }}')"></p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Features --}}
    <section id="features" class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8" x-data>
        <div class="mx-auto max-w-2xl text-center">
            <h2 class="text-3xl font-bold text-heading" x-text="$t('landing.features.title')"></h2>
            <p class="mt-3 text-muted" x-text="$t('landing.features.subtitle')"></p>
        </div>
        <div class="mt-12 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($features as $f)
                <div class="surface-card rounded-2xl border p-6 transition hover:shadow-theme-md">
                    <span class="flex h-12 w-12 items-center justify-center rounded-xl bg-brand-50 text-brand-500 dark:bg-brand-500/15">
                        <x-icon name="{{ $f['icon'] }}" :size="24" />
                    </span>
                    <h3 class="mt-5 text-lg font-semibold text-heading" x-text="$t('landing.features.items.{{ $f['key'] }}.title')"></h3>
                    <p class="mt-2 text-sm text-muted" x-text="$t('landing.features.items.{{ $f['key'] }}.desc')"></p>
                </div>
            @endforeach
        </div>
    </section>

    {{-- How it works --}}
    <section id="how" class="border-y border-themed bg-card-bg surface-card" x-data>
        <div class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
            <div class="mx-auto max-w-2xl text-center">
                <h2 class="text-3xl font-bold text-heading" x-text="$t('landing.how.title')"></h2>
                <p class="mt-3 text-muted" x-text="$t('landing.how.subtitle')"></p>
            </div>
            <div class="mt-12 grid gap-8 md:grid-cols-3">
                @foreach ($steps as $i => $s)
                    <div class="text-center">
                        <span class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-brand-500 text-lg font-bold text-white">{{ $i + 1 }}</span>
                        <h3 class="mt-5 text-lg font-semibold text-heading" x-text="$t('landing.how.steps.{{ $s }}.title')"></h3>
                        <p class="mt-2 text-sm text-muted" x-text="$t('landing.how.steps.{{ $s }}.desc')"></p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- FAQ --}}
    <section id="faq" class="mx-auto max-w-3xl px-4 py-16 sm:px-6 lg:px-8" x-data>
        <h2 class="text-center text-3xl font-bold text-heading" x-text="$t('landing.faqTitle')"></h2>
        <div class="mt-10 space-y-4">
            @foreach ($faq as $q)
                <div class="surface-card rounded-2xl border p-6">
                    <h3 class="text-base font-semibold text-heading" x-text="$t('landing.faq.{{ $q }}')"></h3>
                    <p class="mt-2 text-sm text-muted" x-text="$t('landing.faq.{{ str_replace('q', 'a', $q) }}')"></p>
                </div>
            @endforeach
        </div>
    </section>

    {{-- CTA --}}
    <section class="mx-auto max-w-7xl px-4 pb-20 sm:px-6 lg:px-8" x-data>
        <div class="overflow-hidden rounded-3xl bg-brand-500 px-6 py-14 text-center">
            <h2 class="text-3xl font-bold text-white" x-text="$t('landing.cta.title')"></h2>
            <p class="mx-auto mt-3 max-w-xl text-brand-50" x-text="$t('landing.cta.subtitle')"></p>
            <a href="{{ route('register') }}" class="mt-8 inline-flex h-12 items-center justify-center rounded-lg bg-white px-6 text-sm font-semibold text-brand-600 transition hover:bg-brand-50" x-text="$t('landing.cta.button')"></a>
        </div>
    </section>
</x-layout.marketing>
