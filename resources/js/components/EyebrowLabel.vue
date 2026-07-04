<script setup>
/**
 * EyebrowLabel — a mono section marker with a honey tick. Names a section by
 * what it holds; not a sequence, so no numbering.
 */
</script>

<template>
    <p class="eyebrow"><slot /></p>
</template>

<style scoped>
.eyebrow {
    display: inline-flex;
    align-items: center;
    gap: 0.6rem;
    font-family: var(--font-mono);
    font-size: 0.72rem;
    font-weight: 700;
    letter-spacing: 0.22em;
    text-transform: uppercase;
    color: var(--text-soft);
    margin: 0;
}
.eyebrow::before {
    content: '';
    width: 26px;
    height: 2px;
    background: var(--accent);
}
</style>
