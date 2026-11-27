<x-layout.blank :pageTitle="$pageTitle ?? null">
    <div class="flex min-h-screen flex-col items-center justify-center px-4 text-center" style="background-color: var(--tx-body-bg)" x-data>
        <p class="text-7xl font-bold text-brand-500 sm:text-8xl">404</p>
        <h1 class="mt-4 text-2xl font-bold text-heading" x-text="$t('notFound.title')"></h1>
        <p class="mt-2 max-w-md text-muted" x-text="$t('notFound.desc')"></p>
        <div class="mt-8 flex gap-3">
            <a href="{{ route('home') }}" class="flex h-11 items-center rounded-lg border border-themed px-5 text-sm font-medium text-body transition hover:bg-gray-50 dark:hover:bg-gray-800" x-text="$t('notFound.home')"></a>
            <template x-if="$store.auth.isAuthenticated">
                <a href="{{ route('dashboard') }}" class="flex h-11 items-center rounded-lg bg-brand-500 px-5 text-sm font-medium text-white transition hover:bg-brand-600" x-text="$t('notFound.dashboard')"></a>
            </template>
        </div>
    </div>
</x-layout.blank>
