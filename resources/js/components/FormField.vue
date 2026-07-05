<script setup>
/**
 * FormField — label + control slot + hint/error. The error replaces the hint so
 * the field never shows both at once.
 */
defineProps({
    label: { type: String, default: null },
    for: { type: String, default: null },
    error: { type: String, default: null },
    hint: { type: String, default: null },
});
</script>

<template>
    <div class="field">
        <label v-if="label" :for="$props.for" class="field__label">{{ label }}</label>
        <slot />
        <p v-if="error" class="field__error">{{ error }}</p>
        <p v-else-if="hint" class="field__hint">{{ hint }}</p>
    </div>
</template>

<style scoped>
.field {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}
.field__label {
    font-family: var(--font-mono);
    font-size: 0.68rem;
    font-weight: 700;
    letter-spacing: 0.14em;
    text-transform: uppercase;
    color: var(--text-soft);
}
.field__hint {
    font-size: 0.8rem;
    color: var(--text-soft);
    margin: 0;
}
.field__error {
    font-size: 0.8rem;
    color: var(--status-hidden);
    margin: 0;
}
</style>
