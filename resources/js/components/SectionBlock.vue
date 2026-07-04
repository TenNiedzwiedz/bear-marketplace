<script setup>
import EyebrowLabel from './EyebrowLabel.vue';

/**
 * SectionBlock — the standard section header (eyebrow → title → note) plus its
 * body slot. Keeps vertical rhythm consistent across production views.
 */
defineProps({
    eyebrow: { type: String, required: true },
    title: { type: String, default: null },
    note: { type: String, default: null },
});
</script>

<template>
    <section class="block">
        <header class="block__head">
            <EyebrowLabel>{{ eyebrow }}</EyebrowLabel>
            <h2 v-if="title" class="block__title">{{ title }}</h2>
            <p v-if="note" class="block__note">{{ note }}</p>
        </header>
        <slot />
    </section>
</template>

<style scoped>
.block {
    display: flex;
    flex-direction: column;
    gap: 1.6rem;
}
.block__head {
    display: flex;
    flex-direction: column;
    gap: 0.55rem;
}
.block__title {
    font-family: var(--font-display);
    font-weight: 600;
    font-size: clamp(1.5rem, 3.4vw, 2.1rem);
    line-height: 1.05;
    letter-spacing: -0.02em;
    margin: 0.4rem 0 0;
    text-wrap: balance;
}
.block__note {
    color: var(--text-soft);
    max-width: 56ch;
    margin: 0;
}
</style>
