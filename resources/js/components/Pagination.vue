<script setup>
import { Link } from '@inertiajs/vue3';

/**
 * Pagination — renders a Laravel paginator's `links` array. Keeps scroll and
 * filter state across page changes.
 */
defineProps({
    links: { type: Array, default: () => [] },
});

function display(label) {
    if (label.includes('Previous')) return '‹ Poprzednia';
    if (label.includes('Next')) return 'Następna ›';
    return label;
}
</script>

<template>
    <nav v-if="links.length > 3" class="pagination" aria-label="Paginacja">
        <template v-for="(link, i) in links" :key="i">
            <span v-if="!link.url" class="pagination__item is-disabled">{{ display(link.label) }}</span>
            <Link
                v-else
                :href="link.url"
                class="pagination__item"
                :class="{ 'is-active': link.active }"
                :aria-current="link.active ? 'page' : undefined"
                preserve-scroll
                preserve-state
            >{{ display(link.label) }}</Link>
        </template>
    </nav>
</template>

<style scoped>
.pagination {
    display: flex;
    flex-wrap: wrap;
    gap: 0.4rem;
    justify-content: center;
}
.pagination__item {
    font-family: var(--font-mono);
    font-size: 0.85rem;
    min-width: 40px;
    padding: 0.5rem 0.7rem;
    text-align: center;
    border: 1px solid var(--line-strong);
    border-radius: 4px;
    text-decoration: none;
    color: var(--text);
    transition: border-color 0.15s ease, color 0.15s ease;
}
.pagination__item:hover {
    border-color: var(--text);
}
.pagination__item.is-active {
    background: var(--accent);
    color: var(--accent-ink);
    border-color: transparent;
    font-weight: 700;
}
.pagination__item.is-disabled {
    color: var(--text-soft);
    opacity: 0.5;
}
@media (prefers-reduced-motion: reduce) {
    .pagination__item {
        transition: none;
    }
}
</style>
