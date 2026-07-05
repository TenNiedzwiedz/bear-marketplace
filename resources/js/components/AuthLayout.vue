<script setup>
import { Link } from '@inertiajs/vue3';
import BearSeal from './BearSeal.vue';

/**
 * AuthLayout — a focused centered card for the login and registration screens.
 */
defineProps({
    title: { type: String, required: true },
    subtitle: { type: String, default: null },
});
</script>

<template>
    <main class="auth">
        <div class="auth__card">
            <Link href="/" class="auth__brand" aria-label="Bear Marketplace — strona główna">
                <BearSeal :size="40" :ring="false" />
                <span class="auth__word">Bear</span>
            </Link>
            <h1 class="auth__title">{{ title }}</h1>
            <p v-if="subtitle" class="auth__sub">{{ subtitle }}</p>
            <slot />
        </div>
    </main>
</template>

<style scoped>
.auth {
    min-height: 100vh;
    display: grid;
    place-items: center;
    padding: clamp(1.4rem, 5vw, 3rem) 1rem;
    background-image: radial-gradient(circle at 1px 1px, var(--line) 1px, transparent 0);
    background-size: 22px 22px;
}
.auth__card {
    width: 100%;
    max-width: 420px;
    background: var(--surface);
    border: 1px solid var(--line);
    border-radius: 10px;
    padding: clamp(1.6rem, 4vw, 2.4rem);
    box-shadow: var(--shadow);
}
.auth__brand {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    text-decoration: none;
    color: var(--text);
    margin-bottom: 1.4rem;
}
.auth__word {
    font-family: var(--font-display);
    font-weight: 800;
    font-size: 1.35rem;
    letter-spacing: -0.03em;
}
.auth__title {
    font-family: var(--font-display);
    font-weight: 800;
    font-size: clamp(1.5rem, 4vw, 2rem);
    letter-spacing: -0.03em;
    line-height: 1.05;
    margin: 0;
    text-wrap: balance;
}
.auth__sub {
    color: var(--text-soft);
    margin: 0.6rem 0 0;
}
</style>
