<footer class="border-t border-themed bg-card-bg surface-card" x-data>
    <div class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
        <div class="flex flex-col items-start justify-between gap-8 lg:flex-row">
            <div class="max-w-sm">
                <a href="{{ route('home') }}" class="flex items-center gap-2.5">
                    <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-brand-500 text-white">
                        <x-icon name="layers" :size="18" />
                    </span>
                    <span class="text-lg font-bold text-heading" x-text="$t('brand')"></span>
                </a>
                <p class="mt-4 text-sm text-muted" x-text="$t('landing.footer.tagline')"></p>
            </div>
            <div class="flex flex-wrap gap-x-10 gap-y-2 text-sm">
                <a href="{{ route('contact') }}" class="text-muted transition hover:text-brand-600" x-text="$t('nav.contact')"></a>
                <a href="{{ route('login') }}" class="text-muted transition hover:text-brand-600" x-text="$t('nav.login')"></a>
                <a href="{{ route('register') }}" class="text-muted transition hover:text-brand-600" x-text="$t('nav.getStarted')"></a>
                <a href="#" class="text-muted transition hover:text-brand-600" x-text="$t('landing.footer.privacy')"></a>
            </div>
        </div>
        <div class="mt-10 flex flex-col items-center justify-between gap-4 border-t border-themed pt-6 sm:flex-row">
            <p class="text-xs text-muted">© {{ date('Y') }} <span x-text="$t('brand')"></span>. <span x-text="$t('landing.footer.rights')"></span></p>
            <div class="flex items-center gap-3">
                @foreach (['github', 'slack', 'globe'] as $icon)
                    <a href="#" aria-label="{{ $icon }}"
                        class="flex h-9 w-9 items-center justify-center rounded-full border border-themed text-muted transition hover:text-brand-500">
                        <x-icon name="{{ $icon }}" :size="16" />
                    </a>
                @endforeach
            </div>
        </div>
    </div>
</footer>
