@props([
    'items' => 'items',
    'toggle' => 'true',
])
{{--
    Activity timeline rail. `items` is an Alpine expression yielding an array of
    activities (default "items"); `toggle` (bool expr) shows the complete/undo
    button. Mirrors crm/react ActivityTimeline.
--}}
<ol class="relative space-y-5 before:absolute before:left-[18px] before:top-2 before:bottom-2 before:w-px before:bg-[var(--tx-border)]">
    <template x-for="a in {{ $items }}" :key="a.id">
        <li class="relative flex gap-4">
            <span class="relative z-10 flex h-9 w-9 shrink-0 items-center justify-center rounded-full"
                :class="({
                    call: 'text-brand-500 bg-brand-50 dark:bg-brand-500/15',
                    email: 'text-blue-light-500 bg-blue-light-50 dark:bg-blue-light-500/15',
                    meeting: 'text-warning-600 bg-warning-50 dark:bg-warning-500/15',
                    task: 'text-success-600 bg-success-50 dark:bg-success-500/15',
                })[a.type]">
                <template x-if="a.type === 'call'"><x-icon name="phone" :size="16" /></template>
                <template x-if="a.type === 'email'"><x-icon name="mail" :size="16" /></template>
                <template x-if="a.type === 'meeting'"><x-icon name="users" :size="16" /></template>
                <template x-if="a.type === 'task'"><x-icon name="check-circle" :size="16" /></template>
            </span>
            <div class="flex-1 pt-0.5">
                <div class="flex items-start justify-between gap-3">
                    <p class="text-sm font-medium" :class="a.status === 'done' ? 'text-muted line-through' : 'text-heading'" x-text="a.title"></p>
                    @if ($toggle !== 'false')
                        <button type="button" @click="$store.crm.toggleActivity(a.id); $store.ui.notifySuccess($t('activities.updated'))"
                            class="shrink-0 rounded-md border px-2 py-0.5 text-xs transition"
                            :class="a.status === 'done'
                                ? 'border-success-200 text-success-600 dark:border-success-500/30'
                                : 'border-themed text-muted hover:border-brand-300 hover:text-brand-600'"
                            x-text="a.status === 'done' ? $t('activities.done') : $t('activities.markDone')"></button>
                    @endif
                </div>
                <p class="mt-0.5 text-xs text-muted">
                    <span x-text="$t('activities.types.' + a.type)"></span> · <span x-text="a.owner"></span> · <span x-text="$fromNow(a.date)"></span>
                </p>
            </div>
        </li>
    </template>
</ol>
