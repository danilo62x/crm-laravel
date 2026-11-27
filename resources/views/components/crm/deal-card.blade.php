{{--
    Draggable Kanban deal card. Used inside an x-for over a stage's deals, where
    `deal` is in scope. Native HTML5 drag-and-drop is wired by the PipelineBoard
    via @dragstart/@dragend on this element. Mirrors crm/react DealCard.
--}}
<div
    draggable="true"
    @dragstart="onDragStart($event, deal.id)"
    @dragend="onDragEnd()"
    @click="openDeal(deal)"
    class="surface-card cursor-grab rounded-xl border p-3 shadow-theme-xs transition active:cursor-grabbing hover:shadow-theme-md"
    :class="dragId === deal.id ? 'opacity-40' : ''">
    <div class="flex items-start justify-between gap-2">
        <p class="text-sm font-semibold text-heading" x-text="deal.title"></p>
        <x-icon name="dots-vertical" :size="16" class="text-gray-400" />
    </div>
    <p class="mt-1 text-xs text-muted" x-text="$store.crm.companyName(deal.companyId)"></p>

    <p class="mt-3 text-base font-bold text-brand-600 dark:text-brand-400" x-text="$currency(deal.value)"></p>

    <div class="mt-3 flex items-center justify-between border-t border-themed pt-3">
        <div class="flex items-center gap-2">
            <span class="flex h-7 w-7 items-center justify-center rounded-full bg-brand-500 text-[10px] font-bold text-white"
                x-text="$store.crm.contactName(deal.contactId).split(' ').slice(0,2).map(w => (w[0]||'').toUpperCase()).join('') || '?'"></span>
            <span class="text-xs text-muted" x-text="deal.probability + '%'"></span>
        </div>
        <span class="flex items-center gap-1 text-xs text-muted">
            <x-icon name="clock" :size="13" />
            <span x-text="$date(deal.expectedClose)"></span>
        </span>
    </div>
</div>
