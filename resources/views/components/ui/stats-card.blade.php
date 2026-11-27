@props([
    'titleKey' => '',
    'value' => '',
    'icon' => 'dollar',
    'accent' => 'brand',
    'trend' => null,
    'trendValue' => null,
])
@php($bg = [
    'brand' => 'bg-brand-50 dark:bg-brand-500/15',
    'success' => 'bg-success-50 dark:bg-success-500/15',
    'warning' => 'bg-warning-50 dark:bg-warning-500/15',
    'error' => 'bg-error-50 dark:bg-error-500/15',
    'info' => 'bg-blue-light-50 dark:bg-blue-light-500/15',
][$accent] ?? 'bg-brand-50 dark:bg-brand-500/15')
@php($color = [
    'brand' => 'text-brand-500',
    'success' => 'text-success-600',
    'warning' => 'text-warning-600',
    'error' => 'text-error-500',
    'info' => 'text-blue-light-500',
][$accent] ?? 'text-brand-500')
<div class="rounded-2xl border border-themed surface-card p-5 shadow-theme-sm md:p-6">
    <div class="flex h-12 w-12 items-center justify-center rounded-xl {{ $bg }}">
        <x-icon name="{{ $icon }}" :size="22" class="{{ $color }}" />
    </div>
    <div class="mt-5 flex items-end justify-between">
        <div>
            <span class="text-sm text-muted" x-text="$t('{{ $titleKey }}')"></span>
            <h4 class="mt-2 font-bold text-heading text-title-sm" x-text="{{ $value }}"></h4>
        </div>
        @if ($trend)
            <span class="inline-flex items-center gap-1 rounded-full px-2 py-0.5 text-xs font-medium {{ $trend === 'up' ? 'bg-success-50 text-success-600 dark:bg-success-500/15 dark:text-success-500' : 'bg-error-50 text-error-600 dark:bg-error-500/15 dark:text-error-500' }}">
                <x-icon name="{{ $trend === 'up' ? 'arrow-up' : 'arrow-down' }}" :size="12" :strokeWidth="2.5" />
                {{ $trendValue }}
            </span>
        @endif
    </div>
</div>
