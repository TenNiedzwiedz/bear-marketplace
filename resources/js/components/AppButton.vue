<script setup>
import { computed } from 'vue';

/**
 * AppButton — the marketplace's action vocabulary.
 * primary = the one honey action per view; secondary = outlined; ghost = inline.
 * Pass `href` to render an <a> instead of a <button>.
 */
const props = defineProps({
    variant: {
        type: String,
        default: 'primary',
        validator: (v) => ['primary', 'secondary', 'ghost'].includes(v),
    },
    href: { type: String, default: null },
    type: { type: String, default: 'button' },
});

const tag = computed(() => (props.href ? 'a' : 'button'));
</script>

<template>
    <component
        :is="tag"
        :href="href"
        :type="href ? null : type"
        class="btn"
        :class="`btn--${variant}`"
    >
        <slot />
    </component>
</template>

<style scoped>
.btn {
    font-family: var(--font-sans);
    font-size: 0.95rem;
    font-weight: 600;
    border-radius: 4px;
    padding: 0.72rem 1.15rem;
    border: 1.5px solid transparent;
    cursor: pointer;
    transition: transform 0.12s ease, box-shadow 0.2s ease, background 0.2s ease, border-color 0.2s ease;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    text-decoration: none;
    line-height: 1;
}
.btn--primary {
    background: var(--accent);
    color: var(--accent-ink);
}
.btn--primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 22px -12px var(--accent);
}
.btn--secondary {
    background: transparent;
    color: var(--text);
    border-color: var(--line-strong);
}
.btn--secondary:hover {
    border-color: var(--text);
    transform: translateY(-2px);
}
.btn--ghost {
    background: transparent;
    color: var(--text-soft);
    padding-left: 0.4rem;
    padding-right: 0.4rem;
}
.btn--ghost:hover {
    color: var(--text);
}
@media (prefers-reduced-motion: reduce) {
    .btn {
        transition: none;
    }
    .btn:hover {
        transform: none;
    }
}
</style>
