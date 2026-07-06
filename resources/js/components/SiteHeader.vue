<script setup>
import { computed } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import BearSeal from './BearSeal.vue';
import AppButton from './AppButton.vue';

/**
 * SiteHeader — persistent top bar. Keeps the seller track ("Wystaw ogłoszenie")
 * always within reach, and adapts the account controls to the auth state.
 */
const page = usePage();
const user = computed(() => page.props.auth?.user ?? null);
</script>

<template>
    <header class="site-header">
        <div class="site-header__inner">
            <a href="/" class="brand" aria-label="Bear Marketplace — strona główna">
                <BearSeal :size="34" :ring="false" />
                <span class="brand__word">Bear</span>
                <span class="brand__sub">Marketplace</span>
            </a>

            <nav class="site-nav" aria-label="Główna nawigacja">
                <Link href="/ogloszenia">Ogłoszenia</Link>
                <a href="/#kategorie">Kategorie</a>
                <a href="/#jak-to-dziala">Jak to działa</a>
            </nav>

            <div class="site-header__actions">
                <template v-if="user">
                    <Link v-if="user.isAdmin" href="/admin" class="admin-link">Moderacja</Link>
                    <Link href="/panel" class="account">{{ user.name }}</Link>
                    <Link href="/wyloguj" method="post" as="button" type="button" class="logout">Wyloguj</Link>
                </template>
                <template v-else>
                    <Link href="/logowanie" class="login">Zaloguj się</Link>
                </template>
                <AppButton href="/wystaw" variant="primary">Wystaw ogłoszenie</AppButton>
            </div>
        </div>
    </header>
</template>

<style scoped>
.site-header {
    position: sticky;
    top: 0;
    z-index: 50;
    background: color-mix(in srgb, var(--bg) 88%, transparent);
    backdrop-filter: saturate(1.2) blur(10px);
    border-bottom: 1px solid var(--line);
}
.site-header__inner {
    max-width: 1180px;
    margin: 0 auto;
    padding: 0.75rem clamp(1rem, 4vw, 2.4rem);
    display: flex;
    align-items: center;
    gap: 1.4rem;
}
.brand {
    display: inline-flex;
    align-items: baseline;
    gap: 0.5rem;
    text-decoration: none;
    color: var(--text);
}
.brand :deep(svg) {
    align-self: center;
}
.brand__word {
    font-family: var(--font-display);
    font-weight: 800;
    font-size: 1.35rem;
    letter-spacing: -0.03em;
}
.brand__sub {
    font-family: var(--font-mono);
    font-size: 0.62rem;
    font-weight: 700;
    letter-spacing: 0.24em;
    text-transform: uppercase;
    color: var(--text-soft);
}
.site-nav {
    display: flex;
    gap: 1.5rem;
    margin-left: 1rem;
}
.site-nav a {
    font-size: 0.95rem;
    font-weight: 500;
    text-decoration: none;
    color: var(--text-soft);
    transition: color 0.15s ease;
}
.site-nav a:hover {
    color: var(--text);
}
.site-header__actions {
    display: flex;
    align-items: center;
    gap: 1rem;
    margin-left: auto;
}
.login,
.account {
    font-size: 0.95rem;
    font-weight: 600;
    text-decoration: none;
    color: var(--text);
    white-space: nowrap;
}
.account {
    max-width: 16ch;
    overflow: hidden;
    text-overflow: ellipsis;
}
.login:hover,
.account:hover {
    color: var(--accent-text);
}
.logout {
    font-size: 0.9rem;
    font-weight: 600;
    color: var(--text-soft);
    background: none;
    border: 0;
    padding: 0;
    cursor: pointer;
    white-space: nowrap;
    font-family: inherit;
}
.logout:hover {
    color: var(--text);
}
.admin-link {
    font-size: 0.82rem;
    font-weight: 700;
    letter-spacing: 0.04em;
    text-decoration: none;
    color: var(--seller-firma);
    border: 1px solid color-mix(in srgb, var(--seller-firma) 40%, transparent);
    padding: 0.3rem 0.6rem;
    border-radius: 999px;
    white-space: nowrap;
}
.admin-link:hover {
    background: color-mix(in srgb, var(--seller-firma) 12%, transparent);
}

@media (max-width: 860px) {
    .site-nav {
        display: none;
    }
}
@media (max-width: 560px) {
    .brand__sub,
    .login,
    .account {
        display: none;
    }
}
@media (prefers-reduced-motion: reduce) {
    .site-nav a,
    .login {
        transition: none;
    }
}
</style>
