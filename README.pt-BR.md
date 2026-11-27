# CRM / Laravel

[Read in English](./README.md)

[![License: MIT](https://img.shields.io/badge/License-MIT-blue.svg)](./LICENSE) ![Free](https://img.shields.io/badge/price-free-brightgreen)

CRM completo em Laravel 11 + Tailwind: dashboard com KPIs e gráficos, pipeline Kanban de negócios (drag-and-drop), contatos, empresas, atividades/tarefas, relatórios e configurações. i18n pt-BR/en/es, PWA, dark mode.

## Destaques

- Dashboard com KPIs e gráficos (funil, receita, atividades)
- Pipeline Kanban de negócios com drag-and-drop
- Contatos e empresas (tabela, filtros, detalhe com timeline)
- Atividades/tarefas + relatórios
- i18n pt-BR/en/es, PWA, dark mode

## Telas em destaque

- Dashboard: KPIs, funil, receita, atividades
- Pipeline: Kanban de negócios (drag-and-drop)
- Contatos: Tabela, filtros, detalhe
- Relatórios: Conversão e desempenho

## Stack

- Laravel 11
- PHP 8.2+
- Blade
- Alpine.js
- Tailwind CSS v4
- ApexCharts
- Vite

## Requisitos

- PHP 8.2 ou mais novo, com Composer
- Node.js 20 ou mais novo, com npm

## Como rodar

```bash
composer install
cp .env.example .env
php artisan key:generate
npm install
npm run build
php artisan serve
```

Não precisa de banco de dados: os dados de exemplo ficam em classes PHP e o login é simulado por middleware.

Durante o desenvolvimento, use `npm run dev` no lugar de `npm run build` para ter hot reload dos assets.

Conta de demonstração:

- `admin@crm.com` / `demo123`

## Estrutura do projeto

```
app/
├── Http/  Controllers, Middleware
├── Models/
├── Providers/
├── Support/
resources/  css/, js/, views/
routes/  console.php, web.php
```

## Outras tecnologias

Este template também existe em outras 4 tecnologias, com as mesmas telas e a mesma identidade visual. Todas são gratuitas:

- React 19: https://github.com/danilo62x/crm-react
- Vue 3: https://github.com/danilo62x/crm-vue3
- Angular 19: https://github.com/danilo62x/crm-angular
- HTML estático: https://github.com/danilo62x/crm-html

Mais templates gratuitos (painéis admin, sistemas por segmento e apps Flutter): https://github.com/danilo62x?tab=repositories

## Licença

[MIT](./LICENSE), copyright 2026 Danilo Quinelato.
