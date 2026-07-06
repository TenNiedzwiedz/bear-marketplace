<script setup>
import { computed } from 'vue';
import { usePage, Link } from '@inertiajs/vue3';
import SiteHeader from './SiteHeader.vue';
import SiteFooter from './SiteFooter.vue';

/**
 * AdminLayout — shared chrome for the moderation panel (/admin). A staff-toned
 * sidebar (cranberry accent) with section nav and a live badge of pending
 * reports, plus a content slot. Mirrors PanelLayout for the user area.
 */
defineProps({
    pending: { type: Number, default: 0 },
    title: { type: String, default: null },
});

const page = usePage();
const path = computed(() => page.url.split('?')[0]);

const nav = [
    { path: '/admin', label: 'Przegląd' },
    { path: '/admin/zgloszenia', label: 'Zgłoszenia', badge: true },
    { path: '/admin/ogloszenia', label: 'Ogłoszenia' },
    { path: '/admin/uzytkownicy', label: 'Użytkownicy' },
];
</script>

<template>
    <SiteHeader />

    <main class="admin">
        <div class="admin__grid">
            <aside class="side">
                <div class="side__card">
                    <p class="side__eyebrow">Strefa moderacji</p>
                    <p class="side__title">Panel administratora</p>
                </div>

                <nav class="side__nav" aria-label="Sekcje panelu moderacji">
                    <Link
                        v-for="item in nav"
                        :key="item.path"
                        :href="item.path"
                        class="side__link"
                        :class="{ 'is-active': path === item.path }"
                        :aria-current="path === item.path ? 'page' : undefined"
                    >
                        <span>{{ item.label }}</span>
                        <span v-if="item.badge && pending > 0" class="side__badge">{{ pending }}</span>
                    </Link>
                </nav>
            </aside>

            <div class="content">
                <header v-if="title" class="phead">
                    <h1 class="phead__title">{{ title }}</h1>
                    <div class="phead__actions"><slot name="actions" /></div>
                </header>
                <slot />
            </div>
        </div>
    </main>

    <SiteFooter />
</template>

<style scoped>
.admin {
    max-width: 1180px;
    margin: 0 auto;
    padding: clamp(1.4rem, 4vw, 2.6rem) clamp(1rem, 4vw, 2.4rem);
}
.admin__grid {
    display: grid;
    grid-template-columns: minmax(0, 240px) minmax(0, 1fr);
    gap: clamp(1.4rem, 3vw, 2.6rem);
    align-items: start;
}

/* Sidebar */
.side {
    position: sticky;
    top: 88px;
    display: flex;
    flex-direction: column;
    gap: 1.2rem;
}
.side__card {
    background: color-mix(in srgb, var(--seller-firma) 12%, var(--surface));
    border: 1px solid color-mix(in srgb, var(--seller-firma) 30%, var(--line));
    border-radius: 8px;
    padding: 1.1rem 1.2rem;
}
.side__eyebrow {
    font-family: var(--font-mono);
    font-size: 0.62rem;
    font-weight: 700;
    letter-spacing: 0.18em;
    text-transform: uppercase;
    color: var(--seller-firma);
    margin: 0 0 0.35rem;
}
.side__title {
    font-family: var(--font-display);
    font-weight: 700;
    font-size: 1.1rem;
    margin: 0;
    line-height: 1.1;
}
.side__nav {
    display: flex;
    flex-direction: column;
}
.side__link {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 0.6rem;
    font-size: 0.95rem;
    font-weight: 500;
    color: var(--text-soft);
    text-decoration: none;
    padding: 0.55rem 0.7rem;
    border-left: 2px solid var(--line);
    transition: color 0.15s ease, border-color 0.15s ease;
}
.side__link:hover {
    color: var(--text);
    border-left-color: var(--line-strong);
}
.side__link.is-active {
    color: var(--text);
    font-weight: 600;
    border-left-color: var(--seller-firma);
}
.side__badge {
    font-family: var(--font-mono);
    font-size: 0.68rem;
    font-weight: 700;
    min-width: 20px;
    text-align: center;
    padding: 0.1rem 0.4rem;
    border-radius: 999px;
    background: var(--seller-firma);
    color: #fff;
}

/* Content */
.content {
    min-width: 0;
}
.phead {
    display: flex;
    align-items: center;
    gap: 0.9rem;
    margin-bottom: 1.4rem;
    flex-wrap: wrap;
}
.phead__title {
    font-family: var(--font-display);
    font-weight: 800;
    font-size: clamp(1.6rem, 4vw, 2.4rem);
    letter-spacing: -0.035em;
    line-height: 1;
    margin: 0;
}
.phead__actions {
    display: flex;
    align-items: center;
    gap: 0.6rem;
    margin-left: auto;
}

@media (max-width: 900px) {
    .admin__grid {
        grid-template-columns: 1fr;
    }
    .side {
        position: static;
    }
}
@media (prefers-reduced-motion: reduce) {
    .side__link {
        transition: none;
    }
}
</style>
