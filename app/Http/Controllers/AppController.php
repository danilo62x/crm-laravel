<?php

namespace App\Http\Controllers;

use App\Support\CrmData;
use Illuminate\View\View;

class AppController extends Controller
{
    public function dashboard(): View
    {
        return view('pages.app.dashboard', ['pageTitle' => 'Painel']);
    }

    public function pipeline(): View
    {
        return view('pages.app.pipeline', ['pageTitle' => 'Pipeline']);
    }

    public function contacts(): View
    {
        return view('pages.app.contacts', ['pageTitle' => 'Contatos']);
    }

    public function contactDetail(int $id): View
    {
        $contact = collect(CrmData::contacts())->firstWhere('id', $id);

        return view('pages.app.contact-detail', [
            'pageTitle' => $contact['name'] ?? 'Contato',
            'contactId' => $id,
            'contact' => $contact,
        ]);
    }

    public function companies(): View
    {
        return view('pages.app.companies', ['pageTitle' => 'Empresas']);
    }

    public function deals(): View
    {
        return view('pages.app.deals', ['pageTitle' => 'Negócios']);
    }

    public function activities(): View
    {
        return view('pages.app.activities', ['pageTitle' => 'Atividades']);
    }

    public function reports(): View
    {
        return view('pages.app.reports', ['pageTitle' => 'Relatórios']);
    }

    public function settings(): View
    {
        return view('pages.app.settings', ['pageTitle' => 'Configurações']);
    }
}
