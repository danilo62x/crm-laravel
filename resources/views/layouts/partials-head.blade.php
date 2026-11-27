{{-- Shared <head> contents + window.CRM bootstrap for all layouts. --}}
<meta charset="UTF-8" />
<link rel="icon" type="image/svg+xml" href="/favicon.svg" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<meta name="csrf-token" content="{{ csrf_token() }}">
<link rel="manifest" href="/manifest.webmanifest" />
<meta name="theme-color" content="#465fff" />
<title>{{ isset($pageTitle) && $pageTitle ? $pageTitle . ' — ' : '' }}CRM — Vendas & Contatos</title>
@vite(['resources/css/app.css', 'resources/js/app.js'])
<script>
    // Canonical mock data shared with the Alpine stores (mirrors crm/react src/data + stores).
    window.CRM = {
        stages: @json(\App\Support\CrmData::stages()),
        owners: @json(\App\Support\CrmData::owners()),
        tagColors: @json(\App\Support\CrmData::tagColors()),
        contacts: @json(\App\Support\CrmData::contacts()),
        companies: @json(\App\Support\CrmData::companies()),
        deals: @json(\App\Support\CrmData::deals()),
        activities: @json(\App\Support\CrmData::activities()),
        notes: @json(\App\Support\CrmData::notes()),
        user: @json(session('user')),
    };
</script>
@stack('head')
