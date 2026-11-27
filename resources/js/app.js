import Alpine from 'alpinejs';
import collapse from '@alpinejs/collapse';
import i18n, { LANGUAGES, formatCurrency, formatCompact, formatDate, fromNow } from './i18n.js';
import './pwa.js';

window.Alpine = Alpine;

// ─── Magics do Alpine expostos como globais ─────────────────────────────────
// As fábricas de página (dashboardPage(), ordersPage(), …) são funções
// declaradas num <script>, fora do escopo de expressão do Alpine — que é onde
// o with(scope) coloca os magics. O Alpine injeta os magics no OBJETO DE DADOS
// (injectMagics), não no window, então um `$store` ou `$t` nu dentro dessas
// funções seria ReferenceError. Espelhamos os magics em window para que elas
// continuem legíveis. Alpine.store() devolve o mesmo proxy reativo do magic,
// então os getters que leem daqui continuam reativos.
window.$store = new Proxy(
  {},
  { get: (_target, name) => (typeof name === 'string' ? Alpine.store(name) : undefined) }
);
const registerMagic = Alpine.magic.bind(Alpine);
Alpine.magic = (name, callback) => {
  registerMagic(name, callback);
  Object.defineProperty(window, `$${name}`, { configurable: true, get: () => callback() });
};

Alpine.plugin(collapse);

// ─── localStorage helpers (mirror zustand persist) ──────────────────────────
function load(key, fallback) {
  try {
    const raw = localStorage.getItem(key);
    if (!raw) return fallback;
    const parsed = JSON.parse(raw);
    // zustand persist wraps state in { state, version }
    return parsed && parsed.state ? parsed.state : parsed;
  } catch (_) {
    return fallback;
  }
}
function save(key, state) {
  try {
    localStorage.setItem(key, JSON.stringify({ state, version: 0 }));
  } catch (_) {}
}

const CRM = window.CRM || {};
const STAGES = () => CRM.stages || [];
const getStage = (id) => STAGES().find((s) => s.id === id) || STAGES()[0] || null;

// ─── i18n magics ─────────────────────────────────────────────────────────────
// Touch the reactive `ui.lang` inside each magic so x-text/x-bind re-run when
// the language changes (keeps the whole UI reactive to language switching).
Alpine.magic('t', () => (key, params) => {
  if (Alpine.store('ui')) Alpine.store('ui').lang;
  return i18n.t(key, params);
});
Alpine.magic('currency', () => (value) => {
  if (Alpine.store('ui')) Alpine.store('ui').lang;
  return formatCurrency(value, i18n.lang);
});
Alpine.magic('compact', () => (value) => {
  if (Alpine.store('ui')) Alpine.store('ui').lang;
  return formatCompact(value, i18n.lang);
});
Alpine.magic('date', () => (iso) => {
  if (Alpine.store('ui')) Alpine.store('ui').lang;
  return formatDate(iso, i18n.lang);
});
Alpine.magic('fromNow', () => (iso) => {
  if (Alpine.store('ui')) Alpine.store('ui').lang;
  return fromNow(iso, i18n.lang);
});
Alpine.magic('tArray', () => (key) => {
  if (Alpine.store('ui')) Alpine.store('ui').lang;
  return i18n.tArray(key);
});
Alpine.magic('lang', () => {
  if (Alpine.store('ui')) Alpine.store('ui').lang;
  return i18n.lang;
});

// ─── UI store — key: crm-ui (theme, sidebar, toasts, cookies) ────────────────
let toastId = 0;
const savedUi = load('crm-ui', {});
Alpine.store('ui', {
  darkMode: savedUi.darkMode ?? false,
  sidebarCollapsed: savedUi.sidebarCollapsed ?? false,
  sidebarMobileOpen: false,
  toasts: [],
  cookiesAccepted: savedUi.cookiesAccepted ?? false,
  showCookieBanner: false,
  lang: i18n.lang,

  init() {
    this.applyTheme();
    Alpine.effect(() => {
      this.darkMode;
      this.applyTheme();
      save('crm-ui', {
        darkMode: this.darkMode,
        sidebarCollapsed: this.sidebarCollapsed,
        cookiesAccepted: this.cookiesAccepted,
      });
    });
    document.addEventListener('i18n:changed', (e) => {
      this.lang = e.detail.lang;
    });
    // Surface the cookie banner on first visit (no choice persisted yet).
    if (!this.cookiesAccepted) {
      setTimeout(() => this.openCookieBanner(), 800);
    }
  },
  applyTheme() {
    const root = document.documentElement;
    if (this.darkMode) root.classList.add('dark');
    else root.classList.remove('dark');
  },
  toggleDarkMode() {
    this.darkMode = !this.darkMode;
  },
  setDarkMode(v) {
    this.darkMode = v;
  },
  toggleSidebar() {
    this.sidebarCollapsed = !this.sidebarCollapsed;
  },
  toggleSidebarMobile(v) {
    this.sidebarMobileOpen = v !== undefined ? v : !this.sidebarMobileOpen;
  },
  setLang(lang) {
    i18n.setLang(lang);
    this.lang = lang;
  },
  get languages() {
    return LANGUAGES;
  },
  get currentLanguage() {
    return LANGUAGES.find((l) => l.code === this.lang) || LANGUAGES[0];
  },

  notify(message, type = 'info', duration = 4000) {
    const id = ++toastId;
    this.toasts.push({ id, message, type, duration });
    setTimeout(() => this.dismissToast(id), duration);
  },
  notifySuccess(m) {
    this.notify(m, 'success');
  },
  notifyError(m) {
    this.notify(m, 'error');
  },
  notifyWarning(m) {
    this.notify(m, 'warning');
  },
  dismissToast(id) {
    const i = this.toasts.findIndex((t) => t.id === id);
    if (i !== -1) this.toasts.splice(i, 1);
  },
  acceptCookies() {
    this.cookiesAccepted = true;
    this.showCookieBanner = false;
  },
  declineCookies() {
    this.cookiesAccepted = false;
    this.showCookieBanner = false;
  },
  openCookieBanner() {
    this.showCookieBanner = true;
  },
  closeCookieBanner() {
    this.showCookieBanner = false;
  },
});

// ─── Auth store — key: crm-auth ──────────────────────────────────────────────
Alpine.store('auth', {
  user: load('crm-auth', { user: null }).user ?? null,

  init() {
    // Sync from server session (set on the page) on first load if present.
    if (CRM.user && !this.user) {
      this.user = CRM.user;
    }
    Alpine.effect(() => {
      this.user;
      save('crm-auth', { user: this.user });
    });
  },
  get isAuthenticated() {
    return this.user !== null;
  },
  setUser(user) {
    this.user = user;
  },
  logout() {
    this.user = null;
  },
  updateProfile(patch) {
    if (!this.user) return;
    this.user = { ...this.user, ...patch };
  },
});

// ─── CRM data store — key: crm-data ──────────────────────────────────────────
// Mirrors crm/react stores/crm.ts: contacts / companies / deals / activities /
// notes with the same add/update/remove/move/toggle logic and id sequence.
let seq = 1000;
const nextId = () => ++seq;
const PEOPLE = ['1500648767791-00dcc994a43e', '1494790108377-be9c29b29330', '1507003211169-0a1dd7228f2d', '1438761681033-6461ffad8d80', '1633332755192-727a05c4013d', '1573496359142-b8d87734a5a2'];
function imgHash(s) { s = String(s); let h = 0; for (let i = 0; i < s.length; i++) h = (h * 31 + s.charCodeAt(i)) >>> 0; return h; }
function unsplash(id, w, h) { return `https://images.unsplash.com/photo-${id}?w=${w}&h=${h}&fit=crop&q=70&auto=format`; }
const av = (id) => unsplash(PEOPLE[imgHash('person-' + id) % PEOPLE.length], 96, 96);
const today = () => new Date().toISOString().slice(0, 10);

const savedData = load('crm-data', {});
Alpine.store('crm', {
  contacts: savedData.contacts ?? (CRM.contacts || []),
  companies: savedData.companies ?? (CRM.companies || []),
  deals: savedData.deals ?? (CRM.deals || []),
  activities: savedData.activities ?? (CRM.activities || []),
  notes: savedData.notes ?? (CRM.notes || []),

  init() {
    Alpine.effect(() => {
      this.contacts.length;
      this.companies.length;
      this.deals.length;
      this.activities.length;
      this.notes.length;
      save('crm-data', {
        contacts: this.contacts,
        companies: this.companies,
        deals: this.deals,
        activities: this.activities,
        notes: this.notes,
      });
    });
  },

  // ── Contacts ──
  addContact(c) {
    const id = nextId();
    const contact = { ...c, id, avatar: av(id), createdAt: today() };
    this.contacts.unshift(contact);
    return contact;
  },
  updateContact(id, patch) {
    const c = this.contacts.find((x) => x.id === id);
    if (c) Object.assign(c, patch);
  },
  removeContact(id) {
    const i = this.contacts.findIndex((c) => c.id === id);
    if (i !== -1) this.contacts.splice(i, 1);
  },

  // ── Companies ──
  addCompany(c) {
    const id = nextId();
    const company = { ...c, id, createdAt: today() };
    this.companies.unshift(company);
    return company;
  },
  updateCompany(id, patch) {
    const c = this.companies.find((x) => x.id === id);
    if (c) Object.assign(c, patch);
  },
  removeCompany(id) {
    const i = this.companies.findIndex((c) => c.id === id);
    if (i !== -1) this.companies.splice(i, 1);
  },

  // ── Deals ──
  addDeal(d) {
    const id = nextId();
    const deal = { ...d, id, createdAt: today() };
    this.deals.unshift(deal);
    return deal;
  },
  updateDeal(id, patch) {
    const d = this.deals.find((x) => x.id === id);
    if (d) Object.assign(d, patch);
  },
  removeDeal(id) {
    const i = this.deals.findIndex((d) => d.id === id);
    if (i !== -1) this.deals.splice(i, 1);
  },
  /** Kanban: move a deal to a new stage, syncing the suggested probability. */
  moveDealStage(id, stage) {
    const d = this.deals.find((x) => x.id === id);
    if (d) {
      d.stage = stage;
      const s = getStage(stage);
      if (s) d.probability = s.probability;
    }
  },

  // ── Activities / tasks ──
  addActivity(a) {
    const id = nextId();
    const activity = { ...a, id };
    this.activities.unshift(activity);
    return activity;
  },
  updateActivity(id, patch) {
    const a = this.activities.find((x) => x.id === id);
    if (a) Object.assign(a, patch);
  },
  toggleActivity(id) {
    const a = this.activities.find((x) => x.id === id);
    if (a) a.status = a.status === 'done' ? 'open' : 'done';
  },
  removeActivity(id) {
    const i = this.activities.findIndex((a) => a.id === id);
    if (i !== -1) this.activities.splice(i, 1);
  },

  // ── Notes ──
  addNote(n) {
    const id = nextId();
    const note = { ...n, id, createdAt: new Date().toISOString() };
    this.notes.unshift(note);
    return note;
  },

  // ── Lookups ──
  contactName(id) {
    if (id == null) return '—';
    return this.contacts.find((c) => c.id === id)?.name ?? '—';
  },
  companyName(id) {
    if (id == null) return '—';
    return this.companies.find((c) => c.id === id)?.name ?? '—';
  },
});

// ─── Shared helpers exposed to Alpine x-data components ──────────────────────
window.crmHelpers = {
  getStage,
  stages: STAGES,
  owners: () => CRM.owners || [],
  tagColors: () => CRM.tagColors || {},
  formatCurrency: (v) => formatCurrency(v, i18n.lang),
  formatCompact: (v) => formatCompact(v, i18n.lang),
  formatDate: (iso) => formatDate(iso, i18n.lang),
  fromNow: (iso) => fromNow(iso, i18n.lang),
};

// Apply static DOM translations once Alpine is up (for any [data-i18n] nodes).
document.addEventListener('alpine:initialized', () => i18n.applyDom(document));

Alpine.start();
