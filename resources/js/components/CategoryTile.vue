<script setup>
import CategoryGlyph from './CategoryGlyph.vue';

/**
 * CategoryTile — a browse entry point. Icon + name + live count so the visitor
 * sees at a glance where the listings are.
 */
defineProps({
    name: { type: String, required: true },
    icon: { type: String, required: true },
    count: { type: [String, Number], default: null },
    href: { type: String, default: '#' },
});
</script>

<template>
    <a :href="href" class="tile">
        <span class="tile__icon"><CategoryGlyph :name="icon" /></span>
        <span class="tile__text">
            <span class="tile__name">{{ name }}</span>
            <span v-if="count !== null" class="tile__count">{{ count }} ogłoszeń</span>
        </span>
    </a>
</template>

<style scoped>
.tile {
    display: flex;
    align-items: center;
    gap: 0.9rem;
    padding: 1rem 1.1rem;
    background: var(--surface);
    border: 1px solid var(--line);
    border-radius: 6px;
    text-decoration: none;
    color: var(--text);
    transition: transform 0.14s ease, border-color 0.2s ease;
}
.tile:hover {
    transform: translateY(-3px);
    border-color: var(--line-strong);
}
.tile__icon {
    flex: none;
    display: grid;
    place-items: center;
    width: 44px;
    height: 44px;
    border-radius: 5px;
    background: var(--surface-2);
    color: var(--accent-text);
}
.tile__icon svg {
    width: 24px;
    height: 24px;
}
.tile__text {
    display: flex;
    flex-direction: column;
    gap: 0.15rem;
    min-width: 0;
}
.tile__name {
    font-weight: 600;
    font-size: 0.98rem;
    line-height: 1.1;
}
.tile__count {
    font-family: var(--font-mono);
    font-size: 0.68rem;
    letter-spacing: 0.04em;
    color: var(--text-soft);
}
@media (prefers-reduced-motion: reduce) {
    .tile {
        transition: none;
    }
    .tile:hover {
        transform: none;
    }
}
</style>
