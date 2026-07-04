<script setup>
/**
 * SearchField — the marketplace's front-door input. Two-way bound via v-model.
 */
defineProps({
    modelValue: { type: String, default: '' },
    placeholder: { type: String, default: 'Czego szukasz? — np. rower górski' },
    label: { type: String, default: 'Szukaj ogłoszeń' },
});

defineEmits(['update:modelValue', 'submit']);
</script>

<template>
    <form class="search" role="search" @submit.prevent="$emit('submit')">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
            <circle cx="11" cy="11" r="7" />
            <path d="M21 21l-4.3-4.3" stroke-linecap="round" />
        </svg>
        <input
            type="search"
            :value="modelValue"
            :placeholder="placeholder"
            :aria-label="label"
            @input="$emit('update:modelValue', $event.target.value)"
        />
    </form>
</template>

<style scoped>
.search {
    display: flex;
    align-items: center;
    gap: 0.6rem;
    background: var(--bg);
    border: 1.5px solid var(--line-strong);
    border-radius: 4px;
    padding: 0.7rem 0.9rem;
}
.search:focus-within {
    border-color: var(--accent);
    box-shadow: 0 0 0 3px color-mix(in srgb, var(--accent) 28%, transparent);
}
.search svg {
    flex: none;
    color: var(--text-soft);
}
.search input {
    flex: 1;
    min-width: 0;
    border: 0;
    background: transparent;
    color: var(--text);
    font-family: inherit;
    font-size: 0.95rem;
    outline: none;
}
.search input::placeholder {
    color: var(--text-soft);
}
</style>
