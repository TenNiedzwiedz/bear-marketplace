<script setup>
import BearSeal from './BearSeal.vue';

/**
 * SiteFooter — closing navigation and the brand mark. Groups links by what the
 * visitor is trying to do, not by how the app is organised.
 */
const columns = [
    {
        title: 'Przeglądaj',
        links: [
            { label: 'Wszystkie ogłoszenia', href: '#swieze' },
            { label: 'Kategorie', href: '#kategorie' },
            { label: 'Ogłoszenia firm', href: '#swieze' },
        ],
    },
    {
        title: 'Sprzedawaj',
        links: [
            { label: 'Wystaw ogłoszenie', href: '/wystaw' },
            { label: 'Jak to działa', href: '#jak-to-dziala' },
            { label: 'Konto firmowe', href: '/wystaw' },
        ],
    },
    {
        title: 'Serwis',
        links: [
            { label: 'Bezpieczny handel', href: '#' },
            { label: 'Pomoc', href: '#' },
            { label: 'Kontakt', href: '#' },
        ],
    },
];
</script>

<template>
    <footer class="site-footer">
        <div class="site-footer__inner">
            <div class="site-footer__brand">
                <a href="/" class="fbrand" aria-label="Bear Marketplace — strona główna">
                    <BearSeal :size="40" :ring="false" />
                    <span class="fbrand__word">Bear</span>
                </a>
                <p class="site-footer__tag">Wielki targ. Spokojny opiekun.</p>
            </div>

            <nav class="site-footer__cols" aria-label="Stopka">
                <div v-for="col in columns" :key="col.title" class="fcol">
                    <p class="fcol__title">{{ col.title }}</p>
                    <ul>
                        <li v-for="link in col.links" :key="link.label">
                            <a :href="link.href">{{ link.label }}</a>
                        </li>
                    </ul>
                </div>
            </nav>
        </div>

        <div class="site-footer__legal">
            <span>© 2026 Bear Marketplace</span>
            <span>Ogłoszenia w całej Polsce</span>
        </div>
    </footer>
</template>

<style scoped>
.site-footer {
    border-top: 1px solid var(--line);
    margin-top: clamp(3rem, 8vw, 6rem);
}
.site-footer__inner {
    max-width: 1180px;
    margin: 0 auto;
    padding: clamp(2.4rem, 6vw, 4rem) clamp(1rem, 4vw, 2.4rem) 2rem;
    display: grid;
    grid-template-columns: minmax(0, 1.2fr) minmax(0, 2fr);
    gap: clamp(1.6rem, 4vw, 3rem);
}
.fbrand {
    display: inline-flex;
    align-items: center;
    gap: 0.55rem;
    text-decoration: none;
    color: var(--text);
}
.fbrand__word {
    font-family: var(--font-display);
    font-weight: 800;
    font-size: 1.5rem;
    letter-spacing: -0.03em;
}
.site-footer__tag {
    font-family: var(--font-display);
    font-weight: 600;
    color: var(--text-soft);
    margin: 0.9rem 0 0;
    max-width: 24ch;
}
.site-footer__cols {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 1.4rem;
}
.fcol__title {
    font-family: var(--font-mono);
    font-size: 0.68rem;
    font-weight: 700;
    letter-spacing: 0.16em;
    text-transform: uppercase;
    color: var(--text-soft);
    margin: 0 0 0.9rem;
}
.fcol ul {
    list-style: none;
    margin: 0;
    padding: 0;
    display: flex;
    flex-direction: column;
    gap: 0.6rem;
}
.fcol a {
    font-size: 0.92rem;
    text-decoration: none;
    color: var(--text);
    transition: color 0.15s ease;
}
.fcol a:hover {
    color: var(--accent-text);
}
.site-footer__legal {
    border-top: 1px solid var(--line);
    max-width: 1180px;
    margin: 0 auto;
    padding: 1.2rem clamp(1rem, 4vw, 2.4rem);
    display: flex;
    flex-wrap: wrap;
    justify-content: space-between;
    gap: 0.6rem;
    font-family: var(--font-mono);
    font-size: 0.72rem;
    letter-spacing: 0.08em;
    text-transform: uppercase;
    color: var(--text-soft);
}

@media (max-width: 720px) {
    .site-footer__inner {
        grid-template-columns: 1fr;
    }
}
@media (max-width: 480px) {
    .site-footer__cols {
        grid-template-columns: 1fr 1fr;
    }
}
@media (prefers-reduced-motion: reduce) {
    .fcol a {
        transition: none;
    }
}
</style>
