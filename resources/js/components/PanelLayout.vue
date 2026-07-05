<script setup>
import { computed } from 'vue';
import { usePage, Link } from '@inertiajs/vue3';
import SiteHeader from './SiteHeader.vue';
import SiteFooter from './SiteFooter.vue';
import BearSeal from './BearSeal.vue';
import SellerBadge from './SellerBadge.vue';
import AppButton from './AppButton.vue';

/**
 * PanelLayout — shared chrome for every /panel view: the site header, the user
 * sidebar with section nav, and a content slot. Pass `title` for the standard
 * page heading, or render your own heading in the default slot.
 */
defineProps({
    user: { type: Object, required: true },
    title: { type: String, default: null },
});

const page = usePage();
const path = computed(() => page.url.split('?')[0]);

const nav = [
    { path: '/panel', label: 'Przegląd' },
    { path: '/panel/ogloszenia', label: 'Moje ogłoszenia' },
    { path: '/panel/wiadomosci', label: 'Wiadomości' },
    { path: '/panel/konto', label: 'Konto' },
];
</script>

<template>
    <SiteHeader />

    <main class="panel">
        <div class="panel__grid">
            <aside class="side">
                <div class="side__card">
                    <div class="side__id">
                        <BearSeal :size="46" :ring="false" />
                        <div class="side__idtext">
                            <p class="side__name">{{ user.name }}</p>
                            <SellerBadge :type="user.type" />
                        </div>
                    </div>
                    <p v-if="user.memberSince" class="side__since">Na Bear od {{ user.memberSince }}</p>
                </div>

                <nav class="side__nav" aria-label="Sekcje panelu">
                    <Link
                        v-for="item in nav"
                        :key="item.path"
                        :href="item.path"
                        class="side__link"
                        :class="{ 'is-active': path === item.path }"
                        :aria-current="path === item.path ? 'page' : undefined"
                    >{{ item.label }}</Link>
                </nav>

                <AppButton href="/wystaw" variant="primary" class="side__cta">Wystaw ogłoszenie</AppButton>
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
.panel {
    max-width: 1180px;
    margin: 0 auto;
    padding: clamp(1.4rem, 4vw, 2.6rem) clamp(1rem, 4vw, 2.4rem);
}
.panel__grid {
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
    background: var(--surface);
    border: 1px solid var(--line);
    border-radius: 8px;
    padding: 1.2rem;
}
.side__id {
    display: flex;
    align-items: center;
    gap: 0.8rem;
}
.side__idtext {
    display: flex;
    flex-direction: column;
    gap: 0.35rem;
    align-items: flex-start;
    min-width: 0;
}
.side__name {
    font-family: var(--font-display);
    font-weight: 600;
    font-size: 1.05rem;
    margin: 0;
    line-height: 1.1;
}
.side__since {
    font-family: var(--font-mono);
    font-size: 0.7rem;
    color: var(--text-soft);
    margin: 0.9rem 0 0;
}
.side__nav {
    display: flex;
    flex-direction: column;
}
.side__link {
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
    border-left-color: var(--accent);
}
.side__cta {
    width: 100%;
    justify-content: center;
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
}

@media (max-width: 900px) {
    .panel__grid {
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
