<script setup>
import BearSeal from './BearSeal.vue';

/**
 * EmptyState — a branded empty screen. Use the default slot for a call to action.
 */
defineProps({
    title: { type: String, required: true },
    text: { type: String, default: null },
});
</script>

<template>
    <div class="empty">
        <BearSeal :size="64" :ring="false" />
        <p class="empty__title">{{ title }}</p>
        <p v-if="text" class="empty__text">{{ text }}</p>
        <slot />
    </div>
</template>

<style scoped>
.empty {
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
    gap: 0.5rem;
    padding: clamp(2rem, 6vw, 3.6rem) 1rem;
    border: 1px dashed var(--line-strong);
    border-radius: 8px;
}
.empty :deep(svg) {
    color: var(--text-soft);
    opacity: 0.55;
    margin-bottom: 0.4rem;
}
.empty__title {
    font-family: var(--font-display);
    font-weight: 600;
    font-size: 1.2rem;
    margin: 0;
}
.empty__text {
    color: var(--text-soft);
    max-width: 42ch;
    margin: 0 0 0.6rem;
}
</style>
