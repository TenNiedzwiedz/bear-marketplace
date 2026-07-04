<script setup>
/**
 * AppTag — a small mono label for listing attributes (category, "Do negocjacji",
 * "Z dowozem"). `honey` promotes a single tag to a highlighted "Wyróżnione" state.
 */
defineProps({
    variant: {
        type: String,
        default: 'default',
        validator: (v) => ['default', 'honey'].includes(v),
    },
});
</script>

<template>
    <span class="tag" :class="`tag--${variant}`"><slot /></span>
</template>

<style scoped>
.tag {
    font-family: var(--font-mono);
    font-size: 0.72rem;
    letter-spacing: 0.06em;
    border: 1px solid var(--line-strong);
    border-radius: 999px;
    padding: 0.32rem 0.7rem;
    color: var(--text-soft);
    display: inline-flex;
    align-items: center;
    line-height: 1;
}
.tag--honey {
    color: var(--accent-ink);
    background: var(--accent);
    border-color: transparent;
    font-weight: 700;
}
</style>
