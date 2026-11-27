@props([
    'stage' => null,
    'size' => 'md',
])
{{--
    Colored pill for a deal stage. `stage` is an Alpine expression that evaluates
    to a stage id (e.g. "deal.stage" or "'lead'"). Resolves color/label via
    window.crmHelpers + $t reactively. Mirrors crm/react StagePill.
--}}
@php($pad = $size === 'sm' ? 'px-2 py-0.5 text-[11px]' : 'px-2.5 py-1 text-xs')
<span x-data="{ get s() { return window.crmHelpers.getStage({{ $stage }}) || {}; } }"
    class="inline-flex items-center gap-1.5 rounded-full font-medium {{ $pad }}"
    :style="`background-color: ${s.color}1f; color: ${s.color}`">
    <span class="h-1.5 w-1.5 rounded-full" :style="`background-color: ${s.color}`"></span>
    <span x-text="$t('stages.' + s.nameKey)"></span>
</span>
